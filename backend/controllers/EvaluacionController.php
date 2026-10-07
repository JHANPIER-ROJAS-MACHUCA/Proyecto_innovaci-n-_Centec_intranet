<?php
// Puerto de EvaluacionController CENTECPC a API REST del backend nuevo.
// Origen: C:\xampp\htdocs\CENTECPC\app\Modules\Evaluacion\controllers\EvaluacionController.php
require_once __DIR__ . '/../services/EvaluacionService.php';

class EvaluacionController
{
    public static function listar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $tab = $req->query['tab'] ?? 'evaluados';
        if (!in_array($tab, ['evaluados','sin_eval','proximos'], true)) $tab = 'evaluados';
        $idSuc = isset($req->query['sucursal']) && $req->query['sucursal'] !== '' ? (int) $req->query['sucursal'] : null;
        $filtro = trim($req->query['q'] ?? $req->query['cliente'] ?? '');
        if ($tab === 'sin_eval') {
            $data = EvaluacionRepository::clientesSinEvaluacion($idSuc, $filtro);
        } elseif ($tab === 'proximos') {
            $data = EvaluacionRepository::proximosVencer($idSuc, $filtro);
        } else {
            $data = EvaluacionRepository::listarEvaluados($idSuc, $filtro, trim($req->query['resultado'] ?? ''));
        }
        Response::json(['data' => $data, 'tab' => $tab, 'stats' => EvaluacionRepository::statsGlobal($idSuc), 'success' => true]);
    }

    public static function ver(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $id = $req->query['id'] ?? $req->query['grupo'] ?? 0;
        $eval = EvaluacionRepository::getEvaluacion($id);
        if (!$eval) Response::error('Evaluación no encontrada.', 404);
        Response::json(['data' => array_merge($eval, EvaluacionRepository::getDetalles($id)), 'success' => true]);
    }

    public static function historial(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $idCG = (int) ($req->query['idCG'] ?? 0);
        if (!$idCG) Response::error('idCG requerido.', 422);
        Response::json(['data' => EvaluacionRepository::getEvaluacionesByCliente($idCG),
            'cliente' => EvaluacionRepository::getClienteById($idCG), 'success' => true]);
    }

    public static function guardar(AppRequest $req): void
    {
        // Spec: evaluación la crean Asesor(4), Admin Sucursal(2) y TI(7)
        $user = RoleMiddleware::require([5, 2, 1]);
        $in = $req->body + $_POST;
        $grupo = $in['grupo'] ?? $in['id_evaluacion'] ?? null;
        try {
            $id = EvaluacionService::guardar($in, (int) ($user->idU ?? $user->id ?? 0), $user->idRol ?? null, $grupo ?: null);
            Response::json(['data' => ['grupo' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function eliminar(AppRequest $req): void
    {
        $user = RoleMiddleware::require([5, 1]);
        $id = $req->body['grupo'] ?? $req->query['id'] ?? 0;
        if (!$id) Response::error('grupo requerido.', 422);
        $rol = (int) ($user->idRol ?? 0);
        if ($rol === 1) {
            EvaluacionRepository::eliminarDefinitivo($id);
        } else {
            EvaluacionRepository::softEliminar($id, (int) ($user->idU ?? 0));
        }
        Response::json(['success' => true]);
    }

    public static function permitirEditar(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        EvaluacionRepository::permitirEditar($req->body['grupo'] ?? $req->query['id'] ?? 0);
        Response::json(['success' => true]);
    }

    public static function buscarCliente(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $dni = trim($req->query['dni'] ?? '');
        if ($dni !== '') {
            Response::json(['data' => EvaluacionRepository::getClienteByDni($dni), 'success' => true]);
        }
        $idSuc = isset($req->query['sucursal']) && $req->query['sucursal'] !== '' ? (int) $req->query['sucursal'] : null;
        Response::json(['data' => EvaluacionRepository::buscarClientes(trim($req->query['q'] ?? ''), $idSuc), 'success' => true]);
    }
}
