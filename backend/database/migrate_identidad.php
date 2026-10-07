<?php
// Migración identidad CENTECPC → Intranet.
// Crea con DDL REAL origen: tusuarios, tdatosu, notificaciones_cliente,
// notificaciones_asesor, solicitudes_prestamo, solicitudes_cambio_eval.
// Migra los usuarios de `tusuario` (legacy) preservando idU (referenciado por
// tprestamo/tcaja_*): userU=dniU, pass md5 intacto (Password verifica md5+bcrypt).
// Mapeo tipoU→idRol: 1→8 Gerente, 2→5 admin_personal, 3→7 Plataforma,
// 4→2 Asesor, 5→5, 7→1 Super Admin. estadoU 1→idEstado 1, otro→2.
// Agrega rol 9 Seguimiento (no existe en CENTECPC) y FKs entre tablas nuevas.
// `tusuario` legacy NO se borra (rollback). Uso: php database/migrate_identidad.php
require_once __DIR__ . '/../core/bootstrap.php';

global $capsule;
$db = $capsule->getConnection();
$log = [];

$ddlFile = 'C:\\Users\\JOSE\\AppData\\Local\\Temp\\opencode\\ddl_identidad.sql';
if (!file_exists($ddlFile)) { echo "Falta ddl_identidad.sql\n"; exit(1); }
$ddl = file_get_contents($ddlFile);
// Origen trae CONSTRAINTs a `user_status` (no existe aquí) → se quitan del
// CREATE y se agregan solo los FK seguros al final. user_status se crea aparte.
$stripFk = function (string $create): string {
    $lines = explode("\n", $create);
    $keep = [];
    foreach ($lines as $ln) {
        if (stripos($ln, 'CONSTRAINT') !== false) continue;
        $keep[] = $ln;
    }
    $sql = implode("\n", $keep);
    return preg_replace('/,\s*\n\)/', "\n)", $sql);
};
$nT = 0;
foreach (preg_split('/;\s*\n/', $ddl) as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '' || stripos(ltrim($stmt), 'CREATE TABLE') !== 0) continue;
    $nombre = preg_match('/CREATE TABLE `([^`]+)`/', $stmt, $mm) ? $mm[1] : '?';
    try { $db->statement($stripFk($stmt) . ';'); echo "OK tabla: $nombre\n"; $nT++; }
    catch (\Throwable $e) { echo 'AVISO ' . $nombre . ': ' . mb_substr($e->getMessage(), 0, 150) . "\n"; }
}
// Catálogo user_status (origen: 1 ACTIVO, 2 INACTIVO, 3 SUSPENDIDO, 4 ELIMINADO)
$usFile = 'C:\\Users\\JOSE\\AppData\\Local\\Temp\\opencode\\ddl_userstatus.sql';
if (file_exists($usFile) && !$db->getSchemaBuilder()->hasTable('user_status')) {
    foreach (preg_split('/;\s*\n/', file_get_contents($usFile)) as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try { $db->statement($stmt . ';'); } catch (\Throwable $e) { echo 'AVISO user_status: ' . mb_substr($e->getMessage(), 0, 120) . "\n"; }
    }
    echo "OK tabla+datos: user_status\n";
}
if (!$db->getSchemaBuilder()->hasTable('tusuarios')) { echo "ERROR: tusuarios no se creó, abortando\n"; exit(1); }

// Rol 9 Seguimiento (spec; CENTECPC llega hasta 8)
try {
    $db->table('rol')->insert(['id' => 9, 'tipo' => 'Seguimiento', 'descripcion' => 'Seguimiento de créditos y recuperación de cartera', 'estado' => 1]);
    $log[] = 'OK rol 9 Seguimiento';
} catch (\Throwable $e) { $log[] = 'AVISO rol 9: ' . $e->getMessage(); }

