<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreKategoriRequest;
use App\Models\KategoriMenu;
use Illuminate\Http\Request;

class KategoriMenuController extends Controller
{
    public function index(Request $request)
    {
        $kategori = KategoriMenu::withCount('menus')
            ->when($request->search, fn($q) =>
                $q->where('nama_kategori', 'like', "%{$request->search}%")
            )
            ->latest()->paginate(10);

        return view('owner.kategori.index', compact('kategori'));
    }

    public function store(StoreKategoriRequest $request)
    {
        KategoriMenu::create($request->validated());
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(StoreKategoriRequest $request, KategoriMenu $kategori)
    {
        $kategori->update($request->validated());
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriMenu $kategori)
    {
        if ($kategori->menus()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki menu.');
        }
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}