<?php

namespace CrediSoporte\Domain\Models;

use CrediSoporte\Domain\Helpers\Holiday;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $table = 'tpresta_detalle';
    protected $primaryKey = 'idPD';
    public $timestamps = false;
    protected $guarded = [];

    public function credit()
    {
        return $this->belongsTo('CrediSoporte\Domain\Models\Credit');
    }

    public function amount()
    {
        return (int) ($this->capital + $this->interest);
    }

    public function capitalDebt()
    {
        if ($this->is_finished) {
            return 0;
        }

        $debt = (int) ($this->capital - $this->capital_payment);

        return $debt > 0 ? (int) $debt : 0;
    }

    public function interestDebt()
    {
        if ($this->is_finished) {
            return 0;
        }

        $debt = (int) ($this->interest - $this->interest_payment);

        return $debt > 0 ? (int) $debt : 0;
    }

    public function debtCapital()
    {
        return $this->capitalDebt();
    }

    public function debtInterest()
    {
        return $this->interestDebt();
    }

    public function debtInstallment()
    {
        if ($this->is_finished) {
            return 0;
        }

        $result = $this->capital + $this->interest - $this->capital_payment - $this->interest_payment;

        return (int) ($result > 0 ? $result : 0);
    }

    public function debtMora($penalty, $nextInstallment, $daily = false, $extorned_dates = [])
    {
        // si su penalidad es 0, nunca tendra mora
        if (!$penalty > 0) return 0;

        // el credito no tiene mora si su estado de mora es diferente 1 (pagado) o 2 (extornado)
        if ($this->estado_mora === '1' || $this->estado_mora === '2') return 0;

        $now = new Carbon();
        $fechaPago = new Carbon($this->fechaPago);

        // el credito no esta con mora 
        // - si esta dentro de la fecha de pago o 
        // - pago dentro de la fecha de pago
        if (
            ($this->estado === "1" && $this->fechaProg >= $fechaPago->format('Y-m-d')) ||
            ($this->estado !== '1' && $this->fechaProg >= $now->format('Y-m-d'))
        ) {
            return 0;
        }

        if (!$nextInstallment || $daily) {
            return (float) $penalty;
        }

        $feriados = [
            '01/01', // dia y mes
            '01/05',
            '29/06',
            '30/08',
            '08/10',
            '01/11',
            '08/12',
            '25/12'
        ];

        $fechaFin = null;
        if ($this->estado === '1') {
            if ($fechaPago->format('Y-m-d') >= $nextInstallment->fechaProg) {
                $fechaFin = new Carbon($nextInstallment->fechaProg);
                $fechaFin->subDay(1);
            } else {
                $fechaFin = $fechaPago->clone();
            }
        } else {
            if ($now->format('Y-m-d') < $nextInstallment->fechaProg) {
                $fechaFin = $now->clone();
            } else {
                $fechaFin = new Carbon($nextInstallment->fechaProg);
                $fechaFin->subDay(1);
            }
        }

        $inicio = new Carbon($this->fechaProg);
        $inicio->addDay(1);

        $sumPenalty = $penalty;

        $finDateString = $fechaFin->format('Y-m-d');
        $bandera = true;

        if ($inicio->format('Y-m-d') > $finDateString) {
            $bandera = false;
        }

        while ($bandera) {
            // los domingos no cuentan
            // ni fechas feriados
            if (
                $inicio->format('w') !== '0' && 
                !in_array($inicio->format('d/m'), $feriados)
            ) {
                $sumPenalty += $penalty;
            }


            $inicio->addDay(1);

            if ($inicio->format('Y-m-d') > $finDateString) {
                $bandera = false;
            }
        }

        return $sumPenalty;
    }

    public function debtMoraOld($penalty, $nextInstallment, $daily = false)
    {
        // el credito no tiene mora si su estado de mora es diferente 1 (pagado) o 2 (extornado)
        if ($this->estado_mora === '1' || $this->estado_mora === '2') {
            return 0;
        }

        // el credito no esta con mora 
        // - si esta dentro de al fecha de pago o 
        // - pago dentro de la fecha de pago
        if (
            ($this->estado === '1' && $this->fechaProg >= date('Y-m-d', strtotime($this->fechaPago))) ||
            ($this->estado !== '1' && $this->fechaProg >= date('Y-m-d'))
        ) {
            return 0;
        }

        // la mora de la ultima cuota es uno solo
        if (!$nextInstallment || $daily) {
            return (float) $penalty;
        }

        $feriados = [
            '01/01', // dia y mes
            '01/05',
            '29/06',
            '30/08',
            '08/10',
            '01/11',
            '08/12',
            '25/12'
        ];

        $oneDay = new DateInterval('P1D');

        $fechaFinal = $nextInstallment->fechaProg;
        if ($this->estado === '1') {
            $fechaPago = $this->fechaPago;

            if ($fechaPago >= $nextInstallment->fechaProg) {
                $fechaFinal = $nextInstallment->fechaProg;
            } else {
                $fechaFinal = $fechaPago;
            }
        } else {
            if ($nextInstallment->fechaProg > date('Y-m-d')) {
                $fechaFinal = date('Y-m-d');
            } else {
                $fechaFinal = $nextInstallment->fechaProg;
            }
        }

        $inicio = new DateTime($this->fechaProg);
        $fin = new DateTime($fechaFinal);

        $totalDias = $inicio->diff($fin)->days;
        $diasFeriados = 0;

        for ($i = 0; $i < $totalDias; $i++) {

            // los domingos no cuentan
            if ($inicio->format('w') === '0') {
                $diasFeriados++;
                continue;
            }

            // fechas feriados del año
            if (in_array($inicio->format('d/m'), $feriados)) {
                $diasFeriados++;
            }

            $inicio->add($oneDay);
        }

        return round(($totalDias - $diasFeriados) * $penalty, 1);

        // $atrasado = false;
        // if (
        //     ($this->estado === '1' && $this->fechaProg < date('Y-m-d', strtotime($this->fechaPago))) ||
        //     ($this->estado !== '1' && $this->fechaProg < date('Y-m-d'))
        // ) {
        //     $atrasado = true;
        // }

        // if ($atrasado === false) {
        //     return 0;
        // }

        // $atrasado = $this->isAtrasado();

        // if (!$atrasado || $this->estado_mora) {
        //     return 0;
        // }

        // if (!$nextInstallment) {
        //     return $penalty;
        // } else if ($nextInstallment->fechaProg <= date('Y-m-d')) {
        //     $r = 1;

        //     $n = date('Y-m-d', strtotime($this->fechaProg . '+1 days'));
        //     while ($n < $nextInstallment->fechaProg && $r < 30) {
        //         if (!Holiday::isHoliday($n)) {
        //             $r++;
        //         }
        //         $n = date('Y-m-d', strtotime($n . '+1 days'));
        //     }
        //     return $penalty * $r;

        //     // $ini = new DateTime($installment->fechaProg);
        //     // $fin = new DateTime($siguienteCuota->fechaProg);

        //     // $diff = $ini->diff($fin);

        //     // $proto->deuda_mora = $diff->days * $this->mora;
        // } else if ($nextInstallment->fechaProg > date('Y-m-d')) {
        //     $r = 1;

        //     $n = date('Y-m-d', strtotime($this->fechaProg . '+1 days'));
        //     while ($n < date('Y-m-d') && $r < 30) {
        //         if (!Holiday::isHoliday($n)) {
        //             $r++;
        //         }
        //         $n = date('Y-m-d', strtotime($n . '+1 days'));
        //     }
        //     return $penalty * $r;

        //     // $ini = new DateTime($installment->fechaProg);
        //     // $fin = new DateTime(date('Y-m-d'));

        //     // $diff = $ini->diff($fin);

        //     // $proto->deuda_mora = $diff->days * $this->mora;
        // }

        // return 0;
    }

    public function isPendingNow()
    {
        if (strtotime($this->fechaProg) <= strtotime(date('Y-m-d'))) {
            return true;
        }

        return false;
    }

    /**
     * si se retrazo en el pago
     * 
     * return @boolean
     */
    public function delayed()
    {
        $debtInstallment = $this->debtInstallment();
        $strtimeCurrentDate = strtotime(date('Y-m-d'));
        $strtimeExpiration = strtotime($this->fechaProg);
        // 02-07-2022 > 06-05-2022 -> retrazadado <-
        // 02-07-2022 < 04-07-2022 -> puntual
        // 02-07-2022 < 20-07-2022 -> puntual
        if ($strtimeCurrentDate > $strtimeExpiration && $debtInstallment > 0) {
            return true;
        } else if ($strtimeCurrentDate > $strtimeExpiration) {
            // credito retrazado
            if ($this->fechaPago === null) {
                // FECHA PROG. 06-06-2022 > FECHA PAGO NULL -> PAGO PENDIENTE CON MORA
                // credito con mora
                return true;
            } else if ($strtimeExpiration < strtotime(date('Y-m-d', strtotime($this->fechaPago)))) {
                // FECHA PROG. 06-06-2022 > FECHA PAGO 04-05-2022 -> PAGO PUNTUAL
                // FECHA PROG. 06-06-2022 > FECHA PAGO 04-05-2022 -> PAGO PUNTUAL
                // FECHA PROG. 06-06-2022 = FECHA PAGO 06-06-2022 -> PAGO PUNTUAL
                // FECHA PROG. 06-06-2022 < FECHA PAGO 06-07-2022 -> PAGO RETRAZADO <-
                // credito cancelado despues de la fecha programada
                return true;
            }
        }

        return false;
    }

    public function statusInstallment()
    {
        $debtInstallment = round($this->cuota - $this->montoPagado, 2);
        // if ($debtInstallment > 0 && $debtInstallment < $this->cuota && strtotime($this->fechaProg) <= strtotime(date('Y-m-d'))) {
        //     return 'DEBT';
        // } else if ($debtInstallment <= 0) {
        //     return 'COMPLETED';
        // }

        if ($debtInstallment <= 0) {
            return 'COMPLETED';
        } else if ($debtInstallment > 0 && strtotime($this->fechaProg) <= strtotime(date('Y-m-d'))) {
            return 'DEBT';
        }

        return 'NONE';
    }

    public function statusMora()
    {
        $delayed = $this->delayed();

        // is delayed - fecha pago - monto mora
        // true         10-08-2022   0.5              <--- pago mora
        // true         10-08-2022   null             <--- exonerado
        // true         null         cualquier valor  <--- debe
        if ($delayed && $this->tfechaMora !== null && $this->pagoMora > 0) {
            return "COMPLETED";
        } else if ($delayed && $this->tfechaMora !== null && ($this->pagoMora === null || $this->pagoMora == 0)) {
            return "EXTORTED";
        } else if (
            $delayed &&
            $this->tfechaMora === null
        ) {
            return "DEBT";
        }

        return "NONE";

        // COMPLETED retrasado y pagado
        // DEBT retrasado y no completado
        // EXTORTED retrasado y extornado
        // NONE ninguno
    }

    public function getResumen()
    {
        $proto = (object) [
            'deuda_cuota' => 0,
            'deuda_mora' => 0,
            'atrasado' => false
        ];
    }

    public function isAtrasado()
    {
        $atrasado = false;

        if ($this->estado == 1 && $this->fechaProg < date('Y-m-d', strtotime($this->fechaPago))) {
            $atrasado = true;
        } else if ($this->estado != 1 && $this->fechaProg < date('Y-m-d')) {
            $atrasado = true;
        }

        return $atrasado;
    }

    public function statusMoraToString()
    {
        $estado = '';

        if ($this->estado_mora == 1) {
            $estado = "CANCELADO";
        } else if ($this->estado_mora == 2) {
            $estado = "EXTORNADO";
        } else if ($this->isAtrasado()) {
            $estado = "DEBE";
        }

        return $estado;
    }

    public function statusToString()
    {
        if ($this->is_finished) return "COMPLETADO";

        $capitalDebt = $this->capitalDebt();
        $interestDebt = $this->interestDebt();

        if (($capitalDebt + $interestDebt) === 0) {
            return "COMPLETO";
        }

        if (($this->capital_payment + $this->interest_payment) > 0) {
            return 'INCOMPLETO';
        }

        return "";
    }
}
