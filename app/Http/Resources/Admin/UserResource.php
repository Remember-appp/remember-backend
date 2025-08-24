<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => (string) $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'status'     => $this->status,
            'roles'      => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'created_at' => optional($this->created_at),
            'updated_at' => optional($this->updated_at),
        ];
    }
}
