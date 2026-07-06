<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuApiController extends Controller
{
    // GET /api/v1/menus
    public function index(Request $request)
    {
        $query = Menu::with('kategori');

        if ($request->has('kategori_id')) {
            $query->byKategori($request->kategori_id);
        }

        if ($request->has('status_aktif')) {
            $query->where('status_aktif', $request->boolean('status_aktif'));
        } else {
            // Default: hanya tampilkan yang aktif kalau tidak ada filter eksplisit
            $query->aktif();
        }

        $menus = $query->orderBy('nama_menu')->get();

        return MenuResource::collection($menus);
    }

    // GET /api/v1/menus/{id}
    public function show($id)
    {
        $menu = Menu::with('kategori')->find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        return new MenuResource($menu);
    }

    // POST /api/v1/menus
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_menu' => 'required|string|max:10|unique:menus,kode_menu',
            'nama_menu' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori_menu,id',
            'harga_jual' => 'required|numeric|min:0',
            'status_aktif' => 'nullable|boolean',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $menu = Menu::create([
            'kode_menu' => $request->kode_menu,
            'nama_menu' => $request->nama_menu,
            'kategori_id' => $request->kategori_id,
            'harga_jual' => $request->harga_jual,
            'status_aktif' => $request->status_aktif ?? 1,
            'deskripsi' => $request->deskripsi,
        ]);

        return new MenuResource($menu->load('kategori'));
    }

    // PUT/PATCH /api/v1/menus/{id}
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_menu' => 'sometimes|string|max:10|unique:menus,kode_menu,' . $id,
            'nama_menu' => 'sometimes|string|max:150',
            'kategori_id' => 'sometimes|exists:kategori_menu,id',
            'harga_jual' => 'sometimes|numeric|min:0',
            'status_aktif' => 'sometimes|boolean',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $menu->update($validator->validated());

        return new MenuResource($menu->load('kategori'));
    }

    // DELETE /api/v1/menus/{id}
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}
