<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();

        return $this->success(AddressResource::collection($addresses));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'       => ['required', 'in:shipping,billing'],
            'label'      => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'street'     => ['required', 'string', 'max:255'],
            'city'       => ['required', 'string', 'max:100'],
            'zip'        => ['required', 'string', 'max:20'],
            'country'    => ['required', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'is_default' => ['boolean'],
        ]);

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($data);

        return $this->success(new AddressResource($address), 'Adresse ajoutée.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $data = $request->validate([
            'type'       => ['sometimes', 'in:shipping,billing'],
            'label'      => ['sometimes', 'nullable', 'string', 'max:50'],
            'first_name' => ['sometimes', 'string', 'max:80'],
            'last_name'  => ['sometimes', 'string', 'max:80'],
            'street'     => ['sometimes', 'string', 'max:255'],
            'city'       => ['sometimes', 'string', 'max:100'],
            'zip'        => ['sometimes', 'string', 'max:20'],
            'country'    => ['sometimes', 'string', 'max:100'],
            'phone'      => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $address->update($data);

        return $this->success(new AddressResource($address->fresh()), 'Adresse mise à jour.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return $this->success(null, 'Adresse supprimée.');
    }

    public function setDefault(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->findOrFail($id)->update(['is_default' => true]);

        return $this->success(null, 'Adresse par défaut définie.');
    }
}
