<?php

namespace App\Services;

use App\DTO\Admin\UserFilter;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly UserRepository $repo) {}

    public function list(UserFilter $filter): LengthAwarePaginator
    {
        return $this->repo->paginate($filter);
    }

    public function show(int $id): User
    {
        return $this->repo->findOrFail($id);
    }

    public function store(UserStoreRequest $request): User
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['status']   = $data['status'] ?? 'active';

        return DB::transaction(function () use ($data) {
            $user = $this->repo->create([
                'name' => $data['name'],
                'email'=> $data['email'],
                'phone'=> $data['phone'] ?? null,
                'password' => $data['password'],
                'status'   => $data['status'],
            ]);
            $this->repo->syncRoles($user, $data['role'] ?? 'user');
            return $user->load('roles:id,name');
        });
    }

    public function update(UserUpdateRequest $request, User $user): User
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return DB::transaction(function () use ($data, $user) {
            $user = $this->repo->update($user, $data);
            if (array_key_exists('role', $data)) {
                $this->repo->syncRoles($user, $data['role']);
            }
            return $user->load('roles:id,name');
        });
    }

    public function deactivate(User $user): void
    {
        $this->repo->deactivate($user);
    }
}
