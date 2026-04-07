<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::orderBy('sort_order')->orderBy('name')->get();

        return view('dashboard.admin.DeliveryZones.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DeliveryZone::create([
            'name'       => $data['name'],
            'price'      => $data['price'],
            'is_active'  => true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Zone ajoutée.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'price'      => 'required|numeric|min:0',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $deliveryZone->update([
            'name'       => $data['name'],
            'price'      => $data['price'],
            'is_active'  => isset($data['is_active']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Zone mise à jour.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return back()->with('success', 'Zone supprimée.');
    }
}
