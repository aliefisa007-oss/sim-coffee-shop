<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'kode_kategori' => ['required', 'string', 'max:10', 'unique:kategori_menu'],
            'nama_kategori' => ['required', 'string', 'max:100'],
            'deskripsi'     => ['nullable', 'string'],
        ];
    }
}