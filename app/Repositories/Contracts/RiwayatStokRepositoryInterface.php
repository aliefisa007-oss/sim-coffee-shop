<?php

namespace App\Repositories\Contracts;

interface RiwayatStokRepositoryInterface
{
    public function getAll(array $filters = []);
    public function getByBahan(int $bahanId);
    public function create(array $data);
}