<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $bahanId = $this->route('bahan_baku');

        return [
            'kode_bahan'       => ['required', 'string', 'max:10', Rule::unique('bahan_baku')->ignore($bahanId)],
            'nama_bahan'       => ['required', 'string', 'max:150'],
            'satuan'           => ['required', 'in:gram,ml,pcs,botol'],
            'stok_minimum'     => ['required', 'numeric', 'min:0'],
            'harga_per_satuan' => ['required', 'numeric', 'min:0'],
        ];
    }
}