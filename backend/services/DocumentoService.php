<?php
// Exportación de documentos: evaluado (PDF resumen, DJ patrimonial, Word) +
// registro de cliente (ficha datos, domicilio/negocio).
// Orígenes:
// - app/Modules/Evaluacion/Views/ver_pdf.php, ver_patrimonio.php, ver_word.php
// - evaluador/views/reportes/generar_pdf.php, exportar_resumen_pdf.php,
//   exportar_resumen_actual_pdf.php, exportar_excel.php, exportar_todo_excel.php,
//   declaracion_jurada_patrimonial.php
// - formatos/ (FICHA UNICA DATOS, DOMICILIO Y NEGOCIO 2025, PATRIMONIO, ALOJADO)
require_once __DIR__ . '/../repositories/EvaluacionRepository.php';
require_once __DIR__ . '/../repositories/ClienteRegistroRepository.php';

class DocumentoService
{
    public static function evaluacionCompleta($grupo): array
    {
        $eval = EvaluacionRepository::getEvaluacion($grupo);
        if (!$eval) throw new DomainException('Evaluación no encontrada.');
        $det = EvaluacionRepository::getDetalles($grupo);
        $eval = array_merge($eval, $det);
        $eval['conyuge'] = !empty($eval['id_cliente']) ? EvaluacionRepository::getConyugeByCliente((int) $eval['id_cliente']) : null;
        return $eval;
    }

    public static function render(string $vista, array $vars): string
    {
        extract($vars);
        ob_start();
        require __DIR__ . '/../views/evaluacion/' . $vista;
        return (string) ob_get_clean();
    }

    public static function fichaCliente(int $idCG): array
    {
        return ClienteRegistroRepository::fichaCompleta($idCG);
    }

    public static function formatosDisponibles(): array
    {
        $dir = __DIR__ . '/../storage/formatos/';
        $out = [];
        foreach (glob($dir . '*') ?: [] as $f) {
            $out[] = ['archivo' => basename($f), 'bytes' => filesize($f), 'url' => '/storage/formatos/' . basename($f)];
        }
        return $out;
    }
}

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

    // GET /api/documentos/cliente-ficha?idCG={id} → JSON + HTML ficha única de datos
    public static function clienteFicha(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 2, 9, 1]);
        try {
            Response::json(['data' => DocumentoService::fichaCliente((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    // GET /api/documentos/formatos → lista de plantillas xlsx/pdf/docx traídas de CENTECPC/formatos
    public static function formatos(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 7, 2, 9, 1]);
        Response::json(['data' => DocumentoService::formatosDisponibles(), 'success' => true]);
    }
}
