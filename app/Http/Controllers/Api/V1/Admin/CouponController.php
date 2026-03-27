<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::latest();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('is_active', (bool) $request->active);
        }

        return $this->paginated($query->paginate(25));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(Coupon::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'min_order'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at'  => 'nullable|date|after:today',
            'is_active'   => 'boolean',
        ]);

        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $data['is_active'] ?? true;

        $coupon = Coupon::create($data);

        return $this->success($coupon, 'Coupon créé.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        $data = $request->validate([
            'code'        => "sometimes|required|string|max:50|unique:coupons,code,{$id}",
            'type'        => 'sometimes|required|in:percent,fixed',
            'value'       => 'sometimes|required|numeric|min:0',
            'min_order'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at'  => 'nullable|date',
            'is_active'   => 'boolean',
        ]);

        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $coupon->update($data);

        return $this->success($coupon->fresh(), 'Coupon mis à jour.');
    }

    public function destroy(int $id): JsonResponse
    {
        Coupon::findOrFail($id)->delete();

        return $this->success(null, 'Coupon supprimé.');
    }
}
