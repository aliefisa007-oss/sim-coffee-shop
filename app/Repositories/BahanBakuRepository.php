<?php

namespace App\Repositories;

use App\Models\BahanBaku;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;

class BahanBakuRepository implements BahanBakuRepositoryInterface
{
    public function __construct(protected BahanBaku $model) {}

    public function getAll(array $filters = [])
    {
        return $this->model
            ->when(isset($filters['search']), fn($q) =>
                $q->where('nama_bahan', 'like', "%{$filters['search']}%")
                  ->orWhere('kode_bahan', 'like', "%{$filters['search']}%")
            )
            ->latest()->paginate(10);
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $bahan = $this->findById($id);
        $bahan->update($data);
        return $bahan;
    }

    public function delete(int $id)
    {
        return $this->findById($id)->delete();
    }

    public function getMenipis()
    {
        return $this->model->menipis()->get();
    }

    public function updateStok(int $id, float $stok)
    {
        return $this->model->where('id', $id)->update(['stok' => $stok]);
    }
}