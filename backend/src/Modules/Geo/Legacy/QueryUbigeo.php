<?php

namespace CrediSoporte\Modules\Geo\Legacy;


// Logica migrada verbatim desde app/api/queryUbigeo.php.
// El wrapper en app/api/queryUbigeo.php preserva URL, entradas y salida legacy.
class QueryUbigeo
{
    public static function handle(): void
    {
        global $database;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$ubigeos = $database->table('ubigeo_districts')
    ->join('ubigeo_provinces', 'ubigeo_districts.province_id', 'ubigeo_provinces.id')
    ->join('ubigeo_departments', 'ubigeo_provinces.department_id', 'ubigeo_departments.id')
    ->select(
        'ubigeo_districts.id',
        'ubigeo_districts.name as district',
        'ubigeo_provinces.name as province',
        'ubigeo_departments.name as department',
    )
    ->selectRaw('concat_ws(" - ", ubigeo_departments.name, ubigeo_provinces.name, ubigeo_districts.name) as full_name')
    ->where(function ($query) use ($search) {
        if ($search) {
            $query->where('ubigeo_districts.name', 'like', $search . '%')
                ->orWhere('ubigeo_provinces.name', 'like', $search . '%')
                ->orWhere('ubigeo_departments.name', 'like', $search . '%');
        }
    })
    ->limit(15)
    ->get();

echo json_encode([
    'data' => $ubigeos
]);
    }
}
