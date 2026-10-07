<?php
class CreditoEstadoService
{
    public const TOPE_ADMIN = 1500;

    public static function puedeConfirmar(array $user, float $monto): ?string
    {
        // Códigos CENTECPC: 8 Gerente, 5 admin_personal
        if (!in_array((int)($user['tipoU'] ?? 0), [8, 5], true)) return 'Tu usuario no puede confirmar prestamos.';
        if ($monto > self::TOPE_ADMIN && (int)$user['tipoU'] !== 8) return 'No tienes permisos para aprobar montos mayores a S/ 1,500.00';
        return null;
    }

    public static function confirmar(int $idP, float $monto, float $taza): void
    {
        \App\Models\Credit::where('idP', $idP)->update([
            'capital' => round($monto * 10),
            'interest_rate' => round($taza * 100) / 100,
            'estado' => 2,
        ]);
    }

    public static function cambiarEstado(int $idP, int $estado): void
    {
        \App\Models\Credit::where('idP', $idP)->update(['estado' => $estado]);
    }
}
