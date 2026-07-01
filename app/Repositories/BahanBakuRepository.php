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

    public function getTopFastMoving(int $limit = 10, int $hari = 30)
{
    $subquery = \DB::table('riwayat_stok')
        ->select('bahan_baku_id', \DB::raw('SUM(jumlah) as total_keluar'))
        ->where('tipe', 'keluar')
        ->where('created_at', '>=', now()->subDays($hari))
        ->groupBy('bahan_baku_id');

    return $this->model
        ->select('bahan_baku.*', \DB::raw('COALESCE(rs.total_keluar, 0) as total_keluar'))
        ->leftJoinSub($subquery, 'rs', 'rs.bahan_baku_id', '=', 'bahan_baku.id')
        ->orderByDesc('total_keluar')
        ->limit($limit)
        ->get();
}

    public function getNilaiTotalStok(): float
    {
        return (float) $this->model->selectRaw('SUM(stok * harga_per_satuan) as nilai')->value('nilai');
    }
}