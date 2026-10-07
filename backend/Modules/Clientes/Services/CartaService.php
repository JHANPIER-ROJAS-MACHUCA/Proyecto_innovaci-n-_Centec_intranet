<?php
// Módulo Clientes — servicio de cartas (extraído de ClasificacionService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/ClasificacionService.php';

class CartaService
{
    // Invitación pre-aprobada: solo clase A del motor de comportamiento
    public static function invitacion(int $idCG): array
    {
        $c = ClasificacionService::clasificar('A', 5000);
        $cli = null;
        foreach ($c as $x) {
            if ((int) $x['idCG'] === $idCG) { $cli = $x; break; }
        }
        if (!$cli) throw new DomainException('Cliente no califica como A (invitación pre-aprobada).');
        $nom = trim(($cli['nom'] ?? '') . ' ' . ($cli['ap'] ?? '') . ' ' . ($cli['am'] ?? ''));
        return [
            'titulo' => 'Carta de invitación — crédito pre-aprobado (Clase A)',
            'contenido' => "Estimado(a) $nom (DNI {$cli['dni']}): por su excelente comportamiento de pago (puntaje {$cli['puntaje']}/100), tiene un CRÉDITO PRE-APROBADO. Acérquese a su agencia para hacerlo efectivo.",
            'cliente' => $cli,
        ];
    }

    public static function cobranza(int $idCG): array
    {
        $mora = ClasificacionService::morosidad();
        $row = null;
        foreach ($mora as $x) {
            if ((int) $x['idCG'] === $idCG) { $row = $x; break; }
        }
        if (!$row) throw new DomainException('Cliente sin cuotas retrasadas.');
        $tramo = ClasificacionService::tramo((int) $row['dias_max']);
        $nom = trim(($row['nom'] ?? '') . ' ' . ($row['ap'] ?? '') . ' ' . ($row['am'] ?? ''));
        return [
            'titulo' => "Carta de cobranza — tramo $tramo",
            'tramo' => $tramo,
            'contenido' => "Estimado(a) $nom (DNI {$row['dni']}): registra {$row['cuotas_retrasadas']} cuota(s) retrasada(s), con un máximo de {$row['dias_max']} días de atraso y deuda de S/ {$row['deuda']} (tramo $tramo). Regularice a la brevedad para evitar cargos adicionales.",
            'cliente' => $row,
        ];
    }

    public static function registrar(int $idCG, string $tipo, ?string $tramo, string $titulo, string $contenido, int $idU): int
    {
        global $capsule;
        if (!in_array($tipo, ['invitacion', 'cobranza'], true)) throw new DomainException('Tipo inválido.');
        return $capsule->table('carta_cobranza')->insertGetId([
            'idCG' => $idCG, 'tipo' => $tipo, 'tramo' => $tramo, 'titulo' => $titulo,
            'contenido' => $contenido, 'idU' => $idU, 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function historial(int $idCG): array
    {
        global $capsule;
        return $capsule->table('carta_cobranza')->where('idCG', $idCG)->orderByDesc('id')->limit(50)
            ->get()->map(fn($r) => (array) $r)->all();
    }
}
