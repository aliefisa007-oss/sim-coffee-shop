<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', 'in:owner,kasir'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}