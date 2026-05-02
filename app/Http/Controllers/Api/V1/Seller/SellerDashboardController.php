<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ShopResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerDashboardController extends BaseApiController
{
    // GET /api/v1/seller/dashboard
    public function index(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        $thisMonth = now()->startOfMonth();
        $today     = now()->toDateString();

        // Commandes liées à cette boutique
        $ordersQuery = Order::where('shop_id', $shop->id);

        $stats = [
            'revenue' => [
                'today'      => (clone $ordersQuery)->whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total'),
                'this_month' => (clone $ordersQuery)->where('created_at', '>=', $thisMonth)->where('payment_status', 'paid')->sum('total'),
                'total'      => (clone $ordersQuery)->where('payment_status', 'paid')->sum('total'),
            ],
            'orders' => [
                'today'   => (clone $ordersQuery)->whereDate('created_at', $today)->count(),
                'pending' => (clone $ordersQuery)->where('status', 'pending')->count(),
                'total'   => (clone $ordersQuery)->count(),
            ],
            'products' => [
                'total'        => Product::where('shop_id', $shop->id)->count(),
                'active'       => Product::where('shop_id', $shop->id)->where('status', 'active')->count(),
                'low_stock'    => Product::where('shop_id', $shop->id)->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('status', 'active')->count(),
                'out_of_stock' => Product::where('shop_id', $shop->id)->where('stock_quantity', 0)->count(),
            ],
        ];

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.shop_id', $shop->id)
            ->select(
                'products.id', 'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $recentOrders = Order::where('shop_id', $shop->id)
            ->with('user:id,name,email')
            ->latest()
            ->limit(10)
            ->get(['id', 'order_number', 'user_id', 'status', 'total', 'created_at']);

        return $this->success([
            'shop'          => new ShopResource($shop->load(['template', 'palette'])),
            'stats'         => $stats,
            'top_products'  => $topProducts,
            'recent_orders' => $recentOrders,
        ]);
    }

    // GET /api/v1/seller/shop
    public function shop(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        return $this->success(
            new ShopResource($shop->load(['template', 'palette', 'sections']))
        );
    }

    // PATCH /api/v1/seller/shop
    public function updateShop(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $shop->update($data);

        return $this->success(
            new ShopResource($shop->fresh()->load(['template', 'palette'])),
            'Boutique mise à jour.'
        );
    }

    // PATCH /api/v1/seller/shop/template
    public function changeTemplate(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        $data = $request->validate([
            'template_id' => 'required|exists:shop_templates,id',
            'palette_id'  => 'required|exists:shop_palettes,id',
        ]);

        // Valider que la palette appartient au template
        \App\Models\ShopPalette::where('id', $data['palette_id'])
            ->where('template_id', $data['template_id'])
            ->firstOrFail();

        $shop->update([
            'template_id' => $data['template_id'],
            'palette_id'  => $data['palette_id'],
        ]);

        // Recréer les sections par défaut du nouveau template
        $template = \App\Models\ShopTemplate::find($data['template_id']);
        if ($template && $template->sections) {
            $shop->sections()->delete();
            foreach ($template->sections as $i => $sectionType) {
                $shop->sections()->create([
                    'type'       => $sectionType,
                    'is_active'  => true,
                    'sort_order' => $i,
                ]);
            }
        }

        return $this->success(
            new ShopResource($shop->fresh()->load(['template', 'palette', 'sections'])),
            'Template et palette mis à jour.'
        );
    }
}
