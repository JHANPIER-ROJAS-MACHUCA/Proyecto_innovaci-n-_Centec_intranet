<?php
// Módulo Campo — servicio (extraído de services/PanelService.php, phase2-modular;
// idéntico). Cobranza de campo: usa Credit (Creditos), Delay/Holiday (utils) y
// TransaccionRepository (shared). Dirección: Campo → Creditos/shared.

class CampoService
{
    public static function cobrosHoy(array $user, string $search): array
    {
        $hoy = date('Y-m-d');
        $q = \App\Models\Credit::with('installments')
            ->select('tprestamo.idP', 'tprestamo.number_installments', 'tprestamo.capital', 'tprestamo.penalty', 'tprestamo.tipoP', 'tprestamo.payment_period', 'tclie_general.cel as cell_phone', 'tclie_general.idCG', 'tclie_general.coordinate_lat', 'tclie_general.coordinate_lng')
            ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
            ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
            ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->where('tprestamo.estado', 4);
        if ($search !== '') {
            $q->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%{$search}%'")
                ->orWhere('tclie_general.dni', 'like', "{$search}%");
        } else {
            $q->where('tpresta_detalle.expiration_at', $hoy)->where('tclie_general.idU', $user['idU']);
        }
        $credits = $q->groupBy('tprestamo.idP')->orderBy('tclie_general.ap')->get();
        $vencidos = \App\Models\Credit::with('installments')
            ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
            ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->where('tprestamo.estado', 4)->where('tprestamo.payment_period', '!=', 'daily')
            ->where('tpresta_detalle.expiration_at', '<', $hoy)->whereNull('tpresta_detalle.payment_date')
            ->where('tclie_general.idU', $user['idU'])
            ->groupBy('tprestamo.idP')
            ->select('tprestamo.idP', 'tprestamo.n_credito', 'tprestamo.capital', 'tprestamo.penalty', 'tprestamo.tipoP', 'tclie_general.cel as cell_phone')
            ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
            ->get();
        return ['data' => $credits, 'charges' => $vencidos];
    }

    public static function creditToPay(int $creditId): array
    {
        $credit = \App\Models\Credit::with('installments', 'condoneDates')
            ->select('tprestamo.*', 'credit_types.name as credit_type', 'tclie_general.cel')
            ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
            ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->leftJoin('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
            ->where('tprestamo.estado', 4)->where('tprestamo.idP', $creditId)->first();
        if (!$credit) throw new DomainException('El credito no existe.');
        $delay = new \App\Helpers\Delay;
        $delay->setHolidays(HolidayRepository::fechas());
        $delay->setPenalty($credit->penalty ?? $credit->mora);
        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
        $delay->payment_period = $credit->payment_period;
        $cuotas = [];
        $total = 0.0;
        foreach ($credit->installments as $key => $inst) {
            if ($inst->is_finished) continue;
            $next = $credit->installments[$key + 1] ?? null;
            $mora = $delay->penaltyFromInstallment($inst, $next->fechaProg ?? null);
            $deuda = round($inst->debtCapital() + $inst->debtInterest() + $mora, 1);
            $total = round($total + $deuda, 1);
            $cuotas[] = ['idPD' => $inst->idPD, 'ncuota' => $inst->ncuota, 'fechaProg' => $inst->fechaProg, 'capital' => $inst->debtCapital(), 'interes' => $inst->debtInterest(), 'mora' => $mora, 'deuda' => $deuda];
        }
        return ['data' => $credit, 'cuotas' => $cuotas, 'totalPendiente' => $total];
    }
}
