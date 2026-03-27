<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'customer')->withCount('orders')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
            );
        }

        if ($request->filled('active')) {
            $query->where('is_active', (bool) $request->active);
        }

        return $this->paginated($query->paginate(25));
    }

    public function show(int $id): JsonResponse
    {
        $user = User::withCount('orders')
            ->with(['orders' => fn ($q) => $q->latest()->limit(5)])
            ->findOrFail($id);

        return $this->success(new UserResource($user));
    }

    public function toggleActive(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return $this->error('Impossible de désactiver un administrateur.', 403);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        return $this->success(
            ['is_active' => $user->is_active],
            "Compte {$status}."
        );
    }
}
