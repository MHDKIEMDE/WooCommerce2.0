<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function edit(User $user)
    {
        return view('dashboard.admin.Users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'role'     => 'required|in:customer,admin,super-admin',
            'is_active'=> 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $update = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$name} supprimé.");
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
