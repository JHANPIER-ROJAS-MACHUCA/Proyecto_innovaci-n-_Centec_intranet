<?php
// Registro completo de clientes traído de CENTECPC.
// Origen: app/Modules/Cliente/models/Cliente.php + controllers/ClienteController.php
require_once __DIR__ . '/../repositories/ClienteRegistroRepository.php';

class RegistroClienteController
{
    // POST /api/clientes/registro-completo {dni,nom,ap,...,direccion_data,negocio_data,conyuge,avales}
    // Spec: registran Admin Sucursal(2), Plataforma(3), Asesor(4); TI(7) global
    public static function crearCompleto(AppRequest $req): void
    {
        RoleMiddleware::require([5, 7, 2, 1]);
        $in = $req->body;
        if (empty($in['dni']) || empty($in['nom']) || empty($in['ap'])) {
            Response::error('dni, nom y ap son requeridos.', 422);
        }
        try {
            $idCG = ClienteRegistroRepository::crearCompleto($in);
            Response::json(['data' => ['idCG' => $idCG], 'success' => true], 201);
        } catch (\Throwable $e) {
            Response::error('No se pudo registrar: ' . $e->getMessage(), 422);
        }
    }

    // GET /api/clientes/ficha-completa?idCG={id}
    public static function fichaCompleta(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 2, 9, 1]);
        try {
            Response::json(['data' => ClienteRegistroRepository::fichaCompleta((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }
}
