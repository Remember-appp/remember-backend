<?php

namespace App\DTO\Admin;

class UserFilter
{
    public function __construct(
        public readonly string  $q        = '',
        public readonly ?string $status   = null,   // active|inactive
        public readonly ?string $role     = null,   // user|admin
        public readonly int     $perPage  = 20,
        public readonly string  $sort     = 'created_at',
        public readonly string  $dir      = 'desc',
    ) {}
}
