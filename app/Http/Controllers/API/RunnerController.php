<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;

class RunnerController extends Controller
{
    // Fetch all active deliveries assigned to a specific runner
    public function index(Request $request)
    {
        $runnerUserId = $request->query('user_id');
        if (!$runnerUserId) {
            return response()->json(['status' => 'error', 'message' => 'Runner User ID is required.'], 400);
        }

        $deliveries = Delivery::with(['order.items.product', 'order.vendor', 'order.user'])
            ->where('runner_id', $runnerUserId)
            ->whereIn('status', ['pending', 'picked_up'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($deliveries);
    }

    // Update runner status (e.g. mark picked up or en route)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:picked_up,en_route',
        ]);

        $delivery = Delivery::with('order')->findOrFail($id);
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'picked_up') {
            $updateData['pickup_time'] = now();
            $delivery->order->update(['order_status' => 'preparing']); // Preparing backup or direct picked up
        }

        if ($newStatus === 'en_route') {
            $delivery->order->update(['order_status' => 'en_route']);
        }

        $delivery->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Delivery status updated to ' . $newStatus,
            'delivery' => Delivery::with(['order.items.product', 'order.vendor', 'order.user'])->find($id)
        ]);
    }

    // Verify and complete delivery via OTP/PIN confirmation
    public function verifyDelivery(Request $request, $id)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $delivery = Delivery::with('order')->findOrFail($id);

        if ($delivery->verification_pin !== $request->pin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid delivery verification PIN. Please verify with the customer!',
            ], 422);
        }

        // Successfully verified!
        $delivery->update([
            'status' => 'delivered',
            'delivered_time' => now(),
        ]);

        $delivery->order->update([
            'order_status' => 'delivered',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Delivery verified successfully! Order completed.',
            'delivery' => Delivery::with(['order.items.product', 'order.vendor', 'order.user'])->find($id)
        ]);
    }
}
