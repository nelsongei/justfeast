<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $users = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'created_at'])
            ->orderBy('role')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($users);
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,vendor,runner,client',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'role'     => $data['role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User account created successfully!',
            'user'    => $user
        ]);
    }
}