$mapRol = [1 => 8, 2 => 5, 3 => 7, 4 => 2, 5 => 5, 7 => 1];
$users = $db->table('tusuario')->orderBy('idU')->get();
$nU = 0; $nD = 0;
foreach ($users as $u) {
    $u = (array) $u;
    $idRol = $mapRol[(int) $u['tipoU']] ?? 2;
    $userU = trim((string) ($u['dniU'] ?? ''));
    if ($userU === '') $userU = 'user' . $u['idU'];
    $ex = $db->table('tusuarios')->where('userU', $userU)->where('idU', '!=', $u['idU'])->first();
    if ($ex) $userU .= '_' . $u['idU'];
    if (!$db->table('tusuarios')->where('idU', $u['idU'])->exists()) {
        $db->table('tusuarios')->insert([
            'idU' => $u['idU'], 'userU' => $userU, 'pass' => $u['pass'],
            'idRol' => $idRol, 'esSuperAdmin' => ((int) $u['tipoU'] === 7) ? 1 : 0,
            'idEstado' => ((string) $u['estadoU'] === '1') ? 1 : 2,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);
        $nU++;
    }
    if (!$db->table('tdatosu')->where('idU', $u['idU'])->exists()) {
        $db->table('tdatosu')->insert([
            'idU' => $u['idU'], 'dniU' => $u['dniU'] ?? '', 'amU' => $u['amU'] ?? '',
            'apU' => $u['apU'] ?? '', 'nomU' => $u['nomU'] ?? '', 'celU' => $u['celU'] ?? '',
            'direcU' => is_string($u['direcU'] ?? null) ? mb_substr($u['direcU'], 0, 500) : null,
            'correoU' => $u['correoU'] ?? '', 'fotoU' => $u['img'] ?? null,
        ]);
        $nD++;
    }
}
$log[] = "Usuarios migrados: $nU tusuarios, $nD tdatosu (de " . count($users) . " en tusuario)";

// FKs (solo tablas nuevas/vacías; cada una en try por seguridad)
$fks = [
    'tusuarios → rol' => 'ALTER TABLE `tusuarios` ADD CONSTRAINT `fk_tusuarios_rol` FOREIGN KEY (`idRol`) REFERENCES `rol` (`id`)',
    'tusuarios → user_status' => 'ALTER TABLE `tusuarios` ADD CONSTRAINT `fk_tusuarios_estado` FOREIGN KEY (`idEstado`) REFERENCES `user_status` (`id`)',
    'tdatosu → tusuarios' => 'ALTER TABLE `tdatosu` ADD CONSTRAINT `fk_datosu_user` FOREIGN KEY (`idU`) REFERENCES `tusuarios` (`idU`) ON DELETE CASCADE',
    'usuario_sucursal → tusuarios' => 'ALTER TABLE `usuario_sucursal` ADD CONSTRAINT `fk_us_user` FOREIGN KEY (`idU`) REFERENCES `tusuarios` (`idU`) ON DELETE CASCADE',
    'usuario_sucursal → sucursales' => 'ALTER TABLE `usuario_sucursal` ADD CONSTRAINT `fk_us_suc` FOREIGN KEY (`idS`) REFERENCES `sucursales` (`idS`) ON DELETE CASCADE',
    'rbac_rol_permiso → rbac_roles' => 'ALTER TABLE `rbac_rol_permiso` ADD CONSTRAINT `fk_rbac_rp_rol` FOREIGN KEY (`idRol`) REFERENCES `rbac_roles` (`codigo`) ON DELETE CASCADE',
    'rbac_rol_permiso → rbac_permisos' => 'ALTER TABLE `rbac_rol_permiso` ADD CONSTRAINT `fk_rbac_rp_perm` FOREIGN KEY (`idPermiso`) REFERENCES `rbac_permisos` (`id`) ON DELETE CASCADE',
];
foreach ($fks as $nombre => $sql) {
    try { $db->statement($sql); $log[] = "OK FK $nombre"; }
    catch (\Throwable $e) { $log[] = "AVISO FK $nombre: " . mb_substr($e->getMessage(), 0, 120); }
}
echo implode("\n", $log) . "\n";
