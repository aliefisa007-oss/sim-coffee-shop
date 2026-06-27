<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StokMasukRequest extends FormRequest
{
    public function authorize(): bool
{
    return auth()->user()->isOwner() || auth()->user()->isAdmin();
}

    public function rules(): array
    {
        return [
            'jumlah'           => ['required', 'numeric', 'min:0.01'],
            'harga_per_satuan' => ['nullable', 'numeric', 'min:0'],
            'keterangan'       => ['nullable', 'string', 'max:255'],
        ];
    }
}