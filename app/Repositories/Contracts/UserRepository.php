<?php

namespace App\Repositories\Contracts;

use App\DTO\Admin\UserFilter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function paginate(UserFilter $filter): LengthAwarePaginator;
    public function findOrFail(int $id): User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function deactivate(User $user): void;
    public function syncRoles(User $user, string|array|null $roles): void;
}
