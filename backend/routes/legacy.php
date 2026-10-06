<?php

// Rutas de compatibilidad legacy: preservan las URLs historicas
// (app/api/*.php, app/apiMobile/*.php, app/Controllers/*.php,
//  app/pdf/*.php, app/exel/*.php) delegando a src/.
// Generado automaticamente; no editar a mano.
// $router es CrediSoporte\Core\Router (inyectado por index.php).

$legacy = function (string $class) {
    return function () use ($class) {
        $GLOBALS['database'] = \CrediSoporte\Core\Bootstrap::capsule();
        $class::handle();
    };
};

$router->any('/app/api/activarCredito.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\ActivarCredito'));
$router->any('/app/api/actualizarCliente.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\ActualizarCliente'));
$router->any('/app/api/actualizarUsuario.php', $legacy('\CrediSoporte\Modules\Users\Legacy\ActualizarUsuario'));
$router->any('/app/api/addAttachmentCustomer.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\AddAttachmentCustomer'));
$router->any('/app/api/addCustomerRelationship.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\AddCustomerRelationship'));
$router->any('/app/api/anularAhorro.php', $legacy('\CrediSoporte\Modules\Cash\Legacy\AnularAhorro'));
$router->any('/app/api/cancelarCredito.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CancelarCredito'));
$router->any('/app/api/cobrarCredito.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CobrarCredito'));
$router->any('/app/api/condonarMora.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CondonarMora'));
$router->any('/app/api/condonarOtrasMoras.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CondonarOtrasMoras'));
$router->any('/app/api/confirmarPrestamo.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\ConfirmarPrestamo'));
$router->any('/app/api/crearCliente.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\CrearCliente'));
$router->any('/app/api/createMeta.php', $legacy('\CrediSoporte\Modules\Goals\Legacy\CreateMeta'));
$router->any('/app/api/creditsByCustomer.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CreditsByCustomer'));
$router->any('/app/api/dataCredit.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\DataCredit'));
$router->any('/app/api/deleteCustomerAttachment.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\DeleteCustomerAttachment'));
$router->any('/app/api/deleteCustomerRelationship.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\DeleteCustomerRelationship'));
$router->any('/app/api/deleteJustification.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\DeleteJustification'));
$router->any('/app/api/deleteMeta.php', $legacy('\CrediSoporte\Modules\Goals\Legacy\DeleteMeta'));
$router->any('/app/api/deleteUserAvatar.php', $legacy('\CrediSoporte\Modules\Users\Legacy\DeleteUserAvatar'));
$router->any('/app/api/desembolsar.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\Desembolsar'));
$router->any('/app/api/detalleCredito.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\DetalleCredito'));
$router->any('/app/api/editarCliente.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\EditarCliente'));
$router->any('/app/api/editarCredito.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\EditarCredito'));
$router->any('/app/api/eliminarTransaccion.php', $legacy('\CrediSoporte\Modules\Cash\Legacy\EliminarTransaccion'));
$router->any('/app/api/generarPrestamo.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\GenerarPrestamo'));
$router->any('/app/api/justifyNonPayment.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\JustifyNonPayment'));
$router->any('/app/api/login.php', $legacy('\CrediSoporte\Modules\Auth\Legacy\Login'));
$router->any('/app/api/logout.php', $legacy('\CrediSoporte\Modules\Auth\Legacy\Logout'));
$router->any('/app/api/queryDni.php', $legacy('\CrediSoporte\Modules\External\Legacy\QueryDni'));
$router->any('/app/api/queryUbigeo.php', $legacy('\CrediSoporte\Modules\Geo\Legacy\QueryUbigeo'));
$router->any('/app/api/registrarAhorro.php', $legacy('\CrediSoporte\Modules\Cash\Legacy\RegistrarAhorro'));
$router->any('/app/api/registrarUsuario.php', $legacy('\CrediSoporte\Modules\Users\Legacy\RegistrarUsuario'));
$router->any('/app/api/restorePasswordUser.php', $legacy('\CrediSoporte\Modules\Users\Legacy\RestorePasswordUser'));
$router->any('/app/api/searchCreditsOfCustomer.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\SearchCreditsOfCustomer'));
$router->any('/app/api/searchCustomer.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\SearchCustomer'));
$router->any('/app/api/searchCustomerByDocumentOrNames.php', $legacy('\CrediSoporte\Modules\Customers\Legacy\SearchCustomerByDocumentOrNames'));
$router->any('/app/api/toggleEstadoUsuario.php', $legacy('\CrediSoporte\Modules\Users\Legacy\ToggleEstadoUsuario'));
$router->any('/app/api/toggleRefinanciado.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\ToggleRefinanciado'));
$router->any('/app/api/updateJustification.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\UpdateJustification'));
$router->any('/app/api/updateMeta.php', $legacy('\CrediSoporte\Modules\Goals\Legacy\UpdateMeta'));
$router->any('/app/api/updateUserAvatar.php', $legacy('\CrediSoporte\Modules\Users\Legacy\UpdateUserAvatar'));
$router->any('/app/api/uploadFile.php', $legacy('\CrediSoporte\Modules\Files\Legacy\UploadFile'));
$router->any('/app/apiMobile/cobroMobile.php', $legacy('\CrediSoporte\Modules\Cash\Legacy\CobroMobile'));
$router->any('/app/apiMobile/creditToPay.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\CreditToPay'));
$router->any('/app/apiMobile/listaCobrosHoy.php', $legacy('\CrediSoporte\Modules\Credits\Legacy\ListaCobrosHoy'));
$router->any('/app/Controllers/CreateAttachmentController.php', $legacy('\CrediSoporte\Modules\Attachments\Controllers\CreateAttachmentController'));
$router->any('/app/Controllers/DeleteAttachmentController.php', $legacy('\CrediSoporte\Modules\Attachments\Controllers\DeleteAttachmentController'));
$router->any('/app/Controllers/UpdateAttachmentController.php', $legacy('\CrediSoporte\Modules\Attachments\Controllers\UpdateAttachmentController'));
$router->any('/app/pdf/arqueoCaja.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\ArqueoCaja'));
$router->any('/app/pdf/avisoCobranza.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\AvisoCobranza'));
$router->any('/app/pdf/cartillaPago.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\CartillaPago'));
$router->any('/app/pdf/constanciaNoAdeudo.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\ConstanciaNoAdeudo'));
$router->any('/app/pdf/historialDePago.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\HistorialDePago'));
$router->any('/app/pdf/letraCredit.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\LetraCredit'));
$router->any('/app/pdf/pagare.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\Pagare'));
$router->any('/app/pdf/posicionCliente.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\PosicionCliente'));
$router->any('/app/pdf/propuesta.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\Propuesta'));
$router->any('/app/pdf/resumenCredito.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\ResumenCredito'));
$router->any('/app/pdf/voucher.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\Voucher'));
$router->any('/app/pdf/voucher58.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\Voucher58'));
$router->any('/app/pdf/voucherAhorro.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\VoucherAhorro'));
$router->any('/app/pdf/voucherDesembolso.php', $legacy('\CrediSoporte\Modules\Docs\Legacy\VoucherDesembolso'));
$router->any('/app/exel/carterasVencidas.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CarterasVencidas'));
$router->any('/app/exel/clientes.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\Clientes'));
$router->any('/app/exel/creditosActivos.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CreditosActivos'));
$router->any('/app/exel/creditosFinalizados.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CreditosFinalizados'));
$router->any('/app/exel/creditosFinalizadosActivos.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CreditosFinalizadosActivos'));
$router->any('/app/exel/creditosVigentesAtrasados.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CreditosVigentesAtrasados'));
$router->any('/app/exel/crobrosPorFecha.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\CrobrosPorFecha'));
$router->any('/app/exel/desembolsos.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\Desembolsos'));
$router->any('/app/exel/metas.php', $legacy('\CrediSoporte\Modules\Reports\Legacy\Metas'));

// Shims sin efecto: archivos que solo inicializaban (salida vacia 200).
$noop = function () { };
$router->any('/app/models/Transaction.php', $noop);
$router->any('/app/index.php', $noop);
$router->any('/app', $noop);
$router->any('/app/', $noop);
