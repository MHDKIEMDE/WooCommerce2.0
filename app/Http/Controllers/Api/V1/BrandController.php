<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BrandController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $brands = Cache::remember('brands:all', now()->addMinutes(60), function () {
            return Brand::orderBy('name')->get();
        });

        return $this->success(BrandResource::collection($brands));
    }
}
