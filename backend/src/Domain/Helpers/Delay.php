<?php

namespace CrediSoporte\Domain\Helpers;

use CrediSoporte\Domain\Models\Installment;
use Carbon\Carbon;

class Delay
{
    public $holidays = []; // m-d
    public $condone_dates = [];
    public int $penalty = 0; //
    public $payment_period = "";

    public function setHolidays($holidays = [])
    {
        $b = [];
        foreach ($holidays as $holiday) {
            $v = explode('-', $holiday);
            array_push($b, "$v[1]-$v[2]");
        }

        $this->holidays = $b;
    }

    public function setPenalty($penalty)
    {
        $this->penalty = (int) $penalty;
    }

    // deuda en mora de una cuota
    public function penaltyFromInstallment(Installment $installment, $next_payment)
    {
        if ($installment->is_finished) return 0;

        $discountPenalty = (int) ($installment->delay_payment + $installment->delay_condoned);

        $paymentDate = ($installment->capitalDebt() + $installment->interestDebt()) === 0 ? $installment->payment_date : null;
        $penalty = $this->getPenalty($installment->expiration_at, $paymentDate, $next_payment) - $discountPenalty;

        if ($penalty > 0) {
            return $penalty;
        }

        return 0;
    }

    public function daysExpired(string $last_payment, bool $absolute = false)
    {
        $now = new Carbon();
        $lastPayment = new Carbon($last_payment);
        $days = $lastPayment->diffInDays($now);

        if (!$absolute && $now->format('Y-m-d') < $last_payment) {
            return -1 * $days;
        }

        return $days;
    }

    public function getPenalty($expiration_date, $payment_date, $next_payment)
    {
        // si su penalidad es 0, nunca tendra mora
        if (!$this->penalty > 0) return 0;

        $now = new Carbon();

        if ($expiration_date >= $now->format('Y-m-d')) {
            // la cuota esta dentro de la fecha de pago
            return 0;
        }

        if (!is_null($payment_date) && $expiration_date >= $payment_date) {
            // pago dentro de la fecha
            return 0;
        }

        $penaltySum = in_array($expiration_date, $this->condone_dates) ? 0 : $this->penalty;

        // es ultima cuota
        if (
            is_null($next_payment) &&
            $expiration_date < $now->format('Y-m-d')
        ) {
            $expiration = new Carbon($expiration_date);
            $days = $expiration->diffInDays($now);
            $penaltySum += (int) ($days * $this->penalty);
        }

        // la ultima cuota, los diarios y pago unico no tienen cargos adicionales
        if (
            is_null($next_payment) ||
            $this->payment_period === 'daily' ||
            $this->payment_period === 'single_payment'
        ) {
            return $penaltySum;
        }

        $end_date = "";

        if (!is_null($payment_date) && $payment_date >= $next_payment) {
            $date = new Carbon($next_payment);
            $date->subDay();
            $end_date = $date->format('Y-m-d');
        } else if (!is_null($payment_date)) {
            $date = new Carbon($payment_date);
            $date->subDay();
            
            $end_date = $date->format('Y-m-d');
        } else {
            if ($now->format('Y-m-d') > $next_payment) {
                $date = new Carbon($next_payment);
                $date->subDay();
                $end_date = $date->format('Y-m-d');
            }else{
                $end_date = $now->subDay()->format('Y-m-d');
            }
        }

        $flag = true;

        $expirationDate = new Carbon($expiration_date);
        $expirationDate->addDay();

        if ($expirationDate->format('Y-m-d') > $end_date) {
            $flag = false;
        }

        while ($flag) {
            if (
                $expirationDate->format('w') !== '0' && // domingos no cuenta
                !in_array($expirationDate->format('m-d'), $this->holidays) && // feriados
                !in_array($expirationDate->format('Y-m-d'), $this->condone_dates) // fechas condonadas
            ) {
                $penaltySum += $this->penalty;
            }

            $expirationDate->addDay();

            if ($expirationDate->format('Y-m-d') > $end_date) {
                $flag = false;
            }
        }

        return $penaltySum;
    }
}
