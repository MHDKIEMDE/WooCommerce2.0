<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'label'      => $this->label,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'street'     => $this->street,
            'city'       => $this->city,
            'zip'        => $this->zip,
            'country'    => $this->country,
            'phone'      => $this->phone,
            'is_default' => $this->is_default,
        ];
    }
}
