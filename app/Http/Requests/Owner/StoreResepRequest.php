<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'menu_id'       => ['required', 'exists:menus,id'],
            'bahan_baku_id' => ['required', 'exists:bahan_baku,id'],
            'jumlah'        => ['required', 'numeric', 'min:0.01'],
        ];
    }
}