<?php
// Lógica de sucursales: listado público + detalle completo (empresa, métodos de
// pago, tipos crédito/ahorro/seguro, servicios, asesores). Origen:
// SucursalesController::index/detalle/listarHome + SucursalesModel::conMetodosPago.
require_once __DIR__ . '/../repositories/SucursalRepository.php';

class SucursalService
{
    public static function listar(): array
    {
        $out = [];
        foreach (SucursalRepository::todas() as $s) {
            $asesores = array_values(array_filter(
                SucursalRepository::asesores((int) $s['idS']),
                fn($a) => (int) ($a['idEstado'] ?? 0) === 1
            ));
            $s['asesores'] = $asesores;
            SucursalRepository::conMetodosPago($s);
            $out[] = $s;
        }
        return $out;
    }

    public static function home(): array
    {
        $principal = SucursalRepository::principal();
        if ($principal) SucursalRepository::conMetodosPago($principal);
        return ['principal' => $principal, 'sucursales' => self::listar()];
    }

    public static function detalle(int $idS): array
    {
        $s = SucursalRepository::porId($idS);
        if (!$s) throw new DomainException('Sucursal no encontrada.');
        $asesores = SucursalRepository::asesores($idS);
        SucursalRepository::conMetodosPago($s);
        $s['tiposCredito'] = SucursalRepository::tiposCredito($idS);
        $s['tiposAhorro'] = SucursalRepository::tiposAhorro($idS);
        $s['tiposSeguro'] = SucursalRepository::tiposSeguro($idS);
        $s['servicios'] = SucursalRepository::servicios($idS);
        $s['asesores_activos'] = array_values(array_filter($asesores, fn($a) => (int) ($a['idEstado'] ?? 0) === 1));
        $s['asesores_desvinculados'] = array_values(array_filter($asesores, fn($a) => (int) ($a['idEstado'] ?? 0) !== 1));
        return $s;
    }
}

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
