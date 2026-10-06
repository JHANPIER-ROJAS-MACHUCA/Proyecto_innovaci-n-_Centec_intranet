<?php

namespace CrediSoporte\Domain\Models;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Helpers\Holiday;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $table = 'tprestamo';
    protected $primaryKey = 'idP';
    public $timestamps = false;

    protected $guarded = [];

    public function installments()
    {
        return $this->hasMany('CrediSoporte\Domain\Models\Installment', 'idP');
    }

    public function customer()
    {
        return $this->belongsTo('CrediSoporte\Domain\Models\Customer', 'idCG');
    }

    public function comments()
    {
        return $this->hasMany('CrediSoporte\Domain\Models\Comment', 'credit_id', 'idP');
    }

    public function comment()
    {
        return $this->hasOne('CrediSoporte\Domain\Models\Comment', 'credit_id', 'idP');
    }

    public function transactions()
    {
        return $this->hasMany('CrediSoporte\Domain\Models\Transaction', 'conejo');
    }

    public function relations()
    {
        return $this->morphToMany(Customer::class, 'relationable', 'relations', null, 'customer_id')
            ->withPivot('type');
    }

    public function condoneDates()
    {
        return $this->hasMany(CondoneDate::class, 'credit_id');
    }

    public function productTypeToString()
    {
        $tipoP = '';
        switch ($this->tipoP) {
            case 1:
                $tipoP = "TRANSPORTE";
                break;
            case 2:
                $tipoP = "COMERCIO";
                break;
            case 3:
                $tipoP = "PRENDATARIO";
                break;
            case 4:
                $tipoP = "SERVICIO";
                break;
        }

        return $tipoP;
    }

    public function paymentPeriodToString()
    {
        switch ($this->payment_period) {
            case 'daily':
                return "Diario";
                break;
            case '2days':
                return "2 Días";
                break;
            case '3days':
                return "3 Días";
                break;
            case 'weekly':
                return "Semanal";
                break;
            case 'biweekly':
                return "Quincenal";
                break;
            case 'monthly':
                return "Mensual";
                break;
            case 'single_payment':
                return "Pago único";
                break;

            default:
                return "";
                break;
        }
    }

    public function paymentTypeToString()
    {
        $pago = '';
        switch ($this->pago) {
            case "1":
                $pago = "DIARIO";
                break;
            case "2":
                $pago = "SEMANAL";
                break;
            case "3":
                $pago = "PAGO UNICO";
                break;
            case "4":
                $pago = "MENSUAL";
                break;
            case "5":
                $pago = "QUINCENAL";
                break;
            case "6":
                $pago = "2 DIAS";
                break;
            case "7":
                $pago = "3 DIAS";
                break;
        }

        // switch ($this->payment_period) {
        //     case 'daily':
        //         $pago = "DIARIO";
        //         break;
        //     case '2days':
        //         $pago = "2 DIAS";
        //         break;
        //     case '3days':
        //         $pago = "3 DIAS";
        //         break;
        //     case 'weekly':
        //         $pago = "SEMANAL";
        //         break;
        //     case 'biweekly':
        //         $pago = "QUINCENAL";
        //         break;
        //     case 'monthly':
        //         $pago = "MENSUAL";
        //         break;
        //     case 'single_payment':
        //         $pago = "PAGO UNICO";
        //         break;
        // }

        return $pago;
    }

    public function statusToString()
    {
        switch ($this->estado) {
            case '1':
                return 'PROPUESTO';
            case '2':
                return 'APROBADO';
            case '3':
                return 'DESAPROBADO';
            case '4':
                return 'DESEMBOLSADO';
            case '5':
                return 'CANCELADO';
            case '6':
                return 'ANULADO';
        }
    }

    public function scopeActive($query)
    {
        return $query->where('estado', 4);
    }

    public function interestTotal()
    {
        $number = $this->montoAprovado * $this->taza / 100;
        return ceil($number * 10) / 10;
    }

    // mora que debe despues de que finalizo el credito
    public function debtMora($lastInstallment)
    {
        if (!$lastInstallment || $this->estado === '5') {
            return 0;
        }

        if ($lastInstallment->fechaProg < date('Y-m-d')) {
            $ini = new DateTime($lastInstallment->fechaProg);
            $fin = new DateTime(date('Y-m-d'));

            $diff = $ini->diff($fin);

            return ($diff->days * $this->mora) - $this->otras_moras;
        }
    }

    public function getDeuda($installments)
    {
        $deuda_capital = 0;
        $cuotas_pendientes = 0;
        $moras_pendientes = 0;
        $otras_moras_pendientes = 0;
        $next_payment = null;
        foreach ($installments as $installment) {
            $statusInstallment = $installment->statusInstallment();
            $statusMora = $installment->statusMora();

            $debtInstallment = $installment->debtInstallment();

            if ($statusInstallment !== 'COMPLETED') { // DEBT Y NONE
                $cuotas_pendientes++;

                if ($next_payment === null) {
                    $next_payment = $installment->fechaProg;
                }
            }

            if ($statusMora === 'DEBT') {
                $moras_pendientes++;
            }

            $deuda_capital += $debtInstallment;
        }

        // si tiene moras pendientes se le cobrara las moras depues de la fecha de la finalizacion del credito
        if ($moras_pendientes > 0) {
            $lastInstallment = $installments[count($installments) - 1];

            // agregamos la mora que pagara  despues de finaliza el credito
            if (strtotime(date('Y-m-d')) > strtotime($lastInstallment->fechaProg)) {
                $ini = new DateTime($lastInstallment->fechaProg);
                $fin = new DateTime(date('Y-m-d'));

                $diff = $ini->diff($fin);
                $otrosCargos = $diff->days;

                $otras_moras_pendientes = $otrosCargos;
            }
        }

        return [
            'cuotas_pendientes' => $cuotas_pendientes,
            'moras_pendientes' => $moras_pendientes,
            'deuda_capital' => round($deuda_capital, 2),
            'otras_moras_pendientes' => round($otras_moras_pendientes, 2),
            'next_payment' => $next_payment,
        ];
    }

    public function payByInstallment($installments, $numberInstallments, $numberDelays, $cashId, $transaction)
    {
        $MORASAPAGAR = $numberDelays;
        $fechaPago = date('Y-m-d H:i:s');

        $_array_installments = [];
        $_array_moras = [];
        $_pago_installments = 0;
        $_pago_moras = 0;

        $installmentsAndMoras = $this->getInstallmentsAndMorasFaltantes($installments);

        foreach ($installmentsAndMoras['installments'] as $installment) {
            if ($numberInstallments > 0) {
                $pendienteCuota = round($installment->cuota - $installment->montoPagado, 2);

                $_pago_installments = round($_pago_installments + $pendienteCuota, 2);
                array_push($_array_installments, $installment->idPD);

                $installment->montoPagado = $installment->cuota;
                $installment->fechaPago = $fechaPago;
                $installment->estado = 1;
                $installment->idU = $_COOKIE['tuser'];
                $installment->save();

                $numberInstallments--;
            }
        }

        $totalMoras = count($installmentsAndMoras['moras']);
        foreach ($installmentsAndMoras['moras'] as $installment) {
            if ($numberDelays > 0) {
                $pagoMora = $this->mora;

                // ultima cuota
                $installmentLasted = $installments[count($installments) - 1];

                $otras_moras_pendientes = 0;
                if (strtotime($installmentLasted->fechaProg) < strtotime(date('Y-m-d'))) {
                    $_ini = new DateTime($installmentLasted->fechaProg);
                    $_fin = new DateTime(date('Y-m-d'));
                    $_diff = $_ini->diff($_fin);

                    $otras_moras_pendientes = $_diff->days;
                }

                // sumamos otros cargos al ultimo pago de mora
                if ($numberDelays == 1 && $MORASAPAGAR == $totalMoras) {
                    $other = $otras_moras_pendientes * $this->mora;
                    $pagoMora = round($pagoMora + $other, 2);
                }

                $_pago_moras = round($_pago_moras + $pagoMora, 2);
                array_push($_array_moras, $installment->idPD);

                $installment->pagoMora = $pagoMora;
                $installment->tfechaMora = $fechaPago;
                $installment->save();

                $numberDelays--;
            }
        }

        $resumentInstallments = Installment::where('idP', $this->idP)->get();
        $resumen_data = $this->getDeuda($resumentInstallments);

        if (
            $resumen_data['deuda_capital'] == 0 &&
            $resumen_data['moras_pendientes'] == 0 &&
            $resumen_data['otras_moras_pendientes'] == 0
        ) {
            $this->estado = 5;
            $this->save();
        }

        $transaction->idCA = $cashId;
        $transaction->tipo = 3;
        $transaction->total = $_pago_installments + $_pago_moras;
        $transaction->cuota = $_pago_installments;
        $transaction->idCuota = implode(',', $_array_installments);
        $transaction->mora = $_pago_moras;
        $transaction->idMora = implode(',', $_array_moras);
        $transaction->cliente = $this->idCG;
        $transaction->estadodt = 2;
        $transaction->saldo = $resumen_data['deuda_capital'];
        $transaction->cuotas_pendientes = $resumen_data['cuotas_pendientes'];
        $transaction->next_payment = $resumen_data['next_payment'];
        $transaction->created_at = $fechaPago;
        $transaction->save();

        return true;
    }

    public function payByAmount($installments, $amount, $numberDelays, $cashId, $transaction)
    {
        $MORASAPAGAR = $numberDelays;
        $fechaPago = date('Y-m-d H:i:s');

        $_array_installments = [];
        $_array_moras = [];
        $_pago_installments = 0;
        $_pago_moras = 0;

        $installmentsAndMoras = $this->getInstallmentsAndMorasFaltantes($installments);

        foreach ($installmentsAndMoras['installments'] as $installment) {
            if ($amount > 0) {
                $deudaCuota = round($installment->cuota - $installment->montoPagado, 2);

                if ($amount >= $deudaCuota) {
                    array_push($_array_installments, $installment->idPD);
                    $_pago_installments = round($_pago_installments + $deudaCuota, 2);

                    $installment->montoPagado = $installment->cuota;
                    $installment->fechaPago = $fechaPago;
                    $installment->estado = 1;
                    $installment->idU = $_COOKIE['tuser'];
                    $installment->save();

                    $amount = round($amount - $deudaCuota, 2);
                } else {
                    array_push($_array_installments, $installment->idPD);
                    $_pago_installments = round($_pago_installments + $amount, 2);

                    $installment->montoPagado = round($installment->montoPagado + $amount, 2);
                    $installment->fechaPago = $fechaPago;
                    $installment->estado = 2;
                    $installment->idU = $_COOKIE['tuser'];
                    $installment->save();

                    $amount = 0;
                }
            }
        }

        $totalMoras = count($installmentsAndMoras['moras']);
        foreach ($installmentsAndMoras['moras'] as $installment) {
            if ($numberDelays > 0) {
                $pagoMora = $this->mora;

                // ultima cuota
                $installmentLasted = $installments[count($installments) - 1];

                $otras_moras_pendientes = 0;
                if (strtotime($installmentLasted->fechaProg) < strtotime(date('Y-m-d'))) {
                    $_ini = new DateTime($installmentLasted->fechaProg);
                    $_fin = new DateTime(date('Y-m-d'));
                    $_diff = $_ini->diff($_fin);

                    $otras_moras_pendientes = $_diff->days;
                }


                // sumamos otros cargos al ultimo pago de mora
                if ($numberDelays == 1 && $MORASAPAGAR == $totalMoras) {
                    $other = $otras_moras_pendientes * $this->mora;
                    $pagoMora = round($pagoMora + $other, 2);
                }

                $_pago_moras = round($_pago_moras + $pagoMora, 2);
                array_push($_array_moras, $installment->idPD);

                $installment->pagoMora = $pagoMora;
                $installment->tfechaMora = $fechaPago;
                $installment->save();

                $numberDelays--;
            }
        }

        $resumentInstallments = Installment::where('idP', $this->idP)->get();
        $resumen_data = $this->getDeuda($resumentInstallments);

        if (
            $resumen_data['deuda_capital'] == 0 &&
            $resumen_data['moras_pendientes'] == 0 &&
            $resumen_data['otras_moras_pendientes'] == 0
        ) {
            $this->estado = 5;
            $this->save();
        }

        $transaction->idCA = $cashId;
        $transaction->tipo = 3;
        $transaction->total = $_pago_installments + $_pago_moras;
        $transaction->cuota = $_pago_installments;
        $transaction->idCuota = implode(',', $_array_installments);
        $transaction->mora = $_pago_moras;
        $transaction->idMora = implode(',', $_array_moras);
        $transaction->cliente = $this->idCG;
        $transaction->estadodt = 2;
        $transaction->saldo = $resumen_data['deuda_capital'];
        $transaction->cuotas_pendientes = $resumen_data['cuotas_pendientes'];
        $transaction->next_payment = $resumen_data['next_payment'];
        $transaction->created_at = $fechaPago;
        $transaction->save();

        return true;
    }

    public function getInstallmentsAndMorasFaltantes($installments)
    {
        $_installments = [];
        $_moras = [];
        foreach ($installments as $installment) {
            $statusInstallment = $installment->statusInstallment();
            $statusMora = $installment->statusMora();

            if ($statusInstallment !== 'COMPLETED') { // DEBT Y NONE
                array_push($_installments, $installment);
            }

            if ($statusMora === 'DEBT') {
                array_push($_moras, $installment);
            }
        }

        return [
            'installments' => $_installments,
            'moras' => $_moras
        ];
    }

    public function test($installments, $amount, $numberDelays, $cashId, $transaction)
    {
        $fechaPago = date('Y-m-d H:i:s');

        // total de cuotas y moras faltantes
        $installmentPending = 0;
        $moraPending = 0;
        $deudaCapitalTotal = 0;

        // las cuotas y moras que se pagaron
        $installmentsFinalizados = 0;
        $morasPagadas = 0;

        // cuotas y moras afectadas en array
        $_array_installments = [];
        $_array_moras = [];

        // monto en soles de cuotas y moras pagadas
        $totaPagoInstallment = 0;
        $totalMoraPagado = 0;

        // siguiente pago
        $nextPayment = null;

        foreach ($installments as $installment) {
            $statusInstallment = $installment->statusInstallment();
            $statusMora = $installment->statusMora();

            // -- pago de cuotas
            if ($statusInstallment !== 'COMPLETED') { // DEBT Y NONE
                $installmentPending++;
                $deudaCapitalTotal += $installment->debtInstallment();

                if ($amount > 0) {
                    $debtInstallment = $installment->debtInstallment();

                    if ($amount >= $debtInstallment) {
                        array_push($_array_installments, $installment->idPD);
                        $totaPagoInstallment += $debtInstallment;
                        $installmentsFinalizados++;

                        $installment->montoPagado = $installment->cuota;
                        $installment->fechaPago = $fechaPago;
                        $installment->estado = 1;
                        $installment->idU = $_COOKIE['user1'];
                        $installment->save();

                        $amount = round($amount - $debtInstallment, 2);
                    } else {
                        array_push($_array_installments, $installment->idPD);
                        $totaPagoInstallment += $amount;

                        $installment->montoPagado = round($installment->montoPagado + $amount, 2);
                        $installment->fechaPago = $fechaPago;
                        $installment->estado = 2;
                        $installment->idU = $_COOKIE['user1'];
                        $installment->save();

                        $amount = 0;
                    }
                } else {
                    $nextPayment = $installment->fechaProg;
                }
            }

            // ultima cuota
            $installmentLasted = $installments[count($installments) - 1];

            $otras_moras_pendientes = 0;
            if (strtotime($installmentLasted->fechaProg) < strtotime(date('Y-m-d'))) {
                $_ini = new DateTime($installmentLasted->fechaProg);
                $_fin = new DateTime(date('Y-m-d'));
                $_diff = $_ini->diff($_fin);

                $otras_moras_pendientes = $_diff->days * $this->mora;
            }

            // -- pago de moras
            if ($statusMora === 'DEBT') {
                $moraPending++;

                if ($numberDelays > 0) {
                    $morasPagadas++;

                    $pagoMora = $this->mora;

                    // sumamos otros cargos al ultimo pago de mora
                    if ($numberDelays == 1 && $otras_moras_pendientes > 0) {
                        $pagoMora = round($pagoMora + $otras_moras_pendientes, 2);
                    }

                    $totalMoraPagado += $pagoMora;
                    array_push($_array_moras, $installment->idPD);

                    $installment->pagoMora = $pagoMora;
                    $installment->tfechaMora = $fechaPago;
                    $installment->save();

                    $numberDelays--;
                }
            }
        }

        if ($totaPagoInstallment > 0 || $totalMoraPagado > 0) {
            $transaction->idCA = $cashId;
            $transaction->tipo = 3;
            $transaction->total = $totaPagoInstallment + $totalMoraPagado;
            $transaction->cuota = $totaPagoInstallment;
            $transaction->idCuota = implode(',', $_array_installments);
            $transaction->mora = $totalMoraPagado;
            $transaction->idMora = implode(',', $_array_moras);
            $transaction->cliente = $this->idCG;
            $transaction->estadodt = 2;
            $transaction->saldo = round($deudaCapitalTotal - $totaPagoInstallment, 2);
            $transaction->cuotas_pendientes = $installmentPending - $installmentsFinalizados;
            $transaction->next_payment = $nextPayment;
            $transaction->created_at = $fechaPago;
            $transaction->save();

            if ($installmentPending - $installmentsFinalizados == 0 && $moraPending - $morasPagadas == 0) {
                $this->estado = 5;
                $this->save();
            }
        }

        return true;
    }

    public function getImporte()
    {
        return round($this->montoAprovado + ($this->montoAprovado * $this->taza / 100), 2);
    }

    public function getResumenForCreditWithRelation()
    {
        $debtCapital = 0;
        $debtMora = 0;
        $debtMoraDiasFinalizados = 0;
        $diasRetraso = 0;

        $numberInstallments = count($this->installments);
        $now = strtotime(date('Y-m-d'));
        foreach ($this->installments as $key => $item) {
            $statusMora = $item->statusMora();

            $debtCapital += $item->debtInstallment();

            if ($statusMora === 'DEBT') {
                $debtMora += $this->mora;
            }

            if ($key + 1 === $numberInstallments) {
                $debtMoraDiasFinalizados = $item->fechaProg;
                if (strtotime($item->fechaProg) < $now) {
                    $ini = new DateTime(date('Y-m-d'));
                    $fin = new DateTime($item->fechaProg);

                    $diff = $ini->diff($fin);

                    $diasRetraso = $diff->days;
                }
            }
        }

        return [
            'debtCapital' => $debtCapital,
            'debtMora' => $debtMora + ($diasRetraso * $this->mora),
            'debtMoraDiasFinalizados' => $debtMoraDiasFinalizados,
            'diasRetraso' => $diasRetraso
        ];
    }

    public function getInstallmentPending()
    {
        $this->installments;

        $installmentsPending = [];
        foreach ($this->installments as $installment) {
            $statusCuota = $installment->statusInstallment();
            $statusMora = $installment->statusMora();

            // verificamos si la cuota tiene deuda
            if (
                $statusCuota !== 'COMPLETED' ||
                $statusMora === 'DEBT'
            ) {
                array_push($installmentsPending, $installment);
            }
        }

        return $installmentsPending;
    }

    public function installmentSummary(Delay $delay)
    {
        $return = [];

        foreach ($this->installments as $key => $installment) {
            $proto = (object) [
                'id' => $installment->idPD,
                'deuda_cuota' => $installment->debtInstallment(),
                'deuda_mora' => 0,
                'atrasado' => $installment->isAtrasado()
            ];

            $nextInstallment = $this->installments[$key + 1] ?? null;

            $proto->deuda_mora = $delay->penaltyFromInstallment($installment, $nextInstallment !== null ? $nextInstallment->expiration_at : null);

            // $proto->deuda_mora = $installment->debtMora($this->mora, $nextInstallment);

            // if ($proto->atrasado && !$installment->estado_mora) {
            //     $proto->deuda_mora = $this->mora;
            // }
            // if ($proto->atrasado && !$installment->estado_mora) {
            //     $siguienteCuota = $this->installments[$key + 1] ?? null;

            //     if (!$siguienteCuota) {
            //         $proto->deuda_mora = (float) $this->mora;
            //     } else if ($siguienteCuota->fechaProg <= date('Y-m-d')) {
            //         $r = 1;

            //         $n = date('Y-m-d', strtotime($installment->fechaProg . '+1 days'));
            //         while ($n < $siguienteCuota->fechaProg && $r < 30) {
            //             if (!Holiday::isHoliday($n)) {
            //                 $r++;
            //             }
            //             $n = date('Y-m-d', strtotime($n . '+1 days'));
            //         }
            //         $proto->deuda_mora = $this->mora * $r;

            //         // $ini = new DateTime($installment->fechaProg);
            //         // $fin = new DateTime($siguienteCuota->fechaProg);

            //         // $diff = $ini->diff($fin);

            //         // $proto->deuda_mora = $diff->days * $this->mora;
            //     } else if ($siguienteCuota->fechaProg > date('Y-m-d')) {
            //         $r = 1;

            //         $n = date('Y-m-d', strtotime($installment->fechaProg . '+1 days'));
            //         while ($n < date('Y-m-d') && $r < 30) {
            //             if (!Holiday::isHoliday($n)) {
            //                 $r++;
            //             }
            //             $n = date('Y-m-d', strtotime($n . '+1 days'));
            //         }
            //         $proto->deuda_mora = $this->mora * $r;

            //         // $ini = new DateTime($installment->fechaProg);
            //         // $fin = new DateTime(date('Y-m-d'));

            //         // $diff = $ini->diff($fin);

            //         // $proto->deuda_mora = $diff->days * $this->mora;
            //     }
            // }

            array_push($return, $proto);
        }

        return $return;
    }

    public function creditSummary()
    {
        $proto = (object) [
            'dias_atrasados' => 0,
            'deuda_otras_moras' => 0
        ];

        $lastInstallment = $this->installments[count($this->installments) - 1];
        if ($lastInstallment->fechaProg < date('Y-m-d')) {
            $ini = new DateTime($lastInstallment->fechaProg);
            $fin = new DateTime(date('Y-m-d'));

            $diff = $ini->diff($fin);

            $proto->dias_atrasados = $diff->days;

            $penaltyDiscount = $this->otras_moras + $this->delay_condoned;
            $proto->deuda_otras_moras = round((($diff->days * $this->mora) - $penaltyDiscount) * 10) / 10;
        }

        return $proto;
    }

    public function generateResumenInstallments()
    {
        $protoCredit = (object) [
            'deuda_otras_moras' => 0
        ];

        foreach ($this->installments as $key => $installment) {
            $proto = (object) [
                'deuda_cuota' => 0,
                'deuda_mora' => 0,
                'atrasado' => false
            ];

            if ($installment->estado != 1) {
                $proto->deuda_cuota = round($installment->cuota - $installment->montoPagado, 1);
            }

            $atrasado = $installment->isAtrasado();

            if ($atrasado) {
                $proto->atrasado = true;
            }

            if ($atrasado && !$installment->estado_mora) {
                $siguienteCuota = $this->installments[$key + 1] ?? null;

                if (!$siguienteCuota) {
                    $proto->deuda_mora = (float) $this->mora;
                } else if ($siguienteCuota->fechaProg < date('Y-m-d')) {
                    $ini = new DateTime($installment->fechaProg);
                    $fin = new DateTime($siguienteCuota->fechaProg);

                    $diff = $ini->diff($fin);

                    $proto->deuda_mora = $diff->days * $this->mora;
                } else if (date('Y-m-d') < $siguienteCuota->fechaProg) {
                    $ini = new DateTime($installment->fechaProg);
                    $fin = new DateTime(date('Y-m-d'));

                    $diff = $ini->diff($fin);

                    $proto->deuda_mora = $diff->days * $this->mora;
                }
            }

            $installment->proto = $proto;
        }

        $lastInstallment = $this->installments[count($this->installments) - 1];
        if ($lastInstallment->fechaProg < date('Y-m-d')) {
            $ini = new DateTime($lastInstallment->fechaProg);
            $fin = new DateTime(date('Y-m-d'));

            $diff = $ini->diff($fin);

            $protoCredit->deuda_otras_moras = ($diff->days * $this->mora) - $this->otras_moras;
        }

        $this->proto = $protoCredit;
    }

    public function generateInstallments()
    {
        $interesTotal = $this->interestTotal();

        $interesPorCuota = round($interesTotal / $this->plazo, 1);

        $pagoPorCuota = round($this->montoAprovado / $this->plazo, 1);
        $pagoFinalPorCuota = round($this->montoAprovado - ($pagoPorCuota * ($this->plazo - 1)), 1);

        if (
            $pagoPorCuota < 0 ||
            $pagoFinalPorCuota < 0 ||
            $interesPorCuota < 0
        ) {
            return [];
        }

        $saldo = round($this->montoAprovado + $interesTotal, 1);

        switch ($this->pago) {
            case '1': // diario
                $intervalo = "+1 days";
                break;
            case '2': // semanal
                $intervalo = "+1 week";
                break;
            case '3': // pago unico
                $intervalo = "+2 week";
                break;
            case '4': // mensual
                $intervalo = "+1 month";
                break;
            case '5': // quincenal
                $intervalo = "+15 days";
                break;

            default:
                $intervalo = "+1 days";
                break;
        }

        $f = [];
        $d = $this->started_at;
        for ($i = 0; $i < $this->plazo; $i++) {
            $installment = new Installment();
            $installment->idP = $this->idP;
            $installment->ncuota = $i + 1;

            $esLaUltimaCuota = $i + 1 == $this->plazo;
            $installment->cuota = $esLaUltimaCuota ? $pagoFinalPorCuota : $pagoPorCuota;

            if ($interesPorCuota < round($interesTotal, 1) && $i + 1 !== (int) $this->plazo) {
                $installment->interest = $interesPorCuota;
                $interesTotal = round($interesTotal - $interesPorCuota, 1);
            } else {
                $installment->interest = $interesTotal;
                $interesTotal = 0;
            }

            $saldo = round($saldo - ($installment->cuota + $installment->interest), 1);
            $installment->saldo = $saldo;

            if ($i === 0 && $d) {
                $installment->fechaProg = $d;
                array_push($f, $installment);
                continue;
            } else if ($i === 0 && !$d) {
                $d = date('Y-m-d');
            }

            $d = date('Y-m-d', strtotime($d . $intervalo));

            while (Holiday::isHoliday($d)) {
                $d = date('Y-m-d', strtotime($d . '+1 days'));
            }

            $installment->fechaProg = $d;
            array_push($f, $installment);
        }

        return $f;
    }
}
