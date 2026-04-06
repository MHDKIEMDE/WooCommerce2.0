<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) =>
                $w->where('name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
            );
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('dashboard.admin.Users.index', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', 'Statut mis à jour.');
    }
}
