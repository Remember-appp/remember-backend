<?php

namespace App\Repositories\Eloquent;

use App\DTO\Admin\UserFilter;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
    public function paginate(UserFilter $f): LengthAwarePaginator
    {
        $allowedSort = ['created_at','name','email','status'];
        $sort = in_array($f->sort, $allowedSort, true) ? $f->sort : 'created_at';
        $dir  = $f->dir === 'asc' ? 'asc' : 'desc';

        $q = User::query()
            ->with('roles:id,name')
            ->when($f->q !== '', function ($qq) use ($f) {
                $qq->where(function ($w) use ($f) {
                    $w->where('name',  'ilike', "%{$f->q}%")
                        ->orWhere('email','ilike', "%{$f->q}%")
                        ->orWhere('phone','ilike', "%{$f->q}%");
                });
            })
            ->when(!is_null($f->status), fn ($qq) => $qq->where('status', $f->status))
            ->when(!is_null($f->role),   fn ($qq) => $qq->role($f->role))
            ->orderBy($sort, $dir);

        return $q->paginate(max(1, min($f->perPage, 100)))->withQueryString();
    }

    public function findOrFail(int $id): User
    {
        return User::with('roles:id,name')->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();
        return $user;
    }

    public function deactivate(User $user): void
    {
        $user->update(['status' => 'inactive']);
    }

    public function syncRoles(User $user, string|array|null $roles): void
    {
        $user->syncRoles($roles ?? 'user');
    }
}
