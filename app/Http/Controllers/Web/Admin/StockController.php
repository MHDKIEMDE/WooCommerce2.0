<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('stock_quantity', 'asc');

        $filter = $request->get('filter', 'low');

        if ($filter === 'low') {
            $query->whereRaw('stock_quantity <= low_stock_threshold');
        } elseif ($filter === 'out') {
            $query->where('stock_quantity', 0);
        } elseif ($filter === 'active') {
            $query->where('status', 'active');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) =>
                $w->where('name', 'like', "%$q%")
                  ->orWhere('sku', 'like', "%$q%")
            );
        }

        $products = $query->paginate(25)->withQueryString();

        $counts = [
            'low' => Product::whereRaw('stock_quantity <= low_stock_threshold')->count(),
            'out' => Product::where('stock_quantity', 0)->count(),
            'all' => Product::count(),
        ];

        return view('dashboard.admin.Stock.index', compact('products', 'counts', 'filter'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'stock_quantity'      => $request->stock_quantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? $product->low_stock_threshold,
        ]);

        return back()->with('success', "Stock de « {$product->name} » mis à jour.");
    }
}
