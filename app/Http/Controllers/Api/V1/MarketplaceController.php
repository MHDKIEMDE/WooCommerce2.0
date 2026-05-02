<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketplaceController extends BaseApiController
{
    // GET /api/v1/marketplace
    // Page d'accueil : boutiques vedettes, produits par niche, stats globales
    public function home(): JsonResponse
    {
        $data = Cache::remember('marketplace:home', now()->addMinutes(15), function () {
            // Boutiques actives récentes (1 par template pour la diversité)
            $featuredShops = Shop::with(['template:id,name,slug,icon', 'palette'])
                ->where('status', 'active')
                ->latest()
                ->limit(8)
                ->get();

            // Produits vedettes toutes boutiques confondues
            $featuredProducts = Product::with(['category:id,name,slug', 'images', 'shop:id,name,slug,template_id', 'shop.template:id,slug'])
                ->where('status', 'active')
                ->where('featured', true)
                ->whereHas('shop', fn ($q) => $q->where('status', 'active'))
                ->orderBy('rating_avg', 'desc')
                ->limit(12)
                ->get();

            // Nouveaux produits
            $newProducts = Product::with(['category:id,name,slug', 'images', 'shop:id,name,slug,template_id', 'shop.template:id,slug'])
                ->where('status', 'active')
                ->whereHas('shop', fn ($q) => $q->where('status', 'active'))
                ->latest()
                ->limit(12)
                ->get();

            // Produits par niche (3 par template actif)
            $byNiche = ShopTemplate::withCount(['shops' => fn ($q) => $q->where('status', 'active')])
                ->get()
                ->filter(fn ($t) => $t->shops_count > 0)
                ->map(function ($template) {
                    $products = Product::with(['images', 'shop:id,name,slug'])
                        ->where('status', 'active')
                        ->whereHas('shop', fn ($q) =>
                            $q->where('status', 'active')->where('template_id', $template->id)
                        )
                        ->orderBy('rating_avg', 'desc')
                        ->limit(6)
                        ->get();

                    return [
                        'template' => [
                            'id'          => $template->id,
                            'name'        => $template->name,
                            'slug'        => $template->slug,
                            'icon'        => $template->icon,
                            'shops_count' => $template->shops_count,
                        ],
                        'products' => ProductResource::collection($products),
                    ];
                })
                ->filter(fn ($n) => count($n['products']) > 0)
                ->values();

            // Stats globales
            $stats = [
                'total_shops'    => Shop::where('status', 'active')->count(),
                'total_products' => Product::where('status', 'active')
                                           ->whereHas('shop', fn ($q) => $q->where('status', 'active'))
                                           ->count(),
                'total_niches'   => ShopTemplate::count(),
            ];

            return compact('featuredShops', 'featuredProducts', 'newProducts', 'byNiche', 'stats');
        });

        return $this->success([
            'featured_shops'    => ShopResource::collection($data['featuredShops']),
            'featured_products' => ProductResource::collection($data['featuredProducts']),
            'new_products'      => ProductResource::collection($data['newProducts']),
            'by_niche'          => $data['byNiche'],
            'stats'             => $data['stats'],
        ]);
    }

    // GET /api/v1/marketplace/shops
    // Listing public de toutes les boutiques actives avec filtres
    public function shops(Request $request): JsonResponse
    {
        $query = Shop::with(['template:id,name,slug,icon', 'palette'])
            ->where('status', 'active')
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')]);

        if ($request->filled('niche')) {
            $query->whereHas('template', fn ($q) => $q->where('slug', $request->niche));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
            );
        }

        $sort = match ($request->input('sort', 'latest')) {
            'popular'  => ['products_count', 'desc'],
            'oldest'   => ['created_at', 'asc'],
            default    => ['created_at', 'desc'],
        };

        $shops = $query->orderBy(...$sort)->paginate($request->input('per_page', 20));

        return $this->paginated($shops);
    }

    // GET /api/v1/marketplace/niches
    // Liste des niches disponibles avec le nombre de boutiques et de produits
    public function niches(): JsonResponse
    {
        $niches = Cache::remember('marketplace:niches', now()->addHours(1), function () {
            return ShopTemplate::withCount([
                'shops' => fn ($q) => $q->where('status', 'active'),
            ])->get()
            ->map(fn ($t) => [
                'id'          => $t->id,
                'name'        => $t->name,
                'slug'        => $t->slug,
                'icon'        => $t->icon,
                'fonts'       => $t->fonts,
                'sections'    => $t->sections,
                'shops_count' => $t->shops_count,
            ]);
        });

        return $this->success($niches);
    }
}
