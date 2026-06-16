<?php

namespace App\Http\Requests\Kasir;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isKasir() || auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'items'             => ['required', 'array', 'min:1'],
            'items.*.menu_id'   => ['required', 'exists:menus,id'],
            'items.*.nama_menu' => ['required', 'string'],
            'items.*.harga'     => ['required', 'numeric', 'min:0'],
            'items.*.qty'       => ['required', 'integer', 'min:1'],
            'items.*.subtotal'  => ['required', 'numeric', 'min:0'],
            'metode_bayar'      => ['required', 'in:cash,qris,transfer'],
            'diskon'            => ['nullable', 'numeric', 'min:0'],
            'pajak'             => ['nullable', 'numeric', 'min:0'],
            'catatan'           => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'       => 'Keranjang tidak boleh kosong.',
            'items.*.qty.min'      => 'Qty minimal 1.',
            'metode_bayar.required'=> 'Pilih metode pembayaran.',
            'metode_bayar.in'      => 'Metode bayar tidak valid.',
        ];
    }
}