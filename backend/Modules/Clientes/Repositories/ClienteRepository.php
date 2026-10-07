<?php
class ClienteRepository
{
    public static function dniExists(string $dni): bool
    {
        global $capsule;
        return $capsule->table('tclie_general')->where('dni', $dni)->exists();
    }

    public static function create(array $data)
    {
        return \App\Models\Customer::create($data);
    }

    public static function find(int $idCG)
    {
        return \App\Models\Customer::where('idCG', $idCG)->first();
    }

    public static function search(string $q, int $limit = 100)
    {
        return \App\Models\Customer::with('riskProfile')
            ->where('dni', 'like', "%{$q}%")
            ->orWhere('nom', 'like', "%{$q}%")
            ->orWhere('ap', 'like', "%{$q}%")
            ->orderBy('idCG', 'desc')->limit($limit)->get();
    }

    public static function recientes(int $limit = 200)
    {
        return \App\Models\Customer::with('riskProfile')->orderBy('idCG', 'desc')->limit($limit)->get();
    }

    public static function porDni(string $dni): ?object
    {
        return \App\Models\Customer::where('dni', $dni)->first(['idCG']);
    }

    public static function ubigeo(?int $ubigeoId): ?object
    {
        if (!$ubigeoId) return null;
        global $capsule;
        return $capsule->table('ubigeo_districts as d')
            ->join('ubigeo_provinces as p', 'd.province_id', 'p.id')
            ->join('ubigeo_departments as dep', 'p.department_id', 'dep.id')
            ->where('d.id', $ubigeoId)
            ->select('d.name as district', 'p.name as province', 'dep.name as department')->first();
    }
}
