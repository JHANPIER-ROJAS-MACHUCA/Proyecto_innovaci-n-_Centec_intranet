<?php

namespace CrediSoporte\Modules\Cash;

use CrediSoporte\Domain\Models\CashOffice;
use CrediSoporte\Domain\Models\Transaction;
use Illuminate\Support\Collection;

class CashService
{
    public function oficinas(): Collection
    {
        return CashOffice::take(100)->get();
    }

    public function movimientos(int $limit = 100): Collection
    {
        return Transaction::orderByDesc('idCAD')->take($limit)->get();
    }
}
