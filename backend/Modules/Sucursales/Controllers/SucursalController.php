<?php
// Módulo Sucursales — controlador (movido de controllers/, phase2-modular).
require_once __DIR__ . '/../Services/SucursalService.php';

class SucursalController
{
    // GET /api/sucursales → todas (con asesores activos + empresa + métodos pago). Origen: index/listarHome
    // Spec: gestión de sucursales = TI(7); consulta = Gerencia(1) y Admin Sucursal(2)
    public static function listar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        Response::json(['data' => SucursalService::listar(), 'success' => true]);
    }

    // GET /api/sucursales/home → {principal, sucursales}. Origen: listarHome (JSON público)
    public static function home(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        Response::json(['data' => SucursalService::home(), 'success' => true]);
    }

    // GET /api/sucursales/detalle?id={idS} → detalle completo. Origen: detalle()
    public static function detalle(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            Response::json(['data' => SucursalService::detalle((int) ($req->query['id'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/sucursales/metodos-pago?idS={idS} (flujo de pago: plataforma y cliente)
    public static function metodosPago(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 1, 3]);
        Response::json(['data' => SucursalRepository::metodosPago((int) ($req->query['idS'] ?? 0)), 'success' => true]);
    }

    // GET /api/sucursales/deudas?idCG={id} → deudas del cliente (origen PagoCliente::deudas)
    public static function deudas(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 9, 1, 3]);
        Response::json(['data' => SucursalRepository::deudasCliente((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }

    // GET /api/sucursales/contexto-pago?idCG={id}&sucursal_id={idS?} (origen PagoCliente::contexto)
    public static function contextoPago(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 1, 3]);
        $sucId = isset($req->query['sucursal_id']) && $req->query['sucursal_id'] !== '' ? (int) $req->query['sucursal_id'] : null;
        Response::json(['data' => SucursalRepository::contextoPago((int) ($req->query['idCG'] ?? 0), $sucId), 'success' => true]);
    }

    // GET /api/sucursales/comprobantes?idCG={id} (origen PagoCliente::comprobantes)
    public static function comprobantes(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 9, 1, 3]);
        Response::json(['data' => SucursalRepository::comprobantes((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }

    // POST /api/sucursales/horario-pago {sucursal_id, horario_pago}
    // Spec: estructura de sucursales = TI(7); Admin Sucursal(2) gestiona la suya
    public static function horarioPago(AppRequest $req): void
    {
        RoleMiddleware::require([5, 1]);
        $idS = (int) ($req->body['sucursal_id'] ?? 0);
        if ($idS <= 0) Response::error('sucursal_id requerido.', 422);
        if (!SucursalRepository::porId($idS)) Response::error('Sucursal no encontrada.', 404);
        SucursalRepository::actualizarHorarioPago($idS, (string) ($req->body['horario_pago'] ?? ''));
        Response::json(['success' => true]);
    }

    // POST /api/sucursales/contacto {nombre,email,mensaje,...} (origen: contacto(), público)
    public static function contacto(AppRequest $req): void
    {
        $in = $req->body;
        if (empty($in['nombre']) || empty($in['email']) || empty($in['mensaje'])) {
            Response::error('Campos obligatorios faltantes.', 422);
        }
        Response::json(['success' => true]);
    }
}
