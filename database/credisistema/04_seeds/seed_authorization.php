<?php
/**
 * Seeder del Sistema de Autorizacion CENTECPC
 * Ejecucion:  php database/credisistema/04_seeds/seed_authorization.php [idU_superadmin]
 * (o via database/credisistema/INSTALAR.bat)
 *
 * - Crea el catalogo completo de permisos (modulo.accion)
 * - Asigna rol_permisos con alcance para cada rol
 * - Opcionalmente promueve un admin existente como Super Admin
 *
 * Idempotente: puede ejecutarse multiples veces sin duplicar.
 */

$backend = realpath(__DIR__ . '/../../../backend');
if (!$backend || !is_file($backend . '/vendor/autoload.php')) { echo "ERROR: no se encuentra backend/\n"; exit(1); }
require $backend . '/vendor/autoload.php';

\CrediSoporte\Core\Bootstrap::boot($backend);

use CrediSoporte\Core\Db;

// ===================== CATÁLOGO DE PERMISOS =====================
// [modulo.accion => descripcion]
$permisos = [
    // Dashboard
    'dashboard.ver'              => 'Ver panel principal (cpanel)',

    // Usuarios
    'usuarios.ver'               => 'Ver lista de usuarios',
    'usuarios.crear'             => 'Crear nuevos usuarios',
    'usuarios.editar'            => 'Editar usuarios existentes',
    'usuarios.eliminar'          => 'Eliminar/restaurar usuarios',
    'usuarios.permisos'          => 'Gestionar roles y permisos',
    'usuarios.estado'            => 'Cambiar estado de usuarios',

    // Clientes
    'clientes.ver'               => 'Ver lista de clientes',
    'clientes.crear'             => 'Registrar nuevos clientes',
    'clientes.editar'            => 'Editar clientes existentes',
    'clientes.eliminar'          => 'Eliminar clientes',
    'clientes.asignar'           => 'Asignar asesor a cliente',

    // Creditos
    'creditos.ver'               => 'Ver creditos/prestamos',
    'creditos.crear'             => 'Crear solicitudes de credito',
    'creditos.editar'            => 'Editar creditos',
    'creditos.aprobar'           => 'Aprobar/rechazar creditos',

    // Pagos / Caja
    'pagos.ver'                  => 'Ver historial de pagos',
    'pagos.registrar'            => 'Registrar pagos',
    'pagos.editar'               => 'Editar pagos registrados',
    'pagos.anular'               => 'Anular pagos',
    'pagos.caja'                 => 'Abrir/cajar caja',
    'pagos.validar'              => 'Validar pagos (plataforma)',
    'pagos.historial'            => 'Ver historial completo de pagos',

    // Sucursales
    'sucursales.ver'             => 'Ver sucursales',
    'sucursales.crear'           => 'Crear sucursales',
    'sucursales.editar'          => 'Editar sucursales',
    'sucursales.eliminar'        => 'Eliminar sucursales',

    // Equipos (asesores para la web)
    'equipos.ver'                => 'Ver equipo de asesores',
    'equipos.editar'             => 'Editar equipo (resaltado, fotos)',

    // Tipos de credito
    'tipos_credito.ver'          => 'Ver tipos de credito',
    'tipos_credito.crear'        => 'Crear tipos de credito',
    'tipos_credito.editar'       => 'Editar tipos de credito',
    'tipos_credito.eliminar'     => 'Eliminar tipos de credito',

    // Galeria
    'galeria.ver'                => 'Ver galeria',
    'galeria.crear'              => 'Crear imagenes en galeria',
    'galeria.editar'             => 'Editar imagenes',
    'galeria.eliminar'           => 'Eliminar imagenes',

    // Novedades
    'novedades.ver'              => 'Ver novedades',
    'novedades.crear'            => 'Crear novedades',
    'novedades.editar'           => 'Editar novedades',
    'novedades.eliminar'         => 'Eliminar novedades',

    // Bienes en venta
    'bienes_venta.ver'           => 'Ver bienes en venta',
    'bienes_venta.crear'         => 'Crear bienes en venta',
    'bienes_venta.editar'        => 'Editar bienes en venta',
    'bienes_venta.eliminar'      => 'Eliminar bienes en venta',

    // Home / pagina web
    'home.carrusel'              => 'Gestionar carrusel',
    'home.secciones'             => 'Gestionar secciones del home',
    'home.header'                => 'Gestionar header',
    'home.footer'                => 'Gestionar footer',
    'home.colores'               => 'Gestionar colores del sitio',

    // Seguros
    'seguros.ver'                => 'Ver seguros',
    'seguros.crear'              => 'Crear seguros',
    'seguros.editar'             => 'Editar seguros',
    'seguros.eliminar'           => 'Eliminar seguros',

    // Ahorros
    'ahorros.ver'                => 'Ver ahorros',
    'ahorros.crear'              => 'Crear productos de ahorro',
    'ahorros.editar'             => 'Editar ahorros',
    'ahorros.eliminar'           => 'Eliminar ahorros',

    // Metodos de pago
    'metodos_pago.ver'           => 'Ver metodos de pago',
    'metodos_pago.crear'         => 'Crear metodos de pago',
    'metodos_pago.editar'        => 'Editar metodos de pago',
    'metodos_pago.eliminar'      => 'Eliminar metodos de pago',

    // Reportes
    'reportes.ver'               => 'Ver reportes',
    'reportes.generar'           => 'Generar/exportar reportes',

    // Configuracion
    'configuracion.sistema'      => 'Configuracion general del sistema',
    'configuracion.evaluador'    => 'Configurar evaluador crediticio',

    // Reclamos
    'reclamos.ver'               => 'Ver seguimiento de reclamos',
    'reclamos.atender'           => 'Responder/reclamos',

    // Libro de reclamaciones
    'libro_reclamos.ver'         => 'Ver libro de reclamaciones',
    'libro_reclamos.atender'     => 'Atender reclamaciones',
    'libro_reclamos.responder'   => 'Enviar respuesta formal',

    // Vacantes
    'vacantes.ver'               => 'Ver convocatorias',
    'vacantes.crear'             => 'Crear convocatorias',
    'vacantes.editar'            => 'Editar convocatorias',
    'vacantes.eliminar'          => 'Eliminar convocatorias',
    'vacantes.postulaciones'     => 'Ver postulaciones recibidas',
    'vacantes.config'            => 'Configurar textos de vacantes',

    // Evaluacion crediticia
    'evaluacion.ver'             => 'Ver evaluaciones crediticias',
    'evaluacion.crear'           => 'Crear evaluaciones',
    'evaluacion.editar'          => 'Editar evaluaciones',
    'evaluacion.eliminar'        => 'Eliminar evaluaciones',

    // Portal Cliente
    'cliente.dashboard'          => 'Acceder al panel del cliente',
    'cliente.prestamos'          => 'Ver mis prestamos',
    'cliente.pagos'              => 'Ver historial de pagos',
    'cliente.solicitar'          => 'Solicitar nuevo prestamo',
    'cliente.evaluaciones'       => 'Ver mis evaluaciones',

    // Portal Postulante
    'postulante.dashboard'       => 'Acceder al panel del postulante',
    'postulante.vacantes'        => 'Ver mis postulaciones',
];

