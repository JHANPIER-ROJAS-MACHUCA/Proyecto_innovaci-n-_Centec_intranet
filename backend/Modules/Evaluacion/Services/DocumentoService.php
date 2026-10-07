<?php
// Módulo Evaluacion — documentos (movido de services/, phase2-modular; idéntico).
// Exportación: evaluado (PDF resumen, DJ patrimonial, Word) + ficha de cliente
// (usa Clientes) + formatos. Orígenes en comentarios originales abajo.
require_once __DIR__ . '/../Repositories/EvaluacionRepository.php';
require_once __DIR__ . '/../../Clientes/Repositories/ClienteRegistroRepository.php';

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
        require __DIR__ . '/../Views/' . $vista;
        return (string) ob_get_clean();
    }

    public static function fichaCliente(int $idCG): array
    {
        return ClienteRegistroRepository::fichaCompleta($idCG);
    }

    public static function formatosDisponibles(): array
    {
        $dir = __DIR__ . '/../../../storage/formatos/';
        $out = [];
        foreach (glob($dir . '*') ?: [] as $f) {
            $out[] = ['archivo' => basename($f), 'bytes' => filesize($f), 'url' => '/storage/formatos/' . basename($f)];
        }
        return $out;
    }
}
