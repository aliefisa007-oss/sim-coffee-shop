<?php

namespace App\Repositories;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;

class TransaksiRepository implements TransaksiRepositoryInterface
{
    public function __construct(
        protected Transaksi $model,
        protected DetailTransaksi $detail
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->model
            ->with(['user', 'detailTransaksi'])
            ->when(isset($filters['user_id']), fn($q) =>
                $q->where('user_id', $filters['user_id'])
            )
            ->when(isset($filters['status']), fn($q) =>
                $q->where('status', $filters['status'])
            )
            ->latest('tanggal')->paginate(15);
    }

    public function findById(int $id)
    {
        return $this->model
            ->with(['user', 'detailTransaksi.menu'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function createDetail(int $transaksiId, array $items)
    {
        $details = array_map(fn($item) => array_merge(
            $item, ['transaksi_id' => $transaksiId]
        ), $items);
        return $this->detail->insert($details);
    }

    public function getHariIni()
    {
        return $this->model->selesai()->hariIni()->get();
    }

    public function getBulanIni()
    {
        return $this->model->selesai()->bulanIni()->get();
    }

    public function getRentang(string $dari, string $sampai)
    {
        return $this->model->selesai()->rentang($dari, $sampai)->get();
    }

    public function getTopMenu(int $limit = 10)
    {
        return $this->detail
            ->selectRaw('menu_id, nama_menu, SUM(qty) as total_terjual')
            ->groupBy('menu_id', 'nama_menu')
            ->orderByDesc('total_terjual')
            ->limit($limit)
            ->get();
    }
}