<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(protected User $model) {}

    public function getAll(array $filters = [])
    {
        return $this->model
            ->when(isset($filters['search']), fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
            )
            ->when(isset($filters['role']), fn($q) =>
                $q->where('role', $filters['role'])
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
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id)
    {
        return $this->findById($id)->delete();
    }

    public function getAllKasir()
    {
        return $this->model->kasir()->aktif()->get();
    }
}