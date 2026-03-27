<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $today     = now()->toDateString();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'revenue' => [
                'today'      => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total'),
                'this_month' => Order::where('created_at', '>=', $thisMonth)->where('payment_status', 'paid')->sum('total'),
                'total'      => Order::where('payment_status', 'paid')->sum('total'),
            ],
            'orders' => [
                'today'      => Order::whereDate('created_at', $today)->count(),
                'this_month' => Order::where('created_at', '>=', $thisMonth)->count(),
                'pending'    => Order::where('status', 'pending')->count(),
                'total'      => Order::count(),
            ],
            'customers' => [
                'new_today'  => User::where('role', 'customer')->whereDate('created_at', $today)->count(),
                'this_month' => User::where('role', 'customer')->where('created_at', '>=', $thisMonth)->count(),
                'total'      => User::where('role', 'customer')->count(),
            ],
            'products' => [
                'total'       => Product::count(),
                'low_stock'   => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('status', 'active')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->where('status', 'active')->count(),
            ],
        ];

        $recentOrders = Order::with('user:id,name,email')
            ->latest()
            ->limit(10)
            ->get(['id', 'order_number', 'user_id', 'status', 'total', 'created_at']);

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'products.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return $this->success([
            'stats'         => $stats,
            'recent_orders' => $recentOrders,
            'top_products'  => $topProducts,
        ]);
    }
}
