<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectShop
{
    public function handle(Request $request, Closure $next): Response
    {
        $host       = $request->getHost();
        $rootDomain = config('app.root_domain', 'monghetto.com');

        // Extraire le sous-domaine (ex: "shop" depuis "shop.monghetto.com")
        $subdomain = str_ends_with($host, '.' . $rootDomain)
            ? rtrim(substr($host, 0, -strlen('.' . $rootDomain)), '.')
            : null;

        if ($subdomain && $subdomain !== 'www' && $subdomain !== 'api' && $subdomain !== 'admin') {
            $shop = Shop::where('subdomain', $subdomain)
                ->where('status', 'active')
                ->with(['template', 'palette'])
                ->first();

            if (! $shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Boutique introuvable ou inactive.',
                ], 404);
            }

            $request->attributes->set('current_shop', $shop);
            app()->instance('current_shop', $shop);
        }

        return $next($request);
    }
}
