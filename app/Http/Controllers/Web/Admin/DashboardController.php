<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs globaux
        $kpis = [
            'orders_total'   => Order::count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
            'revenue_total'  => Order::where('payment_status', 'paid')->sum('total'),
            'users_total'    => User::count(),
            'products_total' => Product::where('status', 'active')->count(),
            'low_stock'      => Product::where('status', 'active')
                                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                                    ->where('stock_quantity', '>', 0)->count(),
        ];

        // Commandes + revenus sur les 30 derniers jours (par jour)
        $ordersChart = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as revenue')
            )
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Remplir les jours manquants
        $labels  = [];
        $ordersCounts = [];
        $revenueData  = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[]       = now()->subDays($i)->format('d/m');
            $ordersCounts[] = $ordersChart[$date]->orders  ?? 0;
            $revenueData[]  = $ordersChart[$date]->revenue ?? 0;
        }

        // Top 5 produits les plus vendus
        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // Inscriptions des 30 derniers jours
        $usersChart = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $usersData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $usersData[] = $usersChart[$date]->count ?? 0;
        }

        // Dernières commandes
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.admin.index', compact(
            'kpis', 'labels', 'ordersCounts', 'revenueData',
            'usersData', 'topProducts', 'recentOrders'
        ));
    }
}
