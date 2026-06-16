<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StokMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'jumlah'     => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}