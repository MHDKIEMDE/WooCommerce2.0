<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        $shop = Shop::where('slug', $slug)->firstOrFail();

        if ($shop->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas propriétaire de cette boutique.',
            ], 403);
        }

        $request->attributes->set('shop', $shop);

        return $next($request);
    }
}
