<?php
class UbigeoRepository
{
    public static function departamentos()
    {
        global $capsule;
        return $capsule->table('ubigeo_departments')->orderBy('name')->get();
    }

    public static function provincias(?int $departmentId)
    {
        global $capsule;
        $q = $capsule->table('ubigeo_provinces')->orderBy('name');
        if ($departmentId) $q->where('department_id', $departmentId);
        return $q->get();
    }

    public static function distritos(?int $provinceId)
    {
        global $capsule;
        $q = $capsule->table('ubigeo_districts')->orderBy('name');
        if ($provinceId) $q->where('province_id', $provinceId);
        return $q->limit(500)->get();
    }
}
