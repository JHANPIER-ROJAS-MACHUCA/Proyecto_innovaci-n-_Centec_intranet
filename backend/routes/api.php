<?php

// Manifiesto central de la API modular.
// 1) Registra las rutas de cada modulo (src/Modules/*/routes.php).
// 2) Documenta los scripts legacy (app/api/*.php) que siguen operativos
//    por compatibilidad en su URL directa; la migracion se hace modulo por modulo.
//
// $router es CrediSoporte\Core\Router (inyectado por index.php).

$moduleRoutes = [
    __DIR__ . '/../src/Modules/Auth/routes.php',
    __DIR__ . '/../src/Modules/Users/routes.php',
    __DIR__ . '/../src/Modules/Customers/routes.php',
    __DIR__ . '/../src/Modules/Credits/routes.php',
    __DIR__ . '/../src/Modules/Cash/routes.php',
];

foreach ($moduleRoutes as $file) {
    if (is_file($file)) {
        require $file;
    }
}

// Mapa legacy -> modulo (solo documentacion; no se ejecuta aqui).
// El directorio app/ ya no existe: toda su logica vive en src/ y sus
// URLs historicas se sirven desde routes/legacy.php via el front controller.
const LEGACY_API_MAP = [
    'app/api/login.php, logout.php' => 'Modules/Auth/Legacy',
    'app/api/registrarUsuario.php, actualizarUsuario.php, toggleEstadoUsuario.php, restorePasswordUser.php, *Avatar.php' => 'Modules/Users/Legacy',
    'app/api/createMeta.php, updateMeta.php, deleteMeta.php' => 'Modules/Goals/Legacy',
    'app/api/uploadFile.php' => 'Modules/Files/Legacy',
    'app/api/crearCliente.php, editarCliente.php, actualizarCliente.php, search*.php, *Relationship.php, *Attachment*.php' => 'Modules/Customers/Legacy',
    'app/api/generarPrestamo.php, confirmarPrestamo.php, editarCredito.php, activarCredito.php, cancelarCredito.php, desembolsar.php, cobrarCredito.php, condonar*.php, *Justification*.php, dataCredit.php, detalleCredito.php, creditsByCustomer.php' => 'Modules/Credits/Legacy',
    'app/api/registrarAhorro.php, anularAhorro.php, eliminarTransaccion.php' => 'Modules/Cash/Legacy',
    'app/api/queryUbigeo.php' => 'Modules/Geo/Legacy',
    'app/api/queryDni.php' => 'Modules/External/Legacy',
    'app/apiMobile/cobroMobile.php' => 'Modules/Cash/Legacy',
    'app/apiMobile/creditToPay.php, listaCobrosHoy.php' => 'Modules/Credits/Legacy',
    'app/api/test.php' => 'ELIMINADO (benchmark debug sin uso)',
];
