<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Events\RunnerLocationUpdated;
use App\Services\GeospatialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RunnerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 25), 100);

        $query = Delivery::query()
            ->where('runner_id', $request->user()->id);

        if (!$request->boolean('all')) {
            $query->whereIn('status', ['pending', 'picked_up']);
        }

        $deliveries = $query->latest()
            ->with(['order.items.product', 'order.vendor:id,business_name,logo_url', 'order.user:id,name,phone'])
            ->cursorPaginate($perPage);

        return response()->json($deliveries);
    }

    // Update runner status (e.g. mark picked up or en route)
    public function updateStatus(Request $request, $id)
    {
        $delivery = Delivery::with('order')->findOrFail($id);
        $this->authorize('update', $delivery);

        $request->validate([
            'status' => 'required|in:picked_up,delivered',
        ]);

        $newStatus  = $request->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'picked_up' && !$delivery->pickup_time) {
            $updateData['pickup_time'] = now();
            $delivery->order->update(['order_status' => 'en_route']);
        } elseif ($newStatus === 'delivered' && !$delivery->delivered_time) {
            $updateData['delivered_time'] = now();
            $delivery->order->update(['order_status' => 'delivered']);
        }

        $delivery->update($updateData);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Delivery status updated to ' . $newStatus,
            'delivery' => Delivery::with(['order.items.product', 'order.vendor', 'order.user'])->find($id)
        ]);
    }

    // Verify and complete delivery via OTP/PIN confirmation
    public function verifyDelivery(Request $request, $id)
    {
        $delivery = Delivery::with('order')->findOrFail($id);
        $this->authorize('update', $delivery);

        $request->validate([
            'pin' => 'required|string',
        ]);

        // 1. Check if PIN has expired
        if ($delivery->pin_expires_at && now()->greaterThan($delivery->pin_expires_at)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Delivery verification PIN has expired. Please contact support.',
            ], 422);
        }

        // 2. Check if maximum attempts limit (5) has been exceeded
        if ($delivery->verification_attempts >= 5) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Maximum verification attempts exceeded. Delivery locked for security.',
            ], 422);
        }

        // 3. Verify PIN against bcrypt hash
        $pinMatch = false;
        if (!empty($delivery->verification_pin_hash)) {
            $pinMatch = Hash::check($request->pin, $delivery->verification_pin_hash);
        } else {
            $pinMatch = ($delivery->verification_pin === $request->pin);
        }

        if (!$pinMatch) {
            $delivery->increment('verification_attempts');
            $remaining = max(0, 5 - $delivery->fresh()->verification_attempts);

            return response()->json([
                'status'  => 'error',
                'message' => "Invalid verification PIN. {$remaining} attempts remaining.",
            ], 422);
        }

        // Successfully verified!
        $delivery->update([
            'status'         => 'delivered',
            'delivered_time' => now(),
        ]);

        $delivery->order->update([
            'order_status' => 'delivered',
        ]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Delivery verified successfully! Order completed.',
            'delivery' => Delivery::with(['order.items.product', 'order.vendor', 'order.user'])->find($id)
        ]);
    }

    // Update runner's real-time coordinates, evaluate Haversine distance in meters, and broadcast live WebSocket updates
    public function updateLocation(Request $request, $id)
    {
        $delivery = Delivery::with('order')->findOrFail($id);
        $this->authorize('update', $delivery);

        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $delivery->update([
            'runner_latitude'  => $request->latitude,
            'runner_longitude' => $request->longitude,
        ]);

        // Calculate distance to customer using normalized lat/lng or seat section coordinates
        $order = $delivery->order;
        $destLat = $order->latitude ? floatval($order->latitude) : null;
        $destLng = $order->longitude ? floatval($order->longitude) : null;

        if (!$destLat || !$destLng) {
            $loc = $order->seat_location;
            if (isset($loc['type']) && $loc['type'] === 'gps' && isset($loc['latitude'])) {
                $destLat = floatval($loc['latitude']);
                $destLng = floatval($loc['longitude']);
            } else {
                $sec = strtolower($order->seat_section ?? ($loc['section'] ?? ''));
                if (str_contains($sec, 'vip') && str_contains($sec, 'a')) {
                    $destLat = -1.2276; $destLng = 36.8967;
                } elseif (str_contains($sec, 'vip') && str_contains($sec, 'b')) {
                    $destLat = -1.2276; $destLng = 36.8979;
                } elseif ((str_contains($sec, 'gen') || str_contains($sec, 'general')) && str_contains($sec, 'a')) {
                    $destLat = -1.2286; $destLng = 36.8967;
                } else {
                    $destLat = -1.2286; $destLng = 36.8979;
                }
            }
        }

        // Spherical Haversine distance calculation in meters
        $distanceMeters = GeospatialService::calculateHaversineDistanceMeters(
            floatval($request->latitude),
            floatval($request->longitude),
            $destLat,
            $destLng
        );

        // Arrival threshold: within 30.0 meters
        $reached = ($distanceMeters <= 30.0);

        if ($reached && !$delivery->arrived_at) {
            $delivery->update([
                'arrived_at' => now(),
            ]);
        }

        // Broadcast live location event to WebSocket channel delivery.{id}
        event(new RunnerLocationUpdated(
            $delivery,
            floatval($request->latitude),
            floatval($request->longitude),
            $distanceMeters,
            $reached
        ));

        return response()->json([
            'status'                               => 'success',
            'delivery'                             => $delivery,
            'distance_meters'                      => $distanceMeters,
            'arrival_threshold_meters'             => 30.0,
            'reached'                              => $reached || ($delivery->arrived_at !== null),
            'recommended_polling_interval_seconds' => $reached ? 15 : 5,
        ]);
    }
}
