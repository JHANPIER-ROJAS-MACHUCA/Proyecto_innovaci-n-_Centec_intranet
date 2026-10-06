<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Ubigeo;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/crearCliente.php.
// El wrapper en app/api/crearCliente.php preserva URL, entradas y salida legacy.
class CrearCliente
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$errors = $request->validate([
    'dni' => 'required|digits:8|unique',
    'ap' => 'required|max:30',
    'am' => 'required|max:30',
    'nom' => 'required|max:30',
    'sexo' => 'nullable|in:M,F',
    'cel' => 'nullable|string|max:50',
    'correo' => 'nullable|max:100|email',
    'rubro' => 'nullable|max:100',
    'telefono' => 'nullable|max:100',
    'n_hijos' => 'nullable|max:20',
    'grado_inst' => 'nullable|max:100',
    'fec_nac' => 'nullable|date',
    'estado_civil' => 'nullable|in:S,C,V,D,Conv,Sep',
    'tipo' => 'nullable|in:Propia,Familiar,Alquilada',
    'direc' => 'nullable|max:100',
    'lugar_nac' => 'nullable|max:100',
    'client_type' => 'nullable|exists',
    'risk_profile_id' => 'nullable|exists',
    'idU' => 'nullable|exists',
    'referencia' => 'nullable|max:100',
    'coordinate_lat' => 'nullable|numeric|digits_between:5,15',
    'coordinate_lng' => 'nullable|numeric|digits_between:5,15',
    'ubigeo_id' => 'nullable|exists'
], [
    'dni.required' => 'El dni es requerido.',
    'dni.digits' => 'El dni debe tener :digits dígitos.',
    'dni.unique' => 'El dni ya esta en uso.',
    'ap.required' => 'El apellido paterno es requerido.',
    'ap.max' => 'El apellido paterno no debe tener más de :max caracteres.',
    'am.required' => 'El apellido materno es requerido.',
    'am.max' => 'El apellido materno no debe tener más de :max caracteres.',
    'nom.required' => 'El nombre es requerido.',
    'nom.max' => 'El nombre no debe tener más de :max caracteres.'
], [
    'dni.unique' => $database->table('tclie_general')->where('dni', $request->dni)->exists(),
    'client_type.exists' => $database->table('dictionaries')->where('type', 'CLIENT_TYPE')->where('id', $request->client_type)->exists(),
    'risk_profile_id.exists' => $database->table('dictionaries')->where('type', 'RISK_PROFILE')->where('id', $request->risk_profile_id)->exists(),
    'idU.exists' => $database->table('tusuario')->where('estadoU', '1')->where('idU', $request->idU)->exists(),
    'ubigeo_id.exists' => $database->table('ubigeo_districts')->where('id', $request->ubigeo_id)->exists()
]);

if (count($errors) > 0) {
    echo json_encode([
        'errors' => $errors,
        'success' => false
    ]);
    die();
}

$customer = Customer::create($request->only([
    'risk_profile_id',
    'client_type',
    'dni',
    'direc',
    'correo',
    'telefono',
    'rubro',
    'cel',
    'nom',
    'ap',
    'am',
    'fec_nac',
    'n_hijos',
    'grado_inst',
    'estado_civil',
    'lugar_nac',
    'referencia',
    'tipo',
    'comentario',
    'sexo',
    'idU',
    'coordinate_lat',
    'coordinate_lng',
    'ubigeo_id'
]) + [
    'idO' => 1,
    'status' => 'ACTIVE'
]);
$customer->risk_profile = $customer->riskProfile;
$customer->ubigeo = Ubigeo::join('ubigeo_provinces', 'ubigeo_districts.province_id', 'ubigeo_provinces.id')
    ->join('ubigeo_departments', 'ubigeo_provinces.department_id', 'ubigeo_departments.id')
    ->select(
        'ubigeo_districts.id',
        'ubigeo_districts.name as district',
        'ubigeo_provinces.name as province',
        'ubigeo_departments.name as department'
    )
    ->selectRaw('concat_ws(" ",ubigeo_departments.name, ubigeo_provinces.name, ubigeo_districts.name) as full_name')
    ->where('ubigeo_districts.id', $customer->ubigeo_id)
    ->first();

echo json_encode([
    'data' => $customer,
    'success' => true
]);
    }
}