// ===================== ASIGNACIONES POR ROL =====================
// [rol.id => [permiso => alcance]]
$asignaciones = [
    // 1 = Admin  (acceso total, alcance 'todas' salvo pagos.validar/historial de plataforma)
    1 => array_diff_key(
        array_fill_keys(array_keys($permisos), 'todas'),
        ['pagos.validar' => 1, 'pagos.historial' => 1]
    ),

    // 5 = Admin Personal (negocio: clientes, creditos, caja, sucursales ver/editar, vacantes, evaluacion, reportes, usuarios)
    5 => [
        'dashboard.ver'         => 'todas',
        'usuarios.ver'          => 'todas',
        'usuarios.crear'        => 'todas',
        'usuarios.editar'       => 'todas',
        'usuarios.estado'       => 'todas',
        'clientes.ver'          => 'todas',
        'clientes.crear'        => 'todas',
        'clientes.editar'       => 'todas',
        'creditos.ver'          => 'todas',
        'creditos.crear'        => 'todas',
        'creditos.editar'       => 'todas',
        'creditos.aprobar'      => 'todas',
        'pagos.ver'             => 'todas',
        'pagos.registrar'       => 'todas',
        'pagos.editar'          => 'todas',
        'pagos.anular'          => 'todas',
        'pagos.caja'            => 'todas',
        'sucursales.ver'        => 'todas',
        'tipos_credito.ver'     => 'todas',
        'tipos_credito.crear'   => 'todas',
        'tipos_credito.editar'  => 'todas',
        'bienes_venta.ver'      => 'todas',
        'bienes_venta.crear'    => 'todas',
        'bienes_venta.editar'   => 'todas',
        'reportes.ver'          => 'todas',
        'reportes.generar'      => 'todas',
        'configuracion.evaluador' => 'todas',
        'reclamos.ver'          => 'todas',
        'reclamos.atender'      => 'todas',
        'libro_reclamos.ver'    => 'todas',
        'libro_reclamos.atender'=> 'todas',
        'libro_reclamos.responder' => 'todas',
        'vacantes.ver'          => 'todas',
        'vacantes.crear'        => 'todas',
        'vacantes.editar'       => 'todas',
        'vacantes.postulaciones'=> 'todas',
        'vacantes.config'       => 'todas',
        'evaluacion.ver'        => 'todas',
        'evaluacion.crear'      => 'todas',
    ],

    // 6 = Soporte Web  (contenido pagina + metodos de pago alcalce sucursal)
    6 => [
        'home.carrusel'         => 'todas',
        'home.secciones'        => 'todas',
        'home.header'           => 'todas',
        'home.footer'           => 'todas',
        'home.colores'          => 'todas',
        'equipos.ver'           => 'todas',
        'seguros.ver'           => 'todas',
        'seguros.crear'         => 'todas',
        'seguros.editar'        => 'todas',
        'seguros.eliminar'      => 'todas',
        'ahorros.ver'           => 'todas',
        'ahorros.crear'         => 'todas',
        'ahorros.editar'        => 'todas',
        'ahorros.eliminar'      => 'todas',
        'metodos_pago.ver'      => 'sucursal',
        'metodos_pago.crear'    => 'sucursal',
        'metodos_pago.editar'   => 'sucursal',
        'metodos_pago.eliminar' => 'sucursal',
        'sucursales.ver'        => 'sucursal',
        'galeria.ver'           => 'todas',
        'galeria.crear'         => 'todas',
        'galeria.editar'        => 'todas',
        'galeria.eliminar'      => 'todas',
        'novedades.ver'         => 'todas',
        'novedades.crear'       => 'todas',
        'novedades.editar'      => 'todas',
        'novedades.eliminar'    => 'todas',
    ],

    // 2 = Asesor  (creditos propios, clientes propios, evaluacion propia)
    2 => [
        'dashboard.ver'         => 'todas',
        'creditos.ver'          => 'propio',
        'creditos.crear'        => 'propio',
        'clientes.ver'          => 'propio',
        'clientes.crear'        => 'propio',
        'evaluacion.ver'        => 'propio',
        'evaluacion.crear'      => 'propio',
    ],

    // 3 = Cliente  (portal)
    3 => [
        'cliente.dashboard'     => 'todas',
        'cliente.prestamos'     => 'todas',
        'cliente.pagos'         => 'todas',
        'cliente.solicitar'     => 'todas',
        'cliente.evaluaciones'  => 'todas',
    ],

    // 4 = Postulante  (portal)
    4 => [
        'postulante.dashboard'  => 'todas',
        'postulante.vacantes'   => 'todas',
    ],

    // 7 = Plataforma  (validar pagos)
    7 => [
        'pagos.validar'         => 'todas',
        'pagos.historial'       => 'todas',
    ],
];

// ===================== FUNCIONES AUXILIARES =====================

function getPermisoId(string $nombre): ?int
{
    $row = Db::query(
        "SELECT id FROM permisos WHERE modulo = ? AND accion = ? LIMIT 1",
        [substr($nombre, 0, strpos($nombre, '.')), substr($nombre, strpos($nombre, '.') + 1)]
    )->fetch();
    return $row ? (int) $row['id'] : null;
}

function ensurePermiso(string $nombre, string $desc): int
{
    [$modulo, $accion] = explode('.', $nombre, 2);
    $row = Db::query(
        "SELECT id FROM permisos WHERE modulo = ? AND accion = ? LIMIT 1",
        [$modulo, $accion]
    )->fetch();

    if ($row) {
        Db::query("UPDATE permisos SET descripcion = ?, estado = 1 WHERE id = ?", [$desc, $row['id']]);
        return (int) $row['id'];
    }

    Db::query(
        "INSERT INTO permisos (nombre, descripcion, modulo, accion, estado) VALUES (?, ?, ?, ?, 1)",
        [$nombre, $desc, $modulo, $accion]
    );
    return (int) Db::getConnection()->lastInsertId();
}

// ===================== EJECUCION =====================

echo "=== Seed Authorization: " . date('Y-m-d H:i:s') . " ===\n";

// 1) Asegurar todas las filas de permisos
echo "\n[1/3] Asegurando catalogo de permisos...\n";
$creados = 0;
foreach ($permisos as $nombre => $desc) {
    $id = ensurePermiso($nombre, $desc);
    $creados++;
    echo "  $nombre -> id=$id\n";
}
echo "  Total permisos: $creados\n";

// 2) Asignar rol_permisos
echo "\n[2/3] Asignando permisos por rol...\n";
foreach ($asignaciones as $rolId => $perms) {
    if (empty($perms)) continue;
    echo "  Rol $rolId: " . count($perms) . " permisos\n";
    foreach ($perms as $perm => $alcance) {
        $pid = getPermisoId($perm);
        if (!$pid) {
            echo "    SKIP $perm (no encontrado)\n";
            continue;
        }
        Db::query(
            "INSERT INTO rol_permisos (idRol, idPermiso, alcance) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE alcance = VALUES(alcance)",
            [$rolId, $pid, $alcance]
        );
    }
}

// 3) Super Admin (opcional)
$superId = isset($argv[1]) ? (int) $argv[1] : 0;
if ($superId > 0) {
    echo "\n[3/3] Promoviendo idU=$superId como Super Admin...\n";
    $exists = Db::query(
        "SELECT idU, idRol FROM tusuarios WHERE idU = ? AND idEstado = 1 LIMIT 1",
        [$superId]
    )->fetch();

    if (!$exists) {
        echo "  ERROR: idU=$superId no encontrado o inactivo\n";
    } elseif ((int) $exists['idRol'] !== 1) {
        echo "  ERROR: idU=$superId no tiene rol Admin (idRol=" . $exists['idRol'] . ")\n";
    } else {
        Db::query("UPDATE tusuarios SET esSuperAdmin = 1 WHERE idU = ?", [$superId]);
        echo "  OK: idU=$superId ahora es Super Admin\n";
    }
} else {
    echo "\n[3/3] Sin superadmin. Para promover ejecuta:\n";
    echo "  php database/credisistema/04_seeds/seed_authorization.php <idU_admin>\n";
}

echo "\n=== Listo ===\n";
