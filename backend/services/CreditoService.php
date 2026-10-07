<?php
require_once __DIR__ . '/../repositories/CreditoRepository.php';
require_once __DIR__ . '/../Modules/Clientes/Repositories/ClienteRepository.php';

class CreditoService
{
    public static function diasEntreCuotas(int $txtpago): string
    {
        switch ($txtpago) {
            case 1: return '1';
            case 2: return '7';
            case 3: return '1';
            case 4: return '30';
            default: return '0';
        }
    }

    public static function detalle(int $idP): ?object
    {
        return \App\Models\Credit::with('installments', 'customer')->where('idP', $idP)->first();
    }

    public static function tipos()
    {
        return CreditoRepository::tipos();
    }

    public static function editar(array $in): void
    {
        $credit = \App\Models\Credit::where('idP', (int) ($in['creditId'] ?? 0))->where('estado', 4)->first();
        if (!$credit) throw new DomainException('El credito no puede ser editado.');
        $tocados = \App\Models\Installment::where('idP', $credit->idP)
            ->where(function ($q) {
                $q->whereNotNull('estado')->orWhereNotNull('estado_mora');
            })->count();
        if ($tocados > 0) throw new DomainException('El credito no puede ser editado.');
        foreach (['tasa', 'fechaDesembolso', 'prestamo', 'tipoPago', 'plazo'] as $f) {
            if (!isset($in[$f]) || $in[$f] === '') throw new DomainException("Campo $f requerido.");
        }
        if (!in_array((int) $in['tipoPago'], [1, 2, 3, 4, 5], true)) throw new DomainException('Tipo de pago inválido.');
        $credit->pago = $in['tipoPago'];
        $credit->taza = $in['tasa'];
        $credit->montoAprovado = $in['prestamo'];
        $credit->fechaDesembolso = $in['fechaDesembolso'];
        $credit->started_at = $in['fechaInicio'] ?? null;
        $credit->n_cuota = $in['plazo'];
        $credit->plazo = $in['plazo'];
        $credit->mora = $in['prestamo'] <= 500 ? 0.5 : ($in['prestamo'] <= 1000 ? 1 : 2);
        $installments = $credit->generateInstallments();
        if (count($installments) === 0) throw new DomainException('No se puede generar las cuotas.');
        try {
            CreditoRepository::reemplazarCuotas($credit, $installments);
        } catch (\Throwable $th) {
            throw new DomainException('No se pudo editar el crédito.');
        }
    }

    public static function generar(array $in): array
    {
        $spouse = !empty($in['txtdni']) ? ClienteRepository::porDni($in['txtdni']) : null;
        $aval = !empty($in['txtdni1']) ? ClienteRepository::porDni($in['txtdni1']) : null;
        $vincId = CreditoRepository::createVinculacion(
            $in['identi'] ?? null,
            $spouse ? $spouse->idCG : null,
            $aval ? $aval->idCG : null
        );
        $cred = CreditoRepository::create([
            'idCG' => $in['identi'] ?? null, 'idV' => $vincId,
            'montoPropuesto' => $in['txtmonto'] ?? 0, 'cuota' => $in['txtcuotaf1'] ?? 0,
            'taza' => $in['txtinteres'] ?? 0, 'pago' => $in['txtpago'] ?? 1,
            'plazo' => $in['txtplazo'] ?? 0, 'n_cuota' => $in['txtplazo'] ?? 0,
            'diasPasados' => self::diasEntreCuotas((int)($in['txtpago'] ?? 0)),
            'estado' => 1, 'tipoP' => $in['txttipoPresta'] ?? null, 'mora' => $in['txtmora'] ?? 0,
            'user_id' => $in['user_id'] ?? null, 'started_at' => $in['started_at'] ?? null,
        ]);
        return $cred->toArray();
    }
}
