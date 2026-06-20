<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMenuRequest;
use App\Http\Requests\Owner\UpdateMenuRequest;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Models\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct(
        protected MenuRepositoryInterface $menuRepo
    ) {}

    public function index(Request $request)
    {
        $menus    = $this->menuRepo->getAll($request->only(['search', 'kategori_id', 'status']));
        $kategori = KategoriMenu::all();
        return view('owner.menu.index', compact('menus', 'kategori'));
    }

    public function create()
    {
        $kategori = KategoriMenu::all();
        return view('owner.menu.create', compact('kategori'));
    }

    public function store(StoreMenuRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('foto_menu')) {
            $file            = $request->file('foto_menu');
            $filename        = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menus'), $filename);
            $data['foto_menu'] = $filename;
        }
        $this->menuRepo->create($data);
        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $menu     = $this->menuRepo->findById($id);
        $kategori = KategoriMenu::all();
        return view('owner.menu.edit', compact('menu', 'kategori'));
    }

    public function update(UpdateMenuRequest $request, int $id)
    {
        $data = $request->validated();
        if ($request->hasFile('foto_menu')) {
            $file            = $request->file('foto_menu');
            $filename        = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menus'), $filename);
            $data['foto_menu'] = $filename;
        }
        $this->menuRepo->update($id, $data);
        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(int $id)
{
    $menu = $this->menuRepo->findById($id);

    // Cek apakah menu pernah dipakai di transaksi
    if ($menu->detailTransaksi()->exists()) {
        // Nonaktifkan saja, jangan hapus
        $this->menuRepo->update($id, ['status_aktif' => false]);
        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu tidak bisa dihapus karena pernah ada di transaksi. Menu dinonaktifkan.');
    }

    // Hapus resep dulu
    $menu->resepProduk()->delete();

    // Hapus menu
    $this->menuRepo->delete($id);

    return redirect()->route('owner.menu.index')
                     ->with('success', 'Menu berhasil dihapus.');
    }

    public function toggleStatus(int $id)
    {
        $menu = $this->menuRepo->findById($id);
        $this->menuRepo->update($id, ['status_aktif' => !$menu->status_aktif]);
        return back()->with('success', 'Status menu berhasil diubah.');
    }
}