<?php

namespace App\Repositories;

use App\Models\RiwayatStok;
use App\Repositories\Contracts\RiwayatStokRepositoryInterface;

class RiwayatStokRepository implements RiwayatStokRepositoryInterface
{
    public function __construct(protected RiwayatStok $model) {}

    public function getAll(array $filters = [])
    {
        return $this->model
            ->with(['bahanBaku', 'user'])
            ->when(isset($filters['bahan_baku_id']), fn($q) =>
                $q->where('bahan_baku_id', $filters['bahan_baku_id'])
            )
            ->when(isset($filters['tipe']), fn($q) =>
                $q->where('tipe', $filters['tipe'])
            )
            ->latest()->paginate(20);
    }

    public function getByBahan(int $bahanId)
    {
        return $this->model
            ->where('bahan_baku_id', $bahanId)
            ->with(['user', 'transaksi'])
            ->latest()->paginate(20);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}