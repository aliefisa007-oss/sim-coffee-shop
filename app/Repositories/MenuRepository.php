<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;

class MenuRepository implements MenuRepositoryInterface
{
    public function __construct(protected Menu $model) {}

    public function getAll(array $filters = [])
    {
        return $this->model
            ->with('kategori')
            ->when(isset($filters['search']), fn($q) =>
                $q->where('nama_menu', 'like', "%{$filters['search']}%")
                  ->orWhere('kode_menu', 'like', "%{$filters['search']}%")
            )
            ->when(isset($filters['kategori_id']), fn($q) =>
                $q->where('kategori_id', $filters['kategori_id'])
            )
            ->when(isset($filters['status']), fn($q) =>
                $q->where('status_aktif', $filters['status'])
            )
            ->latest()->paginate(10);
    }

    public function findById(int $id)
    {
        return $this->model->with(['kategori', 'resepProduk.bahanBaku'])
                           ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $menu = $this->model->findOrFail($id);
        $menu->update($data);
        return $menu;
    }

    public function delete(int $id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getAktif()
    {
        return $this->model->with('kategori')
                           ->aktif()
                           ->orderBy('nama_menu')
                           ->get();
    }

    public function getByKategori(int $kategoriId)
    {
        return $this->model->aktif()->byKategori($kategoriId)->get();
    }
}