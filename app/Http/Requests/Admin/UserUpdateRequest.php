<?php
// app/Http/Requests/Admin/UserUpdateRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('admin') === true; }

    public function rules(): array
    {
        $userId = $this->route('user')?->id; // UUID

        return [
            'name'     => ['sometimes','string','max:255'],
            'email'    => ['sometimes','email','max:255', Rule::unique('users','email')->ignore($userId, 'id')],
            'phone'    => ['nullable','string','max:255'],
            'password' => ['nullable','string','min:8','max:255'],
            'status'   => ['sometimes', Rule::in(['active','inactive'])],
            'role'     => ['sometimes', Rule::in(['user','admin'])],
        ];
    }
}
