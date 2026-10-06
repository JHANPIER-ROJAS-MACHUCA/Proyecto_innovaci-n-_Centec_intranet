<?php

namespace CrediSoporte\Modules\Users;

use CrediSoporte\Domain\Models\User;
use Illuminate\Support\Collection;

class UserService
{
    public function activos(): Collection
    {
        return User::active()->orderBy('idU')->get();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }
}
