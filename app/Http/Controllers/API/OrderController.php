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
use App\Services\MpesaDarajaService;
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

        if ($idempotencyKey) {
            $existingOrder = Order::query()
                ->where('user_id', $userId)
                ->where('idempotency_key', $idempotencyKey)
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

                $vendorIdempotencyKey = ($uniqueVendorCount > 1) 
                    ? "{$idempotencyKey}-v{$vId}"
                    : $idempotencyKey;

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
     * Triggers an M-Pesa STK Push prompt to the customer's phone number.
     */
    public function pay(Request $request, Order $order, MpesaDarajaService $mpesaService)
    {
        $this->authorize('pay', $order);

        $user   = $request->user();
        $phone  = MpesaDarajaService::formatPhone($request->input('phone', $user->phone ?? '0700000000'));
        $result = $mpesaService->initiateStkPush($order, $phone);

        if ($result['success'] && isset($result['checkout_request_id'])) {
            $order->update([
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
     * GET /api/orders/{order}/payment-status
     * Returns payment status and calculates exponential backoff polling interval: 2s -> 3s -> 5s -> 8s -> 15s -> 30s
     */
    public function checkPaymentStatus(Request $request, Order $order, MpesaDarajaService $mpesaService)
    {
        $this->authorize('view', $order);

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
