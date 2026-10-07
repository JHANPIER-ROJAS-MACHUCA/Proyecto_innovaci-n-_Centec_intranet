<?php
require_once __DIR__ . '/../repositories/CatalogoRepository.php';
require_once __DIR__ . '/../Modules/Caja/Repositories/CajaRepository.php';
require_once __DIR__ . '/../repositories/FinancieraRepository.php';
require_once __DIR__ . '/../repositories/UbigeoRepository.php';

class CpanelService
{
    public static function resumen(array $user): array
    {
        $tipo = (int) $user['tipoU'];
        $caja = CajaRepository::habilitada($user);
        $data = array_merge(
            ['caja' => $caja ? array_merge(['habilitada' => true], CajaRepository::resumen($caja)) : ['habilitada' => false, 'efectivo' => 0, 'digital' => 0]],
            array_intersect_key(CpanelRepository::conteos(), array_flip(['clientes', 'creditosActivos', 'creditosPropuestos']))
        );
        $todos = CpanelRepository::conteos();
        if (in_array($tipo, [8, 5, 1], true)) {
            $data['morasPendientes'] = $todos['morasPendientes'];
            $data['extornosPendientes'] = $todos['extornosPendientes'];
            $data['billetajePendiente'] = $todos['billetajePendiente'];
        }
        if ($tipo === 5) {
            $data['usuariosActivos'] = $todos['usuariosActivos'];
            $data['oficinas'] = $todos['oficinas'];
        }
        if (in_array($tipo, [7, 2], true)) {
            $data = array_merge($data, CpanelRepository::carteraPropia($user['idU']));
        }
        if ($tipo === 5) {
            $data['metas'] = $todos['metas'];
            $data['cobrosHoy'] = CpanelRepository::cobrosHoyEquipo();
        }
        return $data;
    }
}

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

class FormatoService
{
    const PAGOS = [1 => 'diario', 2 => 'semanal', 3 => 'pago único', 4 => 'mensual', 5 => 'quincenal'];

    public static function contrato(int $idP): array
    {
        $credit = \App\Models\Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->select('tprestamo.*', 'tclie_general.dni', 'tclie_general.direc', 'tclie_general.cel')
            ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as client')
            ->where('tprestamo.idP', $idP)->first();
        if (!$credit) throw new DomainException('Crédito no existe.');
        $meses = ['January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'];
        return [
            'credit' => $credit,
            'vinculacion' => $credit->idV ? RelationRepository::vinculacionCompleta($credit->idV) : null,
            'negocio' => EmpresaService::ver(),
            'numero' => str_pad((string) $idP, 5, '0', STR_PAD_LEFT),
            'fecha' => date('d') . ' días del mes de ' . strtr(date('F'), $meses) . ' de ' . date('Y'),
            'formaPago' => self::PAGOS[(int) $credit->pago] ?? (string) $credit->pago,
        ];
    }
}

class DniService
{
    public static function consultar(string $number): array
    {
        $number = preg_replace('/\D/', '', $number);
        if (strlen($number) !== 8) throw new DomainException('DNI de 8 dígitos requerido.');
        $token = $_ENV['DNI_TOKEN'] ?? null;
        if (!$token) throw new DomainException('Servicio DNI no configurado.');
        $ch = curl_init('https://api.apis.net.pe/v1/dni?numero=' . $number);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token]]);
        $raw = curl_exec($ch);
        curl_close($ch);
        $r = json_decode($raw);
        if (!$r || !empty($r->error)) throw new DomainException($r->error ?? 'Sin respuesta.');
        return [
            'document' => $r->numeroDocumento, 'name' => $r->nombres,
            'father_first_surname' => $r->apellidoPaterno, 'mother_first_surname' => $r->apellidoMaterno,
        ];
    }
}

class UbigeoService
{
    public static function departamentos()
    {
        return UbigeoRepository::departamentos();
    }

    public static function provincias(?int $departmentId)
    {
        return UbigeoRepository::provincias($departmentId);
    }

    public static function distritos(?int $provinceId)
    {
        return UbigeoRepository::distritos($provinceId);
    }
}
