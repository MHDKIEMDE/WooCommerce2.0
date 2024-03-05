<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        $results = Product::where('name', 'like', "%$query%")->get();

        return view('welcome', ['results' => $results]);
    }
}
