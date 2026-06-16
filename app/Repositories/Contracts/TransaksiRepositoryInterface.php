<?php

namespace App\Repositories\Contracts;

interface TransaksiRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function createDetail(int $transaksiId, array $items);
    public function getHariIni();
    public function getBulanIni();
    public function getRentang(string $dari, string $sampai);
    public function getTopMenu(int $limit = 10);
}