<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — API-only project
| Tous les endpoints sont sous /api/v1/ (voir routes/api.php)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'WooCommerce 2.0 API',
        'version' => 'v1',
        'docs'    => '/api/v1/',
    ]);
});
