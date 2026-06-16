<?php

namespace App\Repositories\Contracts;

interface BahanBakuRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getMenipis();
    public function updateStok(int $id, float $stok);
}