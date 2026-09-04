<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Delivery;
use App\Models\Setting;
use App\Models\LoopPayment;
use App\Services\Loop\InitiateLoopPaybillPayment;
use App\Services\MpesaDarajaService;
use App\Services\IntaSendService;
use App\Services\RunnerAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * POST /api/orders
     * Place a new order on behalf of the authenticated customer.
     * Enforces pessimistic row locking (lockForUpdate), exact stock quantity tracking,
     * vendor product ownership validation, and idempotency key checks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'                => 'nullable|exists:vendors,id',
            'seat_location'            => 'required|array',
            'seat_location.type'       => 'nullable|string|in:seat,gps',
            'seat_location.section'    => 'required_without:seat_location.latitude|string',
            'seat_location.row'        => 'required_without:seat_location.latitude|string',
            'seat_location.seat'       => 'required_without:seat_location.latitude|string',
            'seat_location.latitude'   => 'required_if:seat_location.type,gps|numeric',
            'seat_location.longitude'  => 'required_if:seat_location.type,gps|numeric',
            'seat_location.description'=> 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.quantity'         => 'required|integer|min:1',
        ]);

        $idempotencyKey = $request->header('Idempotency-Key') ?: $request->input('idempotency_key');
        $userId         = $request->user()->id;

        if (!empty($idempotencyKey)) {
            $existingOrder = Order::query()
                ->where('user_id', $userId)
                ->where(function ($q) use ($idempotencyKey) {
                    $q->where('idempotency_key', $idempotencyKey)
                      ->orWhere('idempotency_key', 'LIKE', "{$idempotencyKey}-v%");
                })
                ->first();

            if ($existingOrder) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Order already placed.',
                    'order'   => $existingOrder->load('items.product'),
                ], 200);
            }
        }

        try {
            DB::beginTransaction();

            $itemsByVendor = [];

            foreach ($request->items as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Selected product #{$item['product_id']} not found.",
                    ]);
                }

                if ($product->stock_status === 'out_of_stock' || $product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} has insufficient stock.",
                    ]);
                }

                $product->decrement('stock_quantity', $item['quantity']);
                $product->increment('version');

                if ($product->fresh()->stock_quantity <= 0) {
                    $product->update(['stock_status' => 'out_of_stock']);
                }

                $vId = $product->vendor_id;
                if (!isset($itemsByVendor[$vId])) {
                    $itemsByVendor[$vId] = [];
                }

                $itemsByVendor[$vId][] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ];
            }

            // Calculate Multi-Vendor Delivery Fee:
            // Base delivery fee (e.g. Ksh 30) + 50% extra for each additional unique vendor stall
            $baseDeliveryFee   = floatval(Setting::get('delivery_fee', 30));
            $uniqueVendorCount = count($itemsByVendor);
            $extraFeePerVendor = $baseDeliveryFee * 0.5; // +50% of base fee
            $totalDeliveryFee  = $baseDeliveryFee + max(0, $uniqueVendorCount - 1) * $extraFeePerVendor;

            $loc = $request->seat_location;
            $createdOrders = [];
            $primaryOrder  = null;
            $vIndex        = 0;

            foreach ($itemsByVendor as $vId => $vItems) {
                $vSubtotal = 0;
                foreach ($vItems as $vi) {
                    $vSubtotal += $vi['subtotal'];
                }

                // Primary vendor gets base fee, secondary vendors get their 50% extra fee share
                $vFee = ($vIndex === 0) ? $baseDeliveryFee : $extraFeePerVendor;
                $vTotal = $vSubtotal + $vFee;
                $vIndex++;

                $vendorIdempotencyKey = null;
                if (!empty($idempotencyKey)) {
                    $vendorIdempotencyKey = ($uniqueVendorCount > 1) 
                        ? "{$idempotencyKey}-v{$vId}"
                        : $idempotencyKey;
                }

                $order = Order::create([
                    'user_id'         => $userId,
                    'vendor_id'       => $vId,
                    'seat_location'   => $loc,
                    'seat_type'       => $loc['type'] ?? 'seat',
                    'seat_section'    => $loc['section'] ?? null,
                    'seat_row'        => $loc['row'] ?? null,
                    'seat_number'     => $loc['seat'] ?? null,
                    'latitude'        => isset($loc['latitude']) ? floatval($loc['latitude']) : null,
                    'longitude'       => isset($loc['longitude']) ? floatval($loc['longitude']) : null,
                    'total_amount'    => $vTotal,
                    'payment_status'  => 'pending',
                    'payment_method'  => $request->input('payment_method', 'mpesa'),
                    'order_status'    => 'created',
                    'idempotency_key' => $vendorIdempotencyKey,
                ]);

                foreach ($vItems as $vi) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $vi['product']->id,
                        'quantity'   => $vi['quantity'],
                        'price'      => $vi['price'],
                    ]);
                }

                if (!$primaryOrder) {
                    $primaryOrder = $order;
                }
                $createdOrders[] = $order;
            }

            // Generate 4-digit system Delivery OTP / PIN for client verification
            $otpPin  = (string) random_int(1000, 9999);
            $otpHash = Hash::make($otpPin);

            foreach ($createdOrders as $ord) {
                Delivery::create([
                    'order_id'              => $ord->id,
                    'verification_pin'      => $otpPin,
                    'verification_pin_hash' => $otpHash,
                    'verification_attempts' => 0,
                    'pin_expires_at'        => now()->addHours(2),
                    'status'                => 'pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'status'        => 'success',
                'message'       => 'Order created successfully. Please complete payment.',
                'order'         => $primaryOrder->load('items.product'),
                'orders_count'  => count($createdOrders),
                'delivery_fee'  => $totalDeliveryFee,
                'vendor_count'  => $uniqueVendorCount,
            ]);

        } catch (ValidationException $ve) {
            DB::rollBack();
            throw $ve;
        } catch (\Exception $e) {
            DB::rollBack();
            if (str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == 23000) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'An order with this reference has already been created. Please check your active orders.',
                ], 409);
            }
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/orders/active
     * Fetch active order for customer.
     */
    public function active(Request $request)
    {
        $user  = $request->user();
        $order = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->with(['vendor:id,business_name,logo_url', 'items.product', 'runner:id,name,phone', 'delivery'])
            ->first();

        if (!$order) {
            return response()->json(['status' => 'empty', 'message' => 'No active order found.'], 200);
        }

        return response()->json($order);
    }

    /**
     * GET /api/orders/{order}
     */
    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        return response()->json($order->load(['vendor', 'items.product', 'runner', 'delivery']));
    }

    /**
     * POST /api/orders/{order}/pay
     * Triggers payment STK Push (IntaSend or Safaricom M-Pesa Daraja based on payment_method request/order attribute).
     */
    public function pay(Request $request, Order $order, MpesaDarajaService $mpesaService, IntaSendService $intasendService)
    {
        $this->authorize('pay', $order);

        $method = strtolower((string) ($request->input('payment_method') ?: ($request->input('method') ?: ($request->input('provider') ?: ($order->payment_method ?: '')))));

        if ($method === 'intasend' || !empty($order->intasend_invoice_id)) {
            return $this->payWithIntaSend($request, $order, $intasendService);
        }

        $user   = $request->user();
        $phone  = MpesaDarajaService::formatPhone($request->input('phone', $user->phone ?? '0700000000'));
        $result = $mpesaService->initiateStkPush($order, $phone);

        if ($result['success'] && isset($result['checkout_request_id'])) {
            $order->update([
                'payment_method'            => 'mpesa',
                'mpesa_checkout_request_id' => $result['checkout_request_id'],
                'mpesa_merchant_request_id' => $result['merchant_request_id'] ?? null,
            ]);
        }

        return response()->json([
            'status'              => $result['success'] ? 'success' : 'error',
            'message'             => $result['message'],
            'checkout_request_id' => $result['checkout_request_id'] ?? null,
            'order_id'            => $order->id,
        ]);
    }

    /**
     * POST /api/orders/{order}/pay/intasend
     * Triggers an M-Pesa STK Push prompt via IntaSend payment gateway.
     */
    public function payWithIntaSend(Request $request, Order $order, IntaSendService $intasendService)
    {
        $this->authorize('pay', $order);

        $user      = $request->user();
        $phone     = IntaSendService::formatPhone($request->input('phone', $user->phone ?? '0700000000'));
        $email     = $user->email ?? 'customer@glovopro.com';
        $firstName = explode(' ', trim($user->name ?? 'Customer'))[0] ?? 'Customer';
        $lastName  = explode(' ', trim($user->name ?? 'User'))[1] ?? 'Order';

        $result = $intasendService->initiateSTKPush($order, $phone, $email, $firstName, $lastName);

        if ($result['success'] && !empty($result['invoice_id'])) {
            $order->update([
                'payment_method'      => 'intasend',
                'intasend_invoice_id' => $result['invoice_id'],
                'intasend_ref'        => $result['api_ref'] ?? ('order-' . $order->id),
            ]);
        }

        return response()->json([
            'status'     => $result['success'] ? 'success' : 'error',
            'message'    => $result['message'],
            'invoice_id' => $result['invoice_id'] ?? null,
            'order_id'   => $order->id,
        ]);
    }

    /**
     * GET /api/orders/{order}/intasend-status
     * Returns IntaSend payment status for order with backoff interval.
     */
    public function checkIntaSendStatus(Request $request, Order $order, IntaSendService $intasendService)
    {
        $this->authorize('view', $order);

        $backoffSchedule  = [2, 3, 5, 8, 15, 30];
        $attempt          = min(max(1, $request->integer('attempt', 1)), count($backoffSchedule));
        $nextPollInterval = $backoffSchedule[$attempt - 1];

        $freshOrder = $order->fresh();
        if (in_array($freshOrder->payment_status, ['paid', 'failed'], true)) {
            return response()->json([
                'status'                     => 'success',
                'payment_status'             => $freshOrder->payment_status,
                'order_status'               => $freshOrder->order_status,
                'next_poll_interval_seconds' => 0,
            ]);
        }

        if (!$freshOrder->intasend_invoice_id) {
            return response()->json([
                'status'                     => 'pending',
                'payment_status'             => $freshOrder->payment_status,
                'order_status'               => $freshOrder->order_status,
                'next_poll_interval_seconds' => $nextPollInterval,
            ]);
        }

        $result = $intasendService->checkPaymentStatus($freshOrder->intasend_invoice_id);
        $state  = strtoupper((string) ($result['state'] ?? ''));

        if ($result['success'] && $state === 'COMPLETE' && $freshOrder->payment_status === 'pending') {
            $freshOrder->update([
                'payment_status'    => 'paid',
                'order_status'      => 'accepted',
                'paid_at'           => now(),
                'payment_method'    => 'intasend',
                'payment_reference' => $freshOrder->intasend_invoice_id,
            ]);
            $freshOrder = $freshOrder->fresh();
            event(new \App\Events\PaymentStatusUpdated($freshOrder, 'paid', $freshOrder->order_status));
        } elseif ($result['success'] && $state === 'FAILED' && $freshOrder->payment_status === 'pending') {
            $freshOrder->update([
                'payment_status' => 'failed',
            ]);
            $freshOrder = $freshOrder->fresh();
        }

        $isTerminal = in_array($freshOrder->payment_status, ['paid', 'failed'], true);

        return response()->json([
            'status'                     => $result['success'] ? 'success' : 'error',
            'payment_status'             => $freshOrder->payment_status,
            'intasend_state'             => $state,
            'order_status'               => $freshOrder->order_status,
            'next_poll_interval_seconds' => $isTerminal ? 0 : $nextPollInterval,
        ]);
    }

    /**
     * POST /api/orders/{order}/pay/loop
     * Generates LOOP Paybill customer payment intent with instructions and unique account reference.
     */
    public function payWithLoop(Request $request, Order $order, InitiateLoopPaybillPayment $initiator)
    {
        $this->authorize('pay', $order);

        $payment = $initiator->handle($order, $request->all(), $request->user());

        return response()->json([
            'status'             => 'success',
            'message'            => 'LOOP Paybill payment instructions generated.',
            'public_id'          => $payment->public_id,
            'merchant_reference' => $payment->merchant_reference,
            'paybill_number'     => $payment->paybill_number,
            'account_reference'  => $payment->account_reference,
            'amount'             => number_format((float) $payment->amount, 2, '.', ''),
            'currency'           => $payment->currency,
            'payment_status'     => $payment->status,
            'expires_at'         => $payment->expires_at?->toIso8601String(),
            'instructions'       => [
                'Open M-Pesa on your phone',
                'Select Lipa na M-Pesa -> Pay Bill',
                'Enter Business Number: ' . $payment->paybill_number,
                'Enter Account Number: ' . $payment->account_reference,
                'Enter Exact Amount: KES ' . number_format((float) $payment->amount, 2),
                'Enter your M-Pesa PIN and complete payment',
                'Return here and tap "I Have Paid"',
            ],
            'order_id'           => $order->id,
        ]);
    }

    /**
     * POST /api/orders/{order}/pay/loop/claim
     * Customer claims they have completed the M-Pesa Paybill payment.
     */
    public function claimLoopPaybill(Request $request, Order $order)
    {
        $this->authorize('pay', $order);

        $request->validate([
            'mpesa_receipt' => ['required', 'string', 'regex:/^[A-Za-z0-9]{6,25}$/'],
        ]);

        $payment = $order->loopPayment;

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'No active LOOP payment intent found for this order.'], 404);
        }

        if ($payment->status === LoopPayment::STATUS_SUCCESSFUL || $order->payment_status === 'paid') {
            return response()->json(['status' => 'success', 'message' => 'Payment already confirmed!']);
        }

        $payment->update([
            'submitted_receipt'   => strtoupper($request->input('mpesa_receipt')),
            'status'              => LoopPayment::STATUS_CLAIMED,
            'customer_claimed_at' => now(),
        ]);

        return response()->json([
            'status'         => 'success',
            'message'        => 'Payment claim received. Verification in progress...',
            'payment_status' => 'verifying',
            'receipt'        => $payment->submitted_receipt,
            'order_id'       => $order->id,
        ]);
    }

    /**
     * GET /api/orders/{order}/loop-status
     * Returns LOOP payment status for order with backoff interval.
     */
    public function checkLoopStatus(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $backoffSchedule  = [2, 3, 5, 8, 15, 30];
        $attempt          = min(max(1, $request->integer('attempt', 1)), count($backoffSchedule));
        $nextPollInterval = $backoffSchedule[$attempt - 1];

        $payment    = $order->loopPayment;
        $freshOrder = $order->fresh();
        $isTerminal = in_array($freshOrder->payment_status, ['paid', 'failed'], true);

        return response()->json([
            'status'                     => 'success',
            'payment_status'             => $freshOrder->payment_status,
            'order_status'               => $freshOrder->order_status,
            'loop_status'                => $payment?->status,
            'account_reference'          => $payment?->account_reference,
            'submitted_receipt'          => $payment?->submitted_receipt,
            'provider_receipt'           => $payment?->provider_receipt,
            'provider_message'           => $payment?->provider_message,
            'next_poll_interval_seconds' => $isTerminal ? 0 : $nextPollInterval,
        ]);
    }

    /**
     * GET /api/orders/{order}/payment-status
     * Returns payment status (IntaSend or Safaricom M-Pesa Daraja) with exponential backoff polling interval.
     */
    public function checkPaymentStatus(Request $request, Order $order, MpesaDarajaService $mpesaService, IntaSendService $intasendService)
    {
        $this->authorize('view', $order);

        if (!empty($order->intasend_invoice_id) || strtolower((string) $order->payment_method) === 'intasend') {
            return $this->checkIntaSendStatus($request, $order, $intasendService);
        }

        $backoffSchedule  = [2, 3, 5, 8, 15, 30];
        $attempt          = min(max(1, $request->integer('attempt', 1)), count($backoffSchedule));
        $nextPollInterval = $backoffSchedule[$attempt - 1];

        if (in_array($order->payment_status, ['paid', 'failed'], true)) {
            return response()->json([
                'status'                     => 'success',
                'payment_status'             => $order->payment_status,
                'order_status'               => $order->order_status,
                'next_poll_interval_seconds' => 0, // Terminal state reached
            ]);
        }

        if (!$order->mpesa_checkout_request_id) {
            return response()->json([
                'status'                     => 'pending',
                'payment_status'             => $order->payment_status,
                'order_status'               => $order->order_status,
                'next_poll_interval_seconds' => $nextPollInterval,
            ]);
        }

        $result     = $mpesaService->queryStkPushStatus($order->mpesa_checkout_request_id);
        $freshOrder = $order->fresh();

        $resCode = (string) ($result['result_code'] ?? '');
        $resDesc = strtolower((string) ($result['result_desc'] ?? ''));
        $isStillProcessing = in_array($resCode, ['4999', '1037', '500.001.1001'], true) 
            || str_contains($resDesc, 'processing') 
            || str_contains($resDesc, 'in progress')
            || str_contains($resDesc, 'pending');

        if ($result['success'] && $resCode === '0' && $freshOrder->payment_status === 'pending') {
            $freshOrder->update([
                'payment_status'    => 'paid',
                'order_status'      => 'accepted',
                'paid_at'           => now(),
                'payment_reference' => $order->mpesa_checkout_request_id,
            ]);
            $freshOrder = $freshOrder->fresh();
            event(new \App\Events\PaymentStatusUpdated($freshOrder, 'paid', $freshOrder->order_status));
        } elseif ($result['success'] && !empty($resCode) && $resCode !== '0' && !$isStillProcessing && $freshOrder->payment_status === 'pending') {
            $freshOrder->update([
                'payment_status' => 'failed',
            ]);
            $freshOrder = $freshOrder->fresh();
        }

        $isTerminal = in_array($freshOrder->payment_status, ['paid', 'failed'], true);

        return response()->json([
            'status'                     => $result['success'] ? 'success' : 'error',
            'payment_status'             => $freshOrder->payment_status,
            'mpesa_result_code'          => $result['result_code'],
            'mpesa_result_desc'          => $result['result_desc'],
            'order_status'               => $freshOrder->order_status,
            'next_poll_interval_seconds' => $isTerminal ? 0 : $nextPollInterval,
        ]);
    }

    /**
     * GET /api/vendor/orders
     * Return all paid orders scoped to the authenticated vendor account.
     */
    public function vendorOrders(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor profile not found.'], 404);
        }

        $perPage = min($request->integer('per_page', 25), 100);

        $orders = Order::query()
            ->where('vendor_id', $vendor->id)
            ->whereIn('payment_status', ['paid'])
            ->latest()
            ->with(['items.product', 'user:id,name,email,phone', 'delivery'])
            ->cursorPaginate($perPage);

        return response()->json($orders);
    }

    /**
     * PATCH /api/vendor/orders/{order}/status
     * Vendor updates order to 'preparing' or 'ready'.
     * Allocates runner fairly using RunnerAllocationService and generates 6-digit secure delivery PIN.
     */
    public function updateStatus(Request $request, Order $order, RunnerAllocationService $allocationService)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor profile not found.'], 404);
        }

        // Strict query ownership check + Policy authorization
        $order = Order::query()
            ->where('vendor_id', $vendor->id)
            ->findOrFail($order->id);

        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|in:preparing,ready',
        ]);

        $newStatus  = $request->status;
        $updateData = ['order_status' => $newStatus];

        if ($newStatus === 'ready') {
            $runner = $allocationService->allocateRunnerForOrder($order);
            if ($runner) {
                $updateData['runner_id']    = $runner->id;
                $updateData['order_status'] = 'runner_assigned';

                // Cryptographically generate 6-digit delivery PIN
                $pin     = (string) random_int(100000, 999999);
                $pinHash = Hash::make($pin);

                Delivery::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'runner_id'             => $runner->id,
                        'verification_pin'      => $pin,
                        'verification_pin_hash' => $pinHash,
                        'verification_attempts' => 0,
                        'pin_expires_at'        => now()->addMinutes(60),
                        'status'                => 'pending',
                    ]
                );
            }
        }

        $order->update($updateData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Order status updated to ' . $order->order_status,
            'order'   => Order::with(['items.product', 'runner', 'delivery'])->find($order->id),
        ]);
    }
}
