<?php
require_once __DIR__ . '/CajaService.php';
require_once __DIR__ . '/../repositories/FinancieraRepository.php';

class CobroValidationException extends Exception
{
    public array $payload;
    public function __construct(array $payload)
    {
        $this->payload = $payload;
        parent::__construct($payload['message'] ?? 'Error de validación.');
    }
}

class CobroService
{
    public static function validar(array $in): ?array
    {
        $mora = floatval($in['mora'] ?? 0);
        $value = $in['value'] ?? null;
        if (!($mora > 0) && !($value > 0)) {
            $porMonto = ($in['paymentType'] ?? $in['payment_type'] ?? '') === 'amount';
            return ['message' => $porMonto
                ? 'El monto a pagar debe ser mayor a 0.0 soles.'
                : 'Debes de seleccionar al menos una cuota a pagar.', 'success' => false];
        }
        if (empty($in['creditId'])) return ['message' => 'creditId requerido.', 'success' => false];
        return null;
    }

    public static function creditoPendiente(int $creditId): ?object
    {
        return \App\Models\Credit::with('installments', 'condoneDates', 'customer')
            ->where('idP', $creditId)->where('estado', 4)->first();
    }

    public static function calcular(object $credit, string $paymentType, float $value, float $mora): array
    {
        $isAmount = $paymentType === 'amount';
        $requestPayment = $isAmount ? $value : (int) $value;
        $requestPenalty = (int) $mora;
        $_requestPayment = $requestPayment;
        $_requestPenalty = $requestPenalty;
        $fechaYHoraPago = date('Y-m-d H:i:s');
        $fechaPago = date('Y-m-d');

        $delay = new \App\Helpers\Delay;
        $delay->setHolidays(HolidayRepository::fechas());
        $delay->setPenalty($credit->mora);
        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
        $delay->payment_period = $credit->payment_period;

        $countPenalty = 0;
        $countInstallment = 0;
        $penalty = 0.0;
        $sumDebtCapital = 0.0;
        $sumDebtInterest = 0.0;
        $summaries = [];

        foreach ($credit->installments as $key => $installment) {
            if ($installment->is_finished) continue;
            $debtCapital = $installment->debtCapital();
            $debtInterest = $installment->debtInterest();
            $nextInstallment = $credit->installments[$key + 1] ?? null;
            $debtPenalty = $delay->penaltyFromInstallment($installment, $nextInstallment->fechaProg ?? null);
            $penalty = round($debtPenalty + $penalty, 1);
            $sumDebtCapital = round($sumDebtCapital + $debtCapital, 1);
            $sumDebtInterest = round($sumDebtInterest + $debtInterest, 1);
            if ($debtCapital > 0.0 || $debtInterest > 0.0) $countInstallment++;
            if ($debtPenalty > 0.0) $countPenalty++;

            $pc = 0.0;
            $pi = 0.0;
            $pp = 0.0;
            if ($isAmount) {
                if ($debtCapital > 0.0 && $_requestPayment > 0.0 && $_requestPayment >= $debtCapital) {
                    $pc = $debtCapital;
                    $_requestPayment = round($_requestPayment - $debtCapital, 1);
                    $debtCapital = 0.0;
                } else if ($debtCapital > 0.0 && $_requestPayment > 0.0) {
                    $pc = $_requestPayment;
                    $_requestPayment = 0.0;
                    $debtCapital = round($debtCapital - $pc, 1);
                }
                if ($debtInterest > 0.0 && $_requestPayment > 0.0 && $_requestPayment >= $debtInterest) {
                    $pi = $debtInterest;
                    $_requestPayment = round($_requestPayment - $debtInterest, 1);
                    $debtInterest = 0.0;
                } else if ($debtInterest > 0.0 && $_requestPayment > 0.0) {
                    $pi = $_requestPayment;
                    $_requestPayment = 0.0;
                    $debtInterest = round($debtInterest - $pi, 1);
                }
            } else {
                if ($debtCapital > 0.0 && $_requestPayment > 0) {
                    $pc = $debtCapital;
                    $pi = $debtInterest;
                    $_requestPayment--;
                    $debtCapital = 0.0;
                    $debtInterest = 0.0;
                }
            }
            if ($_requestPenalty > 0 && $debtPenalty > 0.0) {
                $pp = $debtPenalty;
                $_requestPenalty--;
                $debtPenalty = 0.0;
            }

            $payment_at = $installment->payment_at;
            if ($payment_at === null && !$debtCapital > 0.0 && !$debtInterest > 0.0 && ($pc > 0.0 || $pi > 0.0)) {
                $payment_at = $fechaPago;
            }
            if ($pc > 0.0 || $pi > 0.0 || $pp > 0.0) {
                $summaries[] = [
                    'id' => $installment->idPD,
                    'number' => $installment->ncuota,
                    'capitalPayment' => $pc,
                    'newCapitalPayment' => $installment->capital_payment + $pc,
                    'interestPayment' => $pi,
                    'newInterestPayment' => $installment->interest_payment + $pi,
                    'penaltyPayment' => $pp,
                    'newPenaltyPayment' => $installment->delay_payment + $pp,
                    'isFinished' => !$debtCapital > 0.0 && !$debtInterest > 0.0 && !$debtPenalty > 0.0,
                    'payment_at' => $payment_at,
                    'updated_at' => $fechaYHoraPago,
                    'expiration_at' => $installment->fechaProg,
                ];
            }
        }

        if ($isAmount && $requestPayment > ($sumDebtCapital + $sumDebtInterest)) {
            throw new CobroValidationException(['message' => 'El monto a pagar no debe ser mayor a ' . ($sumDebtCapital + $sumDebtInterest), 'success' => false]);
        }
        if (!$isAmount && $requestPayment > $countInstallment) {
            throw new CobroValidationException(['message' => 'El número de cuotas a pagar no debe ser mayor a ' . $countInstallment . '.', 'success' => false]);
        }
        if ($mora > $countPenalty) {
            throw new CobroValidationException(['message' => 'La mora a pagar no debe ser mayor a ' . $penalty . '.', 'success' => false]);
        }

        return [
            'summaries' => $summaries,
            'penalty' => $penalty,
            'debtCapital' => $sumDebtCapital,
            'debtInterest' => $sumDebtInterest,
            'countInstallment' => $countInstallment,
            'countPenalty' => $countPenalty,
            'fechaPago' => $fechaPago,
            'fechaYHoraPago' => $fechaYHoraPago,
        ];
    }
    public static function ejecutar(object $credit, object $caja, string $paymentType, float $value, float $mora, ?int $actorId): array
    {
        $calc = self::calcular($credit, $paymentType, $value, $mora);
        try {
            return TransaccionRepository::persistirCobro($credit, $caja, $calc);
        } catch (CobroValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw new CobroValidationException(['message' => 'Lo sentimos se produjo un error desconocido.', 'success' => false]);
        }
    }
}
