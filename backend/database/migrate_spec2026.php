<?php
// Migración spec ACTUALIZACIÓN 2026 (lote 1): clasificación, cartas, avance
// diario, bloqueos de caja. Uso: php database/migrate_spec2026.php
require_once __DIR__ . '/../core/bootstrap.php';

global $capsule;
$db = $capsule->getConnection();
$sch = $db->getSchemaBuilder();

// 1) Calificación Sentinel por cliente (carga manual; alimenta clasificación)
if (!$sch->hasColumn('tclie_general', 'sentinel')) {
    $db->statement("ALTER TABLE `tclie_general` ADD COLUMN `sentinel` VARCHAR(20) NULL DEFAULT 'NORMAL' AFTER `codigo_cliente`");
    echo "OK columna tclie_general.sentinel\n";
} else { echo "EXISTE tclie_general.sentinel\n"; }

// 2) Avance de cobros diarios (#4): snapshot consultable por día
$db->statement("CREATE TABLE IF NOT EXISTS `avance_cobro_diario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `idU` INT NOT NULL,
  `cobrado` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `cuota` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `mora` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `n_cobros` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_avance_dia_user` (`fecha`, `idU`),
  KEY `idx_avance_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
echo "OK tabla avance_cobro_diario\n";

// 3) Cartas generadas (invitación pre-aprobados + cobranza por tramo) (#1-clasif, #9)
$db->statement("CREATE TABLE IF NOT EXISTS `carta_cobranza` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idCG` INT NOT NULL,
  `tipo` VARCHAR(20) NOT NULL,
  `tramo` VARCHAR(40) DEFAULT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `contenido` TEXT NOT NULL,
  `idU` INT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_carta_clie` (`idCG`),
  KEY `idx_carta_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
echo "OK tabla carta_cobranza\n";

// 4) Permisos RBAC nuevos (spec 2026). uq_perm evita duplicados.
$PERMS = [
    ['clientes', 'clasificacion', 'ver', 'Reporte clasificación A/B/C'],
    ['clientes', 'sentinel', 'editar', 'Cargar calificación Sentinel'],
    ['clientes', 'morosidad', 'ver', 'Cuotas retrasadas + días de atraso'],
    ['cartas', 'invitacion', 'ver', 'Carta invitación pre-aprobados'],
    ['cartas', 'cobranza', 'ver', 'Carta de cobranza por tramo'],
    ['cartas', 'registro', 'crear', 'Registrar carta generada'],
    ['cobros', 'avance', 'ver', 'Avance de cobro por día'],
    ['cobros', 'avance', 'guardar', 'Guardar snapshot de avance'],
    ['caja', 'bloqueo', 'ver', 'Estado de bloqueo por billetaje'],
    ['boveda', 'asignacion', 'aceptar', 'Aceptar fondos asignados'],
    ['boveda', 'asignacion', 'eliminar', 'Eliminar asignación no aceptada'],
    ['gerencia', 'sucursales', 'ver', 'Estadística por sucursal + consolidado'],
];
// rol => [modulo.vista.accion...] ('*' = todos los nuevos)
$ASSIGN = [
    8 => ['*'], 1 => ['*'], 5 => ['*'],
    2 => ['clientes.morosidad.ver', 'cartas.cobranza.ver', 'cobros.avance.ver'],
    9 => ['clientes.morosidad.ver', 'cartas.cobranza.ver', 'cobros.avance.ver'],
];
foreach ($PERMS as $p) {
    $db->table('rbac_permisos')->insertOrIgnore([
        'modulo' => $p[0], 'vista' => $p[1], 'accion' => $p[2], 'descripcion' => $p[3], 'estado' => 1,
    ]);
}
$ids = [];
foreach ($db->table('rbac_permisos')->get() as $r) {
    $ids[$r->modulo . '.' . $r->vista . '.' . $r->accion] = $r->id;
}
foreach ($ASSIGN as $rol => $list) {
    $wants = ($list === ['*']) ? array_keys($ids) : $list;
    // solo los 12 nuevos para '*' también vale: filtrar por los de $PERMS
    $nuevos = [];
    foreach ($PERMS as $p) $nuevos[] = $p[0] . '.' . $p[1] . '.' . $p[2];
    if ($list === ['*']) $wants = $nuevos;
    foreach ($wants as $k) {
        if (!isset($ids[$k])) continue;
        $db->table('rbac_rol_permiso')->insertOrIgnore(['idRol' => $rol, 'idPermiso' => $ids[$k], 'estado' => 1]);
    }
}
echo "OK RBAC deltas: " . count($PERMS) . " permisos\n";
