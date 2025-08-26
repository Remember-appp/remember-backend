<?php
// app/Http/Requests/Admin/UserStoreRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('admin') === true; }

    public function rules(): array
    {
        return [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable','string','max:255'],
            'password' => ['required','string','min:8','max:255'],
            'status'   => ['nullable', Rule::in(['active','inactive'])],
            'role'     => ['nullable', Rule::in(['user','admin'])],
        ];
    }
}
