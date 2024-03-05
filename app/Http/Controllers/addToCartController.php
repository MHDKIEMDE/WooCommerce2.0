<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\addToCart;

class AddToCartController extends Controller
{
    /**
     * Affiche les produits dans un panier spécifique.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\Response
     */
    public function showCartProducts($cartId)
    {
        $cart = addToCart::find($cartId);
        
        if (!$cart) {
            // Gérer le cas où le panier n'existe pas
            return response()->json(['message' => 'Le panier spécifié n\'existe pas'], 404);
        }

        $products = $cart->products;

        return response()->json($products);
    }
}
