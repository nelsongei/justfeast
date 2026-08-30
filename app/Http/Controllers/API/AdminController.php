<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function stats()
    {
        // 1. Total Paid Revenue & Count (Single SQL Aggregate Query)
        $orderStats = Order::query()
            ->where('payment_status', 'paid')
            ->selectRaw('COALESCE(SUM(total_amount), 0) AS total_revenue, COUNT(*) AS orders_count')
            ->first();

        $totalRevenue = floatval($orderStats->total_revenue ?? 0);
        $ordersCount  = (int) ($orderStats->orders_count ?? 0);

        // 2. Average delivery time in minutes (Calculated directly in DB, no fake 8.4 fallback)
        $avgDeliveryTime = Delivery::query()
            ->where('status', 'delivered')
            ->whereNotNull('pickup_time')
            ->whereNotNull('delivered_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, pickup_time, delivered_time)) / 60 AS average')
            ->value('average');

        $avgDeliveryTime = $avgDeliveryTime !== null ? round(floatval($avgDeliveryTime), 1) : null;

        // 3. Revenue by Vendor (Single SQL LEFT JOIN Aggregate Query - No N+1 Loop)
        $vendorRevenue = Vendor::query()
            ->leftJoin('orders', function ($join) {
                $join->on('orders.vendor_id', '=', 'vendors.id')
                    ->where('orders.payment_status', '=', 'paid');
            })
            ->select([
                'vendors.id',
                'vendors.business_name',
                'vendors.logo_url',
            ])
            ->selectRaw('COALESCE(SUM(orders.total_amount), 0) AS revenue')
            ->selectRaw('COUNT(orders.id) AS orders_count')
            ->groupBy('vendors.id', 'vendors.business_name', 'vendors.logo_url')
            ->get()
            ->map(function ($row) {
                return [
                    'id'            => $row->id,
                    'business_name' => $row->business_name,
                    'logo_url'      => $row->logo_url,
                    'revenue'       => floatval($row->revenue),
                    'orders_count'  => (int) $row->orders_count,
                ];
            });

        // 4. Section Heatmap Metrics (Single SQL GROUP BY on indexed seat_section column)
        $heatmapRaw = Order::query()
            ->where('payment_status', 'paid')
            ->whereNotNull('seat_section')
            ->select('seat_section', DB::raw('COUNT(*) as total_orders'))
            ->groupBy('seat_section')
            ->get();

        $sectionHeatmap = [
            'vip_a' => 0,
            'vip_b' => 0,
            'gen_a' => 0,
            'gen_b' => 0,
        ];

        foreach ($heatmapRaw as $row) {
            $sec = strtolower($row->seat_section);
            if (str_contains($sec, 'vip') && str_contains($sec, 'a')) {
                $sectionHeatmap['vip_a'] += (int) $row->total_orders;
            } elseif (str_contains($sec, 'vip') && str_contains($sec, 'b')) {
                $sectionHeatmap['vip_b'] += (int) $row->total_orders;
            } elseif ((str_contains($sec, 'gen') || str_contains($sec, 'general')) && str_contains($sec, 'a')) {
                $sectionHeatmap['gen_a'] += (int) $row->total_orders;
            } elseif ((str_contains($sec, 'gen') || str_contains($sec, 'general')) && str_contains($sec, 'b')) {
                $sectionHeatmap['gen_b'] += (int) $row->total_orders;
            }
        }

        // 5. Recent global orders (Selective relation columns, bounded 10 items)
        $recentOrders = Order::query()
            ->with(['vendor:id,business_name,logo_url', 'user:id,name,email'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'total_revenue'          => $totalRevenue,
            'orders_count'           => $ordersCount,
            'avg_delivery_time_mins' => $avgDeliveryTime,
            'vendor_revenue'         => $vendorRevenue,
            'section_heatmap'        => $sectionHeatmap,
            'recent_orders'          => $recentOrders,
        ]);
    }

    public function orders(Request $request)
    {
        $perPage = min($request->integer('per_page', 25), 100);

        $orders = Order::query()
            ->with(['vendor:id,business_name,logo_url', 'user:id,name,email,phone', 'delivery.runner:id,name'])
            ->when($request->filled('status'), fn($q) => $q->where('order_status', $request->status))
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->payment_status))
            ->latest()
            ->cursorPaginate($perPage);

        return response()->json($orders);
    }

    public function users(Request $request)
    {
        $perPage = min($request->integer('per_page', 25), 100);
        $search  = trim($request->input('search', ''));
        $role    = strtolower(trim($request->input('role', '')));

        // 1. Fast single SQL aggregate query for system-wide KPIs (Scales to 100k+ users)
        $kpis = User::query()
            ->selectRaw("COUNT(*) AS total_count")
            ->selectRaw("COUNT(CASE WHEN role IN ('customer', 'client') THEN 1 END) AS customer_count")
            ->selectRaw("COUNT(CASE WHEN role = 'vendor' THEN 1 END) AS vendor_count")
            ->selectRaw("COUNT(CASE WHEN role = 'runner' THEN 1 END) AS runner_count")
            ->first();

        // 2. Server-side SQL filtered paginated user list
        $users = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'created_at'])
            ->when(!empty($role), function ($q) use ($role) {
                if ($role === 'customer' || $role === 'client') {
                    $q->whereIn('role', ['customer', 'client']);
                } else {
                    $q->where('role', $role);
                }
            })
            ->when(!empty($search), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'kpis'   => [
                'total'     => (int) ($kpis->total_count ?? 0),
                'customers' => (int) ($kpis->customer_count ?? 0),
                'vendors'   => (int) ($kpis->vendor_count ?? 0),
                'runners'   => (int) ($kpis->runner_count ?? 0),
            ],
            'users'  => $users,
        ]);
    }

    public function reports(Request $request)
    {
        // 1. Core indicators (Single SQL aggregate query)
        $orderStats = Order::query()
            ->where('payment_status', 'paid')
            ->selectRaw('COALESCE(SUM(total_amount), 0) AS total_revenue, COUNT(*) AS orders_count')
            ->first();

        $totalRevenue  = floatval($orderStats->total_revenue ?? 0);
        $ordersCount   = (int) ($orderStats->orders_count ?? 0);
        $avgOrderValue = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0;

        // 2. eTIMS tax (16% VAT)
        $etimsTax = round($totalRevenue * 0.16, 2);

        // 3. Sales by Vendor (Single SQL LEFT JOIN Aggregate Query - No N+1 Loop)
        $salesByVendor = Vendor::query()
            ->leftJoin('orders', function ($join) {
                $join->on('orders.vendor_id', '=', 'vendors.id')
                    ->where('orders.payment_status', '=', 'paid');
            })
            ->select([
                'vendors.id',
                'vendors.business_name',
                'vendors.logo_url',
            ])
            ->selectRaw('COALESCE(SUM(orders.total_amount), 0) AS revenue')
            ->selectRaw('COUNT(orders.id) AS orders_count')
            ->groupBy('vendors.id', 'vendors.business_name', 'vendors.logo_url')
            ->get()
            ->map(function ($row) {
                return [
                    'business_name' => $row->business_name,
                    'logo_url'      => $row->logo_url,
                    'orders_count'  => (int) $row->orders_count,
                    'revenue'       => floatval($row->revenue),
                ];
            });

        // 4. Runner Performance (Single SQL LEFT JOIN Aggregate Query - No N+1 Loop)
        $runnersPerformance = User::query()
            ->where('users.role', 'runner')
            ->leftJoin('deliveries', 'deliveries.runner_id', '=', 'users.id')
            ->select(['users.id', 'users.name', 'users.email'])
            ->selectRaw("COUNT(CASE WHEN deliveries.status = 'delivered' THEN 1 END) AS completed_deliveries")
            ->selectRaw("COUNT(CASE WHEN deliveries.status != 'delivered' AND deliveries.id IS NOT NULL THEN 1 END) AS active_tasks")
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get()
            ->map(function ($row) {
                return [
                    'name'                 => $row->name,
                    'email'                => $row->email,
                    'completed_deliveries' => (int) $row->completed_deliveries,
                    'active_tasks'         => (int) $row->active_tasks,
                ];
            });

        // 5. Payment Distribution (Single SQL Aggregate Query - No multiple counts)
        $paymentDist = Order::query()
            ->selectRaw("COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) AS paid")
            ->selectRaw("COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) AS failed")
            ->first();

        return response()->json([
            'total_revenue'        => $totalRevenue,
            'orders_count'         => $ordersCount,
            'avg_order_value'      => $avgOrderValue,
            'etims_tax'            => $etimsTax,
            'sales_by_vendor'      => $salesByVendor,
            'runners_performance'  => $runnersPerformance,
            'payment_distribution' => [
                'paid'    => (int) ($paymentDist->paid ?? 0),
                'pending' => (int) ($paymentDist->pending ?? 0),
                'failed'  => (int) ($paymentDist->failed ?? 0),
            ],
        ]);
    }

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:admin,vendor,runner,client',
            'business_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20|unique:users,phone',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        $vendor = null;
        if ($data['role'] === 'vendor') {
            $businessName = !empty($data['business_name']) ? $data['business_name'] : $data['name'] . "'s Stall";
            $eventId = $this->getOrCreateActiveEventId();
            $vendor = Vendor::create([
                'user_id'       => $user->id,
                'business_name' => $businessName,
                'event_id'      => $eventId,
                'status'        => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User account created successfully!',
            'user'    => $user,
            'vendor'  => $vendor,
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'role'     => 'required|string|in:admin,vendor,runner,client,customer',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role  = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (($validated['role'] === 'vendor') && !$user->vendor) {
            $eventId = $this->getOrCreateActiveEventId();
            Vendor::create([
                'user_id'       => $user->id,
                'business_name' => $user->name . "'s Stall",
                'event_id'      => $eventId,
                'status'        => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "User account '{$user->name}' updated successfully!",
            'user'    => $user,
        ]);
    }

    public function destroyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user() && $request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own active administrator account.',
            ], 400);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User account '{$userName}' deleted successfully.",
        ]);
    }

    public function events()
    {
        $events = Event::select(['id', 'name', 'status', 'start_time', 'end_time'])
            ->orderByDesc('id')
            ->get();

        return response()->json($events);
    }

    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20|unique:users,phone',
            'password'      => 'required|string|min:6',
            'event_id'      => 'nullable|exists:events,id',
            'status'        => 'nullable|in:active,inactive',
            'logo'          => 'nullable|image|max:2048',
            'logo_url'      => 'nullable|string|max:255',
        ]);

        $logoUrl = $validated['logo_url'] ?? null;
        if ($request->hasFile('logo')) {
            $file     = $request->file('logo');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/uploads'), $filename);
            $logoUrl  = '/images/uploads/' . $filename;
        }

        $eventId = $this->getOrCreateActiveEventId($validated['event_id'] ?? null);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'] ?? null,
                'role'     => 'vendor',
                'password' => Hash::make($validated['password']),
            ]);

            $vendor = Vendor::create([
                'user_id'       => $user->id,
                'business_name' => $validated['business_name'],
                'event_id'      => $eventId,
                'status'        => $validated['status'] ?? 'active',
                'logo_url'      => $logoUrl,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vendor account '{$vendor->business_name}' registered successfully!",
                'vendor'  => $vendor->load(['user:id,name,email,phone', 'event:id,name']),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to register vendor: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getOrCreateActiveEventId(?int $eventId = null): int
    {
        if ($eventId && Event::where('id', $eventId)->exists()) {
            return (int) $eventId;
        }

        $activeId = Event::where('status', 'active')->value('id');
        if ($activeId) {
            return (int) $activeId;
        }

        $firstId = Event::first()?->id;
        if ($firstId) {
            return (int) $firstId;
        }

        $venue = Venue::firstOrCreate(
            ['name' => 'Main Venue'],
            [
                'map_data'       => ['coordinates' => '0,0'],
                'seating_layout' => ['sections' => []],
            ]
        );

        $event = Event::create([
            'name'       => 'Main Concert Event',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addDays(30),
            'status'     => 'active',
        ]);

        return (int) $event->id;
    }

    public function vendors(Request $request)
    {
        $vendors = Vendor::query()
            ->with(['user:id,name,email,phone', 'event:id,name', 'products:id,vendor_id,name,description,price,stock_status'])
            ->leftJoin('orders', function ($join) {
                $join->on('orders.vendor_id', '=', 'vendors.id')
                    ->where('orders.payment_status', '=', 'paid');
            })
            ->select([
                'vendors.id',
                'vendors.user_id',
                'vendors.event_id',
                'vendors.business_name',
                'vendors.logo_url',
                'vendors.status',
            ])
            ->selectRaw('COALESCE(SUM(orders.total_amount), 0) AS total_revenue')
            ->selectRaw('COUNT(orders.id) AS orders_count')
            ->groupBy('vendors.id', 'vendors.user_id', 'vendors.event_id', 'vendors.business_name', 'vendors.logo_url', 'vendors.status')
            ->get()
            ->map(function ($vendor) {
                $logoHtml = $vendor->logo_url 
                    ? (str_starts_with($vendor->logo_url, 'http') || str_starts_with($vendor->logo_url, '/') 
                        ? '<img src="' . e($vendor->logo_url) . '" alt="Logo" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">' 
                        : e($vendor->logo_url)) 
                    : '<i class="fas fa-store"></i>';

                return [
                    'id'            => $vendor->id,
                    'user_id'       => $vendor->user_id,
                    'event_id'      => $vendor->event_id,
                    'business_name' => $vendor->business_name,
                    'logo_url'      => $logoHtml,
                    'raw_logo'      => $vendor->logo_url,
                    'status'        => $vendor->status,
                    'total_revenue' => floatval($vendor->total_revenue),
                    'orders_count'  => (int) $vendor->orders_count,
                    'user'          => $vendor->user,
                    'event'         => $vendor->event,
                    'products'      => $vendor->products,
                ];
            });

        return response()->json($vendors);
    }

    public function updateVendorStatus(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $vendor->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => "Vendor account '{$vendor->business_name}' is now {$vendor->status}.",
            'vendor'  => $vendor->fresh(['user:id,name,email', 'event:id,name']),
        ]);
    }

    public function storeProduct(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'stock_status' => 'nullable|string|in:in_stock,out_of_stock',
            'image'        => 'nullable|image|max:4096',
            'image_url'    => 'nullable|string',
        ]);

        $imageUrl = 'bg-gradient-to-br from-amber-400 to-red-500';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/uploads'), $filename);
            $imageUrl = '/images/uploads/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imageUrl = $validated['image_url'];
        }

        $product = Product::create([
            'vendor_id'    => $vendor->id,
            'name'         => $validated['name'],
            'price'        => $validated['price'],
            'description'  => $validated['description'] ?? null,
            'stock_status' => $validated['stock_status'] ?? 'in_stock',
            'image_url'    => $imageUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Menu item '{$product->name}' added successfully to {$vendor->business_name}.",
            'product' => $product,
        ], 201);
    }

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $name = $product->name;
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => "Menu item '{$name}' deleted successfully.",
        ]);
    }

    public function getSettings()
    {
        $deliveryFee = floatval(Setting::get('delivery_fee', 30));

        return response()->json([
            'status'   => 'success',
            'settings' => [
                'delivery_fee' => $deliveryFee,
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        Setting::set('delivery_fee', $validated['delivery_fee']);

        return response()->json([
            'status'   => 'success',
            'message'  => 'System seat delivery fee updated to Ksh ' . number_format($validated['delivery_fee'], 2) . ' successfully.',
            'settings' => [
                'delivery_fee' => floatval($validated['delivery_fee']),
            ],
        ]);
    }
}
