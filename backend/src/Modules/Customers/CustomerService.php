<?php

namespace CrediSoporte\Modules\Customers;

use CrediSoporte\Domain\Models\Customer;
use Illuminate\Support\Collection;

class CustomerService
{
    public function recientes(int $limit = 50): Collection
    {
        return Customer::orderByDesc('idCG')->take($limit)->get();
    }

    public function find(int $id): ?Customer
    {
        return Customer::with(['credits', 'attachments', 'relations'])->find($id);
    }
}
