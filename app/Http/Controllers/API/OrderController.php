<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Place a new order
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,id',
            'seat_location' => 'required|array',
            'seat_location.section' => 'required|string',
            'seat_location.row' => 'required|string',
            'seat_location.seat' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock_status !== 'in_stock') {
                    throw new \Exception("Item '{$product->name}' is currently out of stock!");
                }

                $price = $product->price;
                $subtotal = $price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ];
            }

            // Create Order
            $order = Order::create([
                'user_id' => $request->user_id,
                'vendor_id' => $request->vendor_id,
                'seat_location' => $request->seat_location,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'order_status' => 'created',
            ]);

            // Create Order Items
            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully. Please complete payment.',
                'order' => Order::with('items.product')->find($order->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Get order status
    public function show($id)
    {
        $order = Order::with(['items.product', 'vendor', 'runner', 'delivery'])->findOrFail($id);
        return response()->json($order);
    }

    // Get active order for customer
    public function active(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'User ID is required.'], 400);
        }

        $order = Order::with(['items.product', 'vendor', 'runner', 'delivery'])
            ->where('user_id', $userId)
            ->whereIn('order_status', ['created', 'accepted', 'preparing', 'ready', 'runner_assigned', 'en_route'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) {
            return response()->json(['status' => 'success', 'order' => null]);
        }

        return response()->json($order);
    }

    // Simulate M-Pesa STK Push Payment
    public function pay(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'status' => 'success',
                'message' => 'Order has already been paid!',
                'order' => $order
            ]);
        }

        $phone = $request->input('phone', '0712345678');

        // Transition status directly to simulate callback success
        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'accepted' // Moves to Vendor queue!
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'STK Push sent successfully to ' . $phone . '. Transaction verified!',
            'order' => Order::with('items.product')->find($id)
        ]);
    }

    // Get orders for specific Vendor
    public function vendorOrders(Request $request)
    {
        $vendorUserId = $request->query('user_id');
        if (!$vendorUserId) {
            return response()->json(['status' => 'error', 'message' => 'Vendor User ID is required.'], 400);
        }

        $vendor = Vendor::where('user_id', $vendorUserId)->first();
        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor not found.'], 404);
        }

        $orders = Order::with(['items.product', 'user', 'delivery'])
            ->where('vendor_id', $vendor->id)
            ->whereIn('payment_status', ['paid']) // Only show paid orders to vendors
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // Update order status (Vendor triggers preparing and ready)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:preparing,ready',
        ]);

        $order = Order::findOrFail($id);
        $newStatus = $request->status;

        $updateData = ['order_status' => $newStatus];

        if ($newStatus === 'ready') {
            // Assign a random runner who is seeded
            $runner = User::where('role', 'runner')->inRandomOrder()->first();
            if ($runner) {
                $updateData['runner_id'] = $runner->id;
                $updateData['order_status'] = 'runner_assigned';

                // Create a Delivery record with a secret 4-digit PIN for security confirmation
                $pin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                Delivery::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'runner_id' => $runner->id,
                        'verification_pin' => $pin,
                        'status' => 'pending'
                    ]
                );
            }
        }

        $order->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated to ' . $order->order_status,
            'order' => Order::with(['items.product', 'runner', 'delivery'])->find($id)
        ]);
    }
}
