<?php

namespace CrediSoporte\Domain\Helpers;

use DateInterval;
use DateTime;

class Holiday
{
    public static function isHoliday($date)
    {
        $numerWeekDay = date('w', strtotime($date));

        // verificamos si es domingo
        if ($numerWeekDay == 0) {
            return true;
        }

        $dayAndMonth = date('d/m', strtotime($date));

        $feriados = [
            '01/01', // año nuevo
            '01/05', // dia internacinal del trabajo
            '29/06', // san pedro y san pablo
            '30/08', // santarosa de lima
            '08/10', // batalla de angamos
            '01/11', // todo los santos
            '08/12', // Día de la Inmaculada Concepción
            '25/12' // navidad
        ];

        $isFeriado = false;
        foreach ($feriados as $feriado) {
            if ($feriado == $dayAndMonth) {
                $isFeriado = true;
                break;
            }
        }

        return $isFeriado;
    }

    /** fecha laborables entre
     * @param string $startAt 'Y-m-d
     * @param string $endAt 'Y-m-d
     * 
     * @return array[...DateTime]
     */
    public static function workinDatesBetween($startAt, $endAt)
    {
        $oneDay = new DateInterval('P1D');
        $start = new DateTime($startAt);
        $end = new DateTime($endAt);

        // $diffDays = $start->diff($end)->days;
        $dates = [];

        $flag = true;
        do {
            $fecha = $start->format('Y-m-d');

            if (!Holiday::isHoliday($fecha)) {
                $dates[] = new DateTime($fecha);
            }

            $start->add($oneDay);

            if ($start > $end) {
                $flag = false;
            }
        } while ($flag);

        // for ($i = 0; $i < $diffDays; $i++) {
        //     $fecha = $start->format('Y-m-d');

        //     if (Holiday::isHoliday($fecha)) {
        //         $start->add($oneDay);
        //         continue;
        //     }

        //     $dates[] = new DateTime($fecha);

        //     $start->add($oneDay);
        // }

        return $dates;
    }

    public static function numberWorkingDaysRemainingBetween($startAt, $endAt)
    {
        $nowDate = date('Y-m-d');
        $oneDay = new DateInterval('P1D');
        $start = new DateTime($startAt);
        $end = new DateTime($endAt);

        // $diffDays = $start->diff($end)->days;
        $number = 0;

        $flag = true;
        do {
            $fecha = $start->format('Y-m-d');

            if (!Holiday::isHoliday($fecha) && $fecha >= $nowDate) {
                $number++;
            }

            $start->add($oneDay);

            if ($start > $end) {
                $flag = false;
            }
        } while ($flag);

        // for ($i = 0; $i < $diffDays; $i++) {
        //     $fecha = $start->format('Y-m-d');

        //     if (Holiday::isHoliday($fecha)) {
        //         $start->add($oneDay);
        //         continue;
        //     }

        //     if ($fecha >= $nowDate) {
        //         $number++;
        //     }

        //     $start->add($oneDay);
        // }

        return $number;
    }
}
