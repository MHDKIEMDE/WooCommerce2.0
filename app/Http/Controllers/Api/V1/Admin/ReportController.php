<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseApiController
{
    public function sales(Request $request): JsonResponse
    {
        $from   = $request->input('from', now()->subDays(30)->toDateString());
        $to     = $request->input('to', now()->toDateString());
        $period = $request->input('period', 'day');   // day | week | month

        $format = match ($period) {
            'week'  => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $sales = Order::selectRaw("DATE_FORMAT(created_at, ?) as period, COUNT(*) as orders, SUM(total) as revenue", [$format])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $summary = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as total_revenue, AVG(total) as avg_order')
            ->first();

        return $this->success([
            'period'  => ['from' => $from, 'to' => $to],
            'summary' => $summary,
            'chart'   => $sales,
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $topSellers = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->whereDate('orders.created_at', '>=', $from)
            ->whereDate('orders.created_at', '<=', $to)
            ->select(
                'products.id', 'products.name', 'products.sku',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('units_sold')
            ->limit(20)
            ->get();

        $lowStock = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold']);

        return $this->success([
            'period'      => ['from' => $from, 'to' => $to],
            'top_sellers' => $topSellers,
            'low_stock'   => $lowStock,
        ]);
    }

    public function customers(Request $request): JsonResponse
    {
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $newCustomers = User::where('role', 'customer')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topSpenders = User::where('role', 'customer')
            ->withSum(['orders as total_spent' => fn ($q) => $q->where('payment_status', 'paid')], 'total')
            ->withCount('orders')
            ->having('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at']);

        return $this->success([
            'period'       => ['from' => $from, 'to' => $to],
            'new_per_day'  => $newCustomers,
            'top_spenders' => $topSpenders,
        ]);
    }

    public function stock(): JsonResponse
    {
        $outOfStock = Product::where('stock_quantity', 0)->where('status', 'active')
            ->with('category:id,name')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'category_id']);

        $critical = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->with('category:id,name')
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold', 'category_id']);

        $totalValue = Product::where('status', 'active')
            ->selectRaw('SUM(stock_quantity * cost_price) as value')
            ->value('value');

        return $this->success([
            'out_of_stock'   => $outOfStock,
            'critical_stock' => $critical,
            'inventory_value' => round((float) $totalValue, 2),
        ]);
    }
}
