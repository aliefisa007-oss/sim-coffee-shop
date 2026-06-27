<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'kode_menu'    => ['required', 'string', 'max:10', 'unique:menus'],
            'nama_menu'    => ['required', 'string', 'max:150'],
            'kategori_id'  => ['required', 'exists:kategori_menu,id'],
            'harga_jual'   => ['required', 'numeric', 'min:0'],
            'status_aktif' => ['required', 'boolean'],
            'foto_menu'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi'    => ['nullable', 'string'],
        ];
    }
}