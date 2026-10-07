<?php
// Seed RBAC — Regla: Usuario → Rol → Permisos → Módulos → Vistas → Acciones.
// Roles = códigos CENTECPC tabla `rol` (los usan cookie tuser y tusuarios.idRol):
//   8 GERENCIA · 1 TI/SUPER ADMIN · 5 ADMIN SUCURSAL (admin_personal) ·
//   7 PLATAFORMA · 2 ASESOR · 9 SEGUIMIENTO (fila creada en migrate_identidad) ·
//   3 CLIENTE. (4 Postulante y 6 Soporte Web existen pero sin spec.)
// ATENCIÓN: NO confundir con el antiguo tipoU legacy (1=GERENTE, 2=ADMIN...),
// ya migrado a estos códigos en migrate_identidad.php.
// Jerarquía operativa: 1 GERENCIA > 7 TI > 2 ADMIN_SUC > 3/4/6 staff > 8 CLIENTE.
// Jerarquía financiera: CAJA GERENCIA > CAJA TI > CAJA SUCURSAL > OPERATIVAS.
// Uso: php database/seed_rbac.php  (idempotente: borra e inserta)
require_once __DIR__ . '/../core/bootstrap.php';

global $capsule;
$db = $capsule->getConnection();

$db->statement("CREATE TABLE IF NOT EXISTS rbac_roles (
  codigo TINYINT NOT NULL PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  nivel_operativo TINYINT NOT NULL DEFAULT 4,
  nivel_financiero TINYINT NOT NULL DEFAULT 4,
  estado TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$db->statement("CREATE TABLE IF NOT EXISTS rbac_permisos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  modulo VARCHAR(40) NOT NULL,
  vista VARCHAR(60) NOT NULL,
  accion VARCHAR(20) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_perm (modulo, vista, accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$db->statement("CREATE TABLE IF NOT EXISTS rbac_rol_permiso (
  idRol TINYINT NOT NULL,
  idPermiso INT NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (idRol, idPermiso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$ROLES = [
    [8, 'GERENCIA', 'Visión global: control financiero, BI y decisiones', 1, 1],
    [1, 'TI / ADMIN SISTEMA', 'Administración técnica y estructural global', 2, 2],
    [5, 'ADMINISTRADOR SUCURSAL', 'Opera su sucursal y supervisa personal', 3, 3],
    [7, 'PLATAFORMA', 'Atención al cliente y operaciones monetarias', 4, 4],
    [2, 'ASESOR', 'Captación, productos financieros y gestión comercial', 4, 4],
    [9, 'SEGUIMIENTO / COBRANZAS', 'Seguimiento de créditos y recuperación', 4, 4],
    [3, 'CLIENTE', 'Solo consulta de su información financiera', 5, 5],
];

// La matriz $M abajo se escribió con códigos legacy; se re-mapean aquí:
// legacy 1→8, 2→5, 3→7, 4→2, 6→9, 7→1, 8→3.
$REMAP = [1 => 8, 2 => 5, 3 => 7, 4 => 2, 5 => 5, 6 => 9, 7 => 1, 8 => 3];

// [rol, modulo, vista, accion, descripcion]
$M = [
[7,'dashboard','general','ver','Resumen general del sistema'],
[7,'sucursales','registro','crear','Registrar sucursal + código único'],
[7,'sucursales','registro','editar','Editar sucursal'],
[7,'sucursales','registro','estado','Activar/desactivar sucursal'],
[7,'sucursales','consulta','ver','Consultar información de sucursales'],
[7,'sucursales','financiero','ver','Estado financiero de cada sucursal'],
[7,'usuarios','registro','crear','Registrar usuarios'],
[7,'usuarios','registro','editar','Editar usuarios'],
[7,'usuarios','registro','estado','Activar/desactivar usuarios'],
[7,'usuarios','credenciales','reset','Restablecer contraseñas'],
[7,'usuarios','sucursal','asignar','Asignar usuario a sucursal'],
[7,'usuarios','rol','asignar','Asignar rol'],
[7,'usuarios','historial','ver','Historial del usuario'],
[7,'roles','gestion','crear','Crear roles'],
[7,'roles','gestion','editar','Editar roles'],
[7,'roles','gestion','estado','Activar/desactivar permisos'],
[7,'roles','permisos','asignar','Asignar permisos y módulos por rol'],
[7,'cajas','gestion','crear','Crear cajas'],
[7,'cajas','asignacion','asignar','Asignar caja a sucursal y responsable'],
[7,'cajas','operacion','abrir','Abrir cajas'],
[7,'cajas','operacion','cerrar','Cerrar cajas'],
[7,'cajas','movimientos','ver','Consultar movimientos'],
[7,'cajas','saldo','ver','Consultar saldo'],
[7,'cajas','supervision','ver','Supervisar cajas de todas las sucursales'],
[7,'fondos','recepcion','mover','Recibir fondos del nivel superior'],
[7,'fondos','asignacion','asignar','Asignar fondos a sucursales'],
[7,'fondos','transferencia','transferir','Registrar transferencias'],
[7,'fondos','disponible','ver','Consultar fondos disponibles'],
[7,'fondos','historial','ver','Historial de asignaciones'],
[7,'auditoria','registro','ver','Historial, usuarios, cambios, cajas, transferencias, errores'],
[7,'config','general','ver','Ver configuración'],
[7,'config','general','editar','Editar configuración'],
[1,'dashboard','general','ver','Dashboard gerencial y KPIs'],
[1,'caja_general','saldo','ver','Consultar saldo de caja general'],
[1,'caja_general','fondos','mover','Recibir dinero y entregar fondos a TI'],
[1,'caja_general','ingresos','crear','Registrar ingresos'],
[1,'caja_general','egresos','crear','Registrar egresos'],
[1,'caja_general','movimientos','ver','Consultar movimientos'],
[1,'caja_general','cierre','cerrar','Cierre de caja general'],
[1,'finanzas','fondos','ver','Fondos disponibles, entregados y por sucursal'],
[1,'finanzas','movimientos','ver','Movimientos financieros'],
[1,'finanzas','comparativa','ver','Comparación entre sucursales'],
[1,'finanzas','transferencias','ver','Historial de transferencias'],
[1,'creditos','general','ver','Consultar créditos'],
[1,'creditos','aprobados','ver','Créditos aprobados'],
[1,'creditos','desembolsados','ver','Créditos desembolsados'],
[1,'creditos','pendientes','ver','Créditos pendientes'],
[1,'creditos','vencidos','ver','Créditos vencidos'],
[1,'creditos','cartera-sucursal','ver','Cartera por sucursal'],
[1,'creditos','cartera-asesor','ver','Cartera por asesor'],
[1,'ahorros','cuentas','ver','Consultar cuentas'],
[1,'ahorros','depositos','ver','Depósitos'],
[1,'ahorros','retiros','ver','Retiros'],
[1,'ahorros','saldos','ver','Saldos'],
[1,'ahorros','evolucion','ver','Evolución de ahorros'],
[1,'cobranzas','total','ver','Cobranza total'],
[1,'cobranzas','por-sucursal','ver','Cobranza por sucursal'],
[1,'cobranzas','por-asesor','ver','Cobranza por asesor'],
[1,'cobranzas','vencidas','ver','Cuotas vencidas'],
[1,'cobranzas','morosidad','ver','Morosidad'],
[1,'cobranzas','recuperacion','ver','Recuperación de cartera'],
[1,'reportes','financieros','ver','Reportes financieros'],
[1,'reportes','creditos','ver','Reportes de créditos'],
[1,'reportes','ahorros','ver','Reportes de ahorros'],
[1,'reportes','cobranza','ver','Reportes de cobranza'],
[1,'reportes','sucursales','ver','Reportes de sucursales'],
[1,'reportes','rendimiento','ver','Reportes de rendimiento'],
[1,'reportes','indicadores','ver','Indicadores BI'],
[2,'dashboard','general','ver','Dashboard de sucursal'],
[2,'personal','trabajadores','ver','Ver trabajadores de la sucursal'],
[2,'personal','funciones','asignar','Asignar funciones'],
[2,'personal','asesores','ver','Consultar asesores'],
[2,'personal','seguimiento','ver','Consultar personal de seguimiento'],
[2,'personal','plataforma','ver','Consultar personal de plataforma'],
[2,'clientes','registro','crear','Registrar cliente'],
[2,'clientes','consulta','ver','Consultar clientes'],
[2,'clientes','registro','editar','Editar información'],
[2,'clientes','registro','estado','Activar / bloquear cliente'],
[2,'clientes','historial','ver','Historial del cliente'],
[2,'cajas','operacion','abrir','Abrir caja de Plataforma/Asesor/Seguimiento'],
[2,'cajas','saldo','ver','Consultar saldos'],
[2,'cajas','fondos','asignar','Asignar fondos'],
[2,'cajas','movimientos','ver','Revisar movimientos'],
[2,'cajas','cierres','supervisar','Supervisar cierres'],
[2,'cajas','faltantes','ver','Revisar faltantes y sobrantes'],
[2,'fondos','recepcion','mover','Recibir fondos de TI'],
[2,'fondos','distribucion','asignar','Distribuir fondos al personal autorizado'],
[2,'fondos','disponible','ver','Fondos sin asignar'],
[2,'creditos','solicitudes','ver','Consultar solicitudes'],
[2,'creditos','revision','ver','Revisar créditos y estados'],
[2,'creditos','desembolsos','supervisar','Supervisar desembolsos'],
[2,'creditos','cartera','ver','Consultar cartera'],
[2,'creditos','morosidad','ver','Consultar morosidad'],
[2,'cobranzas','cobros','ver','Consultar cobros'],
[2,'cobranzas','supervision','ver','Supervisar cobranzas'],
[2,'cobranzas','faltantes','ver','Revisar faltantes'],
[2,'cobranzas','historial','ver','Historial de cobranza'],
[2,'reportes','caja','ver','Reporte de caja'],
[2,'reportes','clientes','ver','Reporte de clientes'],
[2,'reportes','movimientos','ver','Reporte de movimientos'],
[3,'dashboard','general','ver','Dashboard de plataforma'],
[3,'clientes','busqueda','ver','Buscar cliente'],
[3,'clientes','consulta','ver','Consultar información y estado'],
[3,'clientes','registro','crear','Registrar cliente'],
[3,'clientes','registro','editar','Actualizar información'],
[3,'clientes','estado','ver','Consultar estado del cliente'],
[3,'cuentas','apertura','crear','Abrir cuenta'],
[3,'cuentas','consulta','ver','Consultar cuenta'],
[3,'cuentas','depositos','crear','Realizar depósitos'],
[3,'cuentas','retiros','crear','Realizar retiros'],
[3,'cuentas','saldo','ver','Consultar saldo'],
[3,'cuentas','movimientos','ver','Consultar movimientos'],
[3,'cuentas','estado-cuenta','imprimir','Imprimir estado de cuenta'],
[3,'cobranza','registro','crear','Registrar cobro'],
[3,'cobranza','credito','ver','Buscar crédito'],
[3,'cobranza','pagos','crear','Registrar pago de cuota'],
[3,'cobranza','comprobante','imprimir','Generar comprobante'],
[3,'cobranza','realizados','ver','Cobros realizados'],
[3,'cobranza','historial','imprimir','Imprimir historial'],
[3,'desembolsos','autorizados','ver','Créditos autorizados'],
[3,'desembolsos','registro','crear','Realizar desembolso'],
[3,'desembolsos','metodo','crear','Registrar método de desembolso'],
[3,'desembolsos','comprobante','imprimir','Generar comprobante'],
[3,'desembolsos','historial','ver','Historial de desembolsos'],
[3,'caja','saldo','ver','Consultar saldo de caja'],
[3,'caja','movimientos','ver','Consultar movimientos'],
[3,'caja','operaciones','crear','Registrar operaciones autorizadas'],
[3,'caja','cierre','solicitar','Solicitar cierre'],
[3,'caja','resumen','imprimir','Imprimir resumen de caja'],
[4,'dashboard','general','ver','Dashboard del asesor'],
[4,'prospectos','registro','crear','Registrar prospecto'],
[4,'prospectos','registro','editar','Editar prospecto'],
[4,'prospectos','busqueda','ver','Buscar prospecto'],
[4,'prospectos','interes','crear','Registrar interés'],
[4,'prospectos','seguimiento','programar','Programar seguimiento'],
[4,'prospectos','cliente','convertir','Convertir prospecto en cliente'],
[4,'clientes','asignados','ver','Consultar clientes asignados'],
[4,'clientes','registro','crear','Registrar cliente'],
[4,'clientes','registro','editar','Actualizar información'],
[4,'clientes','historial','ver','Historial del cliente'],
[4,'clientes','visitas','crear','Registrar visitas'],
[4,'ahorros','productos','ver','Consultar productos de ahorro'],
[4,'ahorros','interes','crear','Registrar interés del cliente'],
[4,'ahorros','solicitud','crear','Crear solicitud de cuenta'],
[4,'ahorros','cuentas','ver','Consultar cuentas de clientes'],
[4,'creditos','solicitud','crear','Registrar solicitud (tipo, monto, plazo)'],
[4,'creditos','requisitos','crear','Adjuntar requisitos'],
[4,'creditos','estado','ver','Consultar estado'],
[4,'creditos','evaluacion','crear','Evaluación inicial'],
[4,'seguimiento','visitas','programar','Programar visitas'],
[4,'seguimiento','llamadas','crear','Registrar llamadas'],
[4,'seguimiento','reuniones','crear','Registrar reuniones'],
[4,'seguimiento','observaciones','crear','Registrar observaciones'],
[4,'seguimiento','historial','ver','Historial del cliente'],
[4,'cobranza','asignadas','ver','Cuotas asignadas'],
[4,'cobranza','morosos','ver','Clientes morosos'],
[4,'cobranza','gestion','crear','Gestión de cobranza'],
[4,'cobranza','visita','crear','Registrar visita'],
[4,'cobranza','compromiso','crear','Compromiso de pago'],
[4,'caja','saldo','ver','Saldo asignado'],
[4,'caja','operaciones','crear','Operaciones autorizadas'],
[4,'caja','movimientos','ver','Consultar movimientos'],
[4,'caja','fondos','mover','Entregar fondos al siguiente nivel'],
[4,'caja','cierre','solicitar','Solicitar cierre de caja'],
[6,'dashboard','general','ver','Dashboard de seguimiento'],
[6,'cartera','consulta','ver','Cartera (filtros: estado, asesor, cliente, vencimiento, saldo)'],
[6,'cuotas','consulta','ver','Consultar cuotas'],
[6,'cuotas','proximas','ver','Cuotas próximas a vencer'],
[6,'cuotas','vencidas','ver','Cuotas vencidas'],
[6,'cuotas','atraso','ver','Días de atraso y monto pendiente'],
[6,'gestion','llamadas','crear','Registrar llamada'],
[6,'gestion','visitas','crear','Registrar visita'],
[6,'gestion','mensajes','crear','Registrar mensaje'],
[6,'gestion','compromisos','crear','Compromiso de pago'],
[6,'gestion','acuerdos','crear','Registrar acuerdo'],
[6,'gestion','resultados','crear','Resultado de gestión'],
[6,'historial','cliente','ver','Historial del cliente (créditos, pagos, gestiones)'],
[6,'cobranza','registro','crear','Registrar cobro autorizado'],
[6,'cobranza','comprobante','imprimir','Generar comprobante'],
[6,'cobranza','realizados','ver','Cobros realizados'],
[6,'cobranza','historial','ver','Historial de cobros'],
[6,'caja','saldo','ver','Consultar saldo'],
[6,'caja','movimientos','ver','Consultar movimientos'],
[6,'caja','operaciones','crear','Operaciones permitidas'],
[6,'caja','cierre','solicitar','Solicitar cierre'],
[8,'inicio','resumen','ver','Resumen de productos'],
[8,'perfil','datos','ver','Datos personales, contacto y sucursal'],
[8,'cuentas','numero','ver','Número de cuenta'],
[8,'cuentas','tipo','ver','Tipo de ahorro'],
[8,'cuentas','saldo','ver','Saldo'],
[8,'cuentas','movimientos','ver','Movimientos'],
[8,'cuentas','estado-cuenta','ver','Estado de cuenta'],
[8,'creditos','detalle','ver','Crédito: monto, saldo, tasa, plazo'],
[8,'creditos','proxima-cuota','ver','Próxima cuota'],
[8,'cuotas','pagadas','ver','Cuotas pagadas'],
[8,'cuotas','pendientes','ver','Cuotas pendientes'],
[8,'cuotas','vencidas','ver','Cuotas vencidas y fechas'],
[8,'comprobantes','depositos','ver','Comprobantes de depósitos'],
[8,'comprobantes','pagos','ver','Comprobantes de pagos'],
[8,'comprobantes','operaciones','ver','Comprobantes de operaciones'],
];

$db->table('rbac_rol_permiso')->delete();
$db->table('rbac_permisos')->delete();
$db->table('rbac_roles')->delete();

foreach ($ROLES as $r) {
    $db->table('rbac_roles')->insert([
        'codigo' => $r[0], 'nombre' => $r[1], 'descripcion' => $r[2],
        'nivel_operativo' => $r[3], 'nivel_financiero' => $r[4], 'estado' => 1,
    ]);
}
$ids = [];
foreach ($M as $m) {
    [$rol, $mod, $vis, $acc, $desc] = $m;
    $rol = $REMAP[$rol] ?? $rol;
    $key = "$mod.$vis.$acc";
    if (!isset($ids[$key])) {
        $id = $db->table('rbac_permisos')->insertGetId([
            'modulo' => $mod, 'vista' => $vis, 'accion' => $acc,
            'descripcion' => $desc, 'estado' => 1,
        ]);
        $ids[$key] = $id;
    }
    $db->table('rbac_rol_permiso')->insert([
        'idRol' => $rol, 'idPermiso' => $ids[$key], 'estado' => 1,
    ]);
}
$nP = $db->table('rbac_permisos')->count();
$nA = $db->table('rbac_rol_permiso')->count();
echo "RBAC OK: 7 roles, $nP permisos, $nA asignaciones\n";
