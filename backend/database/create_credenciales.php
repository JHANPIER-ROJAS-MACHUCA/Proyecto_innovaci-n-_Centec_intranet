<?php
// Crea 1 credencial por rol (tablas CENTECPC). Idempotente por DNI.
// Login por DNI. Uso: php database/create_credenciales.php <clave>
// (la clave NO se guarda en el repo; se pasa por argumento o env SEED_PASS)
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../utils/Password.php';

$PASS = $argv[1] ?? getenv('SEED_PASS') ?: null;
if (!$PASS) { echo "Uso: php database/create_credenciales.php <clave>\n"; exit(1); }
$TEST = [
    ['dni' => '10000001', 'nom' => 'TI',         'ap' => 'SISTEMA',     'am' => 'CENTECP', 'idRol' => 1, 'suc' => null],
    ['dni' => '10000002', 'nom' => 'GERENCIA',   'ap' => 'GENERAL',     'am' => 'CENTECP', 'idRol' => 8, 'suc' => null],
    ['dni' => '10000003', 'nom' => 'ADMIN',      'ap' => 'SUCURSAL',    'am' => 'CENTECP', 'idRol' => 5, 'suc' => true],
    ['dni' => '10000004', 'nom' => 'PLATAFORMA', 'ap' => 'OPERADOR',    'am' => 'CENTECP', 'idRol' => 7, 'suc' => true],
    ['dni' => '10000005', 'nom' => 'ASESOR',     'ap' => 'COMERCIAL',   'am' => 'CENTECP', 'idRol' => 2, 'suc' => true],
    ['dni' => '10000006', 'nom' => 'SEGUIMIENTO','ap' => 'COBRANZAS',   'am' => 'CENTECP', 'idRol' => 9, 'suc' => true],
    ['dni' => '10000007', 'nom' => 'CLIENTE',    'ap' => 'PRUEBA',      'am' => 'CENTECP', 'idRol' => 3, 'suc' => null],
];

global $capsule;
$db = $capsule->getConnection();
$sucId = $db->table('sucursales')->where('estado', 1)->orderBy('idS')->value('idS');

foreach ($TEST as $t) {
    if (UserRepository::dniExists($t['dni'])) { echo "EXISTE {$t['dni']}\n"; continue; }
    $u = UserRepository::create([
        'userU' => $t['dni'], 'pass' => Password::hash($PASS),
        'idRol' => $t['idRol'], 'esSuperAdmin' => $t['idRol'] === 1 ? 1 : 0,
        'dniU' => $t['dni'], 'nomU' => $t['nom'], 'apU' => $t['ap'], 'amU' => $t['am'],
    ]);
    $idU = $u->idU;
    if ($t['suc'] && $sucId && !$db->table('usuario_sucursal')->where('idU', $idU)->exists()) {
        $db->table('usuario_sucursal')->insert([
            'idS' => $sucId, 'idU' => $idU, 'estado' => 1,
            'tipo_asignacion' => 'principal', 'fecha_inicio' => date('Y-m-d'),
        ]);
    }
    echo "CREADO {$t['dni']} rol={$t['idRol']} idU=$idU\n";
}
echo "Clave común: $PASS\n";
