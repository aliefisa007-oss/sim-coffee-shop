<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'kode_bahan'   => ['required', 'string', 'max:10', 'unique:bahan_baku'],
            'nama_bahan'   => ['required', 'string', 'max:150'],
            'satuan'       => ['required', 'in:gram,ml,pcs,botol'],
            'stok'         => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
            'harga_per_satuan' => ['required', 'numeric', 'min:0'],
        ];
    }
}