<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreResepRequest;
use App\Models\Menu;
use App\Models\ResepProduk;
use App\Models\BahanBaku;

class ResepProdukController extends Controller
{
    public function index()
    {
        $menus = Menu::with('resepProduk.bahanBaku')->aktif()->get();
        return view('owner.resep.index', compact('menus'));
    }

    public function byMenu(Menu $menu)
    {
        $menu->load('resepProduk.bahanBaku');
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        return view('owner.resep.form', compact('menu', 'bahanBaku'));
    }

    public function store(StoreResepRequest $request)
    {
        ResepProduk::updateOrCreate(
            [
                'menu_id'       => $request->menu_id,
                'bahan_baku_id' => $request->bahan_baku_id,
            ],
            ['jumlah' => $request->jumlah]
        );
        return back()->with('success', 'Resep berhasil disimpan.');
    }

    public function destroy(ResepProduk $resep)
    {
        $resep->delete();
        return back()->with('success', 'Bahan dari resep berhasil dihapus.');
    }
}