<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StokPenyesuaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'stok_fisik' => ['required', 'numeric', 'min:0'],
            // Wajib diisi — penyesuaian butuh alasan untuk audit trail,
            // beda dengan stok masuk/keluar yang keterangannya opsional.
            'keterangan' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required' => 'Alasan penyesuaian wajib diisi (mis. hasil stock opname, rusak, tumpah, dll).',
        ];
    }
}
