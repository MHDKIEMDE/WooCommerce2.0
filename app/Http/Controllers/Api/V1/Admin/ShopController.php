<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\ShopApproved;
use App\Events\ShopSuspended;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Shop::with(['owner:id,name,email', 'template:id,name,slug'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%")
            );
        }

        return $this->paginated($query->paginate(20));
    }

    public function approve(int $id): JsonResponse
    {
        $shop = Shop::with('owner')->findOrFail($id);
        $shop->update(['status' => 'active']);

        event(new ShopApproved($shop));

        return $this->success(new ShopResource($shop), 'Boutique validée.');
    }

    public function suspend(Request $request, int $id): JsonResponse
    {
        $data   = $request->validate(['reason' => 'nullable|string|max:500']);
        $shop   = Shop::with('owner')->findOrFail($id);
        $shop->update(['status' => 'suspended']);

        event(new ShopSuspended($shop, $data['reason'] ?? null));

        return $this->success(new ShopResource($shop), 'Boutique suspendue.');
    }
}
