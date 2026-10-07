<?php

namespace App\Models;

use App\Helpers\Holiday;
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
        return $this->belongsTo('App\Models\Credit');
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
        if (!$penalty > 0) return 0;

        if ($this->estado_mora === '1' || $this->estado_mora === '2') return 0;

        $now = new Carbon();
        $fechaPago = new Carbon($this->fechaPago);

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
        if ($this->estado_mora === '1' || $this->estado_mora === '2') {
            return 0;
        }

        if (
            ($this->estado === '1' && $this->fechaProg >= date('Y-m-d', strtotime($this->fechaPago))) ||
            ($this->estado !== '1' && $this->fechaProg >= date('Y-m-d'))
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

            if ($inicio->format('w') === '0') {
                $diasFeriados++;
                continue;
            }

            if (in_array($inicio->format('d/m'), $feriados)) {
                $diasFeriados++;
            }

            $inicio->add($oneDay);
        }

        return round(($totalDias - $diasFeriados) * $penalty, 1);














    }

    public function isPendingNow()
    {
        if (strtotime($this->fechaProg) <= strtotime(date('Y-m-d'))) {
            return true;
        }

        return false;
    }

    public function delayed()
    {
        $debtInstallment = $this->debtInstallment();
        $strtimeCurrentDate = strtotime(date('Y-m-d'));
        $strtimeExpiration = strtotime($this->fechaProg);
        if ($strtimeCurrentDate > $strtimeExpiration && $debtInstallment > 0) {
            return true;
        } else if ($strtimeCurrentDate > $strtimeExpiration) {
            if ($this->fechaPago === null) {
                return true;
            } else if ($strtimeExpiration < strtotime(date('Y-m-d', strtotime($this->fechaPago)))) {
                return true;
            }
        }

        return false;
    }

    public function statusInstallment()
    {
        $debtInstallment = round($this->cuota - $this->montoPagado, 2);

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
