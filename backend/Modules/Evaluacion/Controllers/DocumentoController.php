<?php
// Módulo Evaluacion — controlador de documentos (extraído de
// Services/DocumentoService.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/DocumentoService.php';

class DocumentoController
{
    // GET /api/documentos/evaluacion-pdf?id={grupo} → HTML imprimible (window.print → PDF)
    public static function evaluacionPdf(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 2, 9, 1]);
        try {
            $eval = DocumentoService::evaluacionCompleta($req->query['id'] ?? 0);
            header('Content-Type: text/html; charset=UTF-8');
            echo DocumentoService::render('ver_pdf.php', ['eval' => $eval]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/documentos/evaluacion-patrimonio?id={grupo} → DJ patrimonial + anexo fotos
    public static function evaluacionPatrimonio(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 2, 9, 1]);
        try {
            $eval = DocumentoService::evaluacionCompleta($req->query['id'] ?? 0);
            header('Content-Type: text/html; charset=UTF-8');
            echo DocumentoService::render('ver_patrimonio.php', ['eval' => $eval]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/documentos/evaluacion-word?id={grupo} → descarga .doc (Word HTML)
    public static function evaluacionWord(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 2, 9, 1]);
        try {
            $eval = DocumentoService::evaluacionCompleta($req->query['id'] ?? 0);
            header('Content-Type: application/msword; charset=UTF-8');
            header('Content-Disposition: attachment; filename="DJ_Patrimonio_' . $eval['id_evaluacion'] . '.doc"');
            echo DocumentoService::render('ver_word.php', ['eval' => $eval]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/documentos/cliente-ficha?idCG={id} → JSON ficha única de datos
    public static function clienteFicha(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 2, 9, 1]);
        try {
            Response::json(['data' => DocumentoService::fichaCliente((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/documentos/formatos → plantillas xlsx/pdf/docx de CENTECPC/formatos
    public static function formatos(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 2, 9, 1]);
        Response::json(['data' => DocumentoService::formatosDisponibles(), 'success' => true]);
    }
}
