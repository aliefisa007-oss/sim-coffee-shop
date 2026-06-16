<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function index(Request $request)
    {
        $users = $this->userRepo->getAll($request->only(['search', 'role']));
        return view('owner.users.index', compact('users'));
    }

    public function create()
    {
        return view('owner.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data             = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $this->userRepo->create($data);
        return redirect()->route('owner.users.index')
                         ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->userRepo->findById($id);
        return view('owner.users.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->only(['name', 'role', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $this->userRepo->update($id, $data);
        return redirect()->route('owner.users.index')
                         ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->userRepo->delete($id);
        return redirect()->route('owner.users.index')
                         ->with('success', 'User berhasil dihapus.');
    }

    public function toggleActive(int $id)
    {
        $user = $this->userRepo->findById($id);
        $this->userRepo->update($id, ['is_active' => !$user->is_active]);
        return back()->with('success', 'Status user berhasil diubah.');
    }
}