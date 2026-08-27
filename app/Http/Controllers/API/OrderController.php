<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Delivery;
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
            'vendor_id'                => 'required|exists:vendors,id',
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

            $totalAmount   = 0;
            $itemsToCreate = [];

            foreach ($request->items as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->where('vendor_id', $request->vendor_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Selected product #{$item['product_id']} does not belong to vendor #{$request->vendor_id}.",
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

                $price       = $product->price;
                $subtotal    = $price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $price,
                ];
            }

            $loc = $request->seat_location;

            $order = Order::create([
                'user_id'         => $userId,
                'vendor_id'       => $request->vendor_id,
                'seat_location'   => $loc,
                'seat_type'       => $loc['type'] ?? 'seat',
                'seat_section'    => $loc['section'] ?? null,
                'seat_row'        => $loc['row'] ?? null,
                'seat_number'     => $loc['seat'] ?? null,
                'latitude'        => isset($loc['latitude']) ? floatval($loc['latitude']) : null,
                'longitude'       => isset($loc['longitude']) ? floatval($loc['longitude']) : null,
                'total_amount'    => $totalAmount,
                'payment_status'  => 'pending',
                'order_status'    => 'created',
                'idempotency_key' => $idempotencyKey,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Order created successfully. Please complete payment.',
                'order'   => Order::with('items.product')->find($order->id),
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
     */
    public function pay(Request $request, Order $order, IntaSendService $intaSend)
    {
        $this->authorize('pay', $order);

        $user      = $request->user();
        $phone     = IntaSendService::formatPhone($request->input('phone', $user->phone ?? '0700000000'));
        $nameParts = explode(' ', trim($user->name ?? 'Customer Account'), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? 'User';

        $result = $intaSend->initiateSTKPush($order, $phone, $user->email, $firstName, $lastName);

        if ($result['success'] && isset($result['invoice_id'])) {
            $order->update([
                'intasend_invoice_id' => $result['invoice_id'],
                'intasend_ref'        => $result['api_ref'],
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
     * GET /api/orders/{order}/payment-status
     * Returns payment status and calculates exponential backoff polling interval: 2s -> 3s -> 5s -> 8s -> 15s -> 30s
     */
    public function checkPaymentStatus(Request $request, Order $order, IntaSendService $intaSend)
    {
        $this->authorize('view', $order);

        $backoffSchedule  = [2, 3, 5, 8, 15, 30];
        $attempt          = min(max(1, $request->integer('attempt', 1)), count($backoffSchedule));
        $nextPollInterval = $backoffSchedule[$attempt - 1];

        if ($order->payment_status === 'paid') {
            return response()->json([
                'status'                       => 'success',
                'payment_status'               => 'paid',
                'order_status'                 => $order->order_status,
                'next_poll_interval_seconds'   => 0, // Terminal state reached, stop polling
            ]);
        }

        if (!$order->intasend_invoice_id) {
            return response()->json([
                'status'                       => 'pending',
                'payment_status'               => $order->payment_status,
                'order_status'                 => $order->order_status,
                'next_poll_interval_seconds'   => $nextPollInterval,
            ]);
        }

        $result     = $intaSend->checkPaymentStatus($order->intasend_invoice_id);
        $freshOrder = $order->fresh();

        if ($result['success'] && $result['state'] === 'COMPLETE' && $freshOrder->payment_status === 'paid') {
            event(new \App\Events\PaymentStatusUpdated($freshOrder, 'paid', $freshOrder->order_status));
        }

        return response()->json([
            'status'                       => $result['success'] ? 'success' : 'error',
            'payment_status'               => $freshOrder->payment_status,
            'intasend_state'               => $result['state'],
            'order_status'                 => $freshOrder->order_status,
            'next_poll_interval_seconds'   => $freshOrder->payment_status === 'paid' ? 0 : $nextPollInterval,
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
