<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Wishlist;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function profile(Request $request): View
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function editProfile(Request $request): View
    {
        return view('account.edit-profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('account.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function addresses(Request $request): View
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();
        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'street'     => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'zip'        => 'nullable|string|max:20',
            'country'    => 'required|string|max:100',
            'phone'      => 'nullable|string|max:30',
            'type'       => 'in:shipping,billing',
        ]);

        $data['user_id']    = $request->user()->id;
        $data['is_default'] = $request->user()->addresses()->count() === 0;

        Address::create($data);

        return back()->with('success', 'Adresse ajoutée.');
    }

    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $data = $request->validate([
            'label'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'street'     => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'zip'        => 'nullable|string|max:20',
            'country'    => 'required|string|max:100',
            'phone'      => 'nullable|string|max:30',
        ]);

        $address->update($data);

        return back()->with('success', 'Adresse mise à jour.');
    }

    public function destroyAddress(Request $request, int $id): RedirectResponse
    {
        $request->user()->addresses()->findOrFail($id)->delete();
        return back()->with('success', 'Adresse supprimée.');
    }

    public function setDefaultAddress(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->findOrFail($id)->update(['is_default' => true]);
        return back()->with('success', 'Adresse principale définie.');
    }

    public function downloadInvoice(Request $request, int $id): Response
    {
        $order = Order::where('user_id', $request->user()->id)->with(['items', 'user'])->findOrFail($id);
        $shop  = Setting::getGroup('shop');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order'       => $order,
            'shopName'    => $shop['shop_name'] ?? config('app.name'),
            'shopAddress' => $shop['shop_address'] ?? null,
            'shopPhone'   => $shop['shop_phone'] ?? null,
            'shopEmail'   => $shop['shop_email'] ?? null,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("facture-{$order->order_number}.pdf");
    }

    public function wishlist(Request $request): View
    {
        $items = Wishlist::with('product.images')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('items'));
    }

    public function toggleWishlist(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $user      = $request->user();
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Produit retiré de votre liste de souhaits.';
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $message = 'Produit ajouté à votre liste de souhaits.';
        }

        return back()->with('success', $message);
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
