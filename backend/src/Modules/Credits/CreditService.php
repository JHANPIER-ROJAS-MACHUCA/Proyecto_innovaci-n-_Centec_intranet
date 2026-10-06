<?php

namespace CrediSoporte\Modules\Credits;

use CrediSoporte\Domain\Models\Credit;
use Illuminate\Support\Collection;

class CreditService
{
    public function recientes(int $limit = 50): Collection
    {
        return Credit::orderByDesc('idP')->take($limit)->get();
    }

    public function detalle(int $id): ?Credit
    {
        return Credit::with(['installments', 'customer', 'transactions'])->find($id);
    }
}
