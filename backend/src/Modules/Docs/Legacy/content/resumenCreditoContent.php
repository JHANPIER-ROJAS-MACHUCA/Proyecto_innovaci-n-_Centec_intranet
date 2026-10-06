<?php


use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;

$currentDate = date('Y-m-d');

$credit = Credit::with(['installments', 'condoneDates'])
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->whereIn('estado', [4, 5])
    ->find($_GET['creditId']);

if (!$credit) {
    http_response_code(404);
    die();
}

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->penalty);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;

$resumenInstallment = (object) [
    'capital' => 0,
    'deudaDeCuotas' => 0,
    'paidOut' => 0,
    'mora' => 0,
    'daysOfDelay' => 0
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen credito</title>
    <style>
        html,
        body {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 13px;
        }

        table {
            text-align: center;
        }

        .table {
            width: 100%;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid #78909C;
            border-collapse: collapse;
        }

        .table-resumen,
        .table-resumen th,
        .table-resumen td {
            border: 1px solid #78909C;
            border-collapse: collapse;
        }

        .table-header {
            width: 100%;
        }

        .table-header,
        .table-header th,
        .table-header td {
            border: 1px solid #78909C;
            border-collapse: collapse;
        }

        .table-header th {
            font-size: 13px;
        }

        td {
            padding: 2px 10px;
        }
    </style>
</head>

<body>
    <div style="text-align: right;"><?php echo 'Fecha y Hora generado ' . date('d/m/Y h:i A') ?></div>

    <table class="table-header" style="margin-top: 10px;">
        <thead>
            <tr>
                <th>CLIENTE</th>
                <th>CUENTA</th>
                <th>TIPO</th>
                <th>FECHA DESEMBOLSO</th>
                <th>CAPITAL DESEMBOLSADO</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-transform: uppercase;">
                <td style="white-space: nowrap;"><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                <td><?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></td>
                <td><?php echo $credit->name ?></td>
                <td><?php echo date('d/m/Y', strtotime($credit->fechaDesembolso)) ?></td>
                <td><span style="font-weight: bold;">S/.<?php echo number_format($credit->capital / 10, 2) ?></span></td>
            </tr>
        </tbody>
    </table>

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>N° cuota</th>
                <th>Estado</th>
                <th>Capital</th>
                <th>Interes</th>
                <th>Monto pagado</th>
                <th>Fecha programada</th>
                <th>Fecha pagada</th>
                <th>Dias atraso</th>
                <th>Mora</th>
                <th>Con.</th>
                <th>Pag.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalDiasAtrasados = 0;
            $sumCapital = 0;
            $sumInterest = 0;

            $sumCapitalPayment = 0;
            $sumInterestPayment = 0;

            $sumCapitalDebt = 0;
            $sumInterestDebt = 0;
            $sumPenalty = 0;

            $sumPenaltyPayment = 0;
            $sumPenaltyCondoned = 0;
            ?>
            <?php foreach ($credit->installments as $keyInstallment => $installment) { ?>
                <?php

                $sumCapital += $installment->capital;
                $sumInterest += $installment->interest;

                $sumCapitalPayment += $installment->capital_payment;
                $sumInterestPayment += $installment->interest_payment;

                $capitalDebt = $installment->capitalDebt();
                $interestDebt = $installment->interestDebt();

                $sumCapitalDebt += $capitalDebt;
                $sumInterestDebt += $interestDebt;

                $sumPenaltyPayment += $installment->delay_payment;
                $sumPenaltyCondoned += $installment->delay_condoned;

                $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;

                $penalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
                $sumPenalty += $penalty;

                $diasAtrasados = 0;

                if ($installment->expiration_at < date('Y-m-d')) {
                    if (
                        ($capitalDebt + $interestDebt) === 0 &&
                        $installment->payment_date > $installment->expiration_at
                    ) {
                        $ini = new DateTime($installment->expiration_at);
                        $fin = new DateTime($installment->payment_date);
                        $diasAtrasados = $ini->diff($fin)->days;
                    } else if (($capitalDebt + $interestDebt) > 0) {
                        $ini = new DateTime($installment->expiration_at);
                        $fin = new DateTime(date('Y-m-d'));
                        $diasAtrasados = $ini->diff($fin)->days;
                    }
                }

                $totalDiasAtrasados += $diasAtrasados;

                ?>
                <tr style="<?php echo $installment->expiration_at === $currentDate ? "background-color: #FFF9C4;" : '' ?>">
                    <td><?php echo $installment->number ?></td>
                    <td>
                        <div style="white-space: nowrap;font-weight: bold;">
                            <?php if (
                                (($installment->capital_payment + $installment->interest_payment) > 0) &&
                                (($installment->capitalDebt() + $installment->interestDebt()) === 0)
                            ) { ?>
                                <span style="color: green">CAN.</span>
                            <?php } else if (($installment->capital_payment + $installment->interest_payment) > 0) { ?>
                                <span style="color: orange">INC.</span>
                            <?php } else if ($installment->expiration_at < $currentDate) { ?>
                                <span style="color: red">NP</span>
                            <?php } ?>
                        </div>
                    </td>
                    <td>
                        <span style="white-space: nowrap; font-weight: bold;">
                            <?php echo number_format($installment->capital / 10, 2) ?>
                        </span>
                    </td>
                    <td>
                        <span style="white-space: nowrap; font-weight: bold;">
                            <?php echo number_format($installment->interest / 10, 2) ?>
                        </span>
                    </td>
                    <td>
                        <span style="white-space: nowrap; font-weight: bold;">
                            <?php echo ($installment->capital_payment + $installment->interest_payment) > 0 ? number_format(($installment->capital_payment + $installment->interest_payment) / 10, 2) : '' ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($installment->expiration_at)) ?></td>
                    <td>
                        <span style="white-space: nowrap">
                            <?php echo $installment->payment_date !== null ? date('d/m/Y H:i', strtotime("$installment->payment_date $installment->payment_time")) : '' ?>
                        </span>
                    </td>
                    <td><?php echo $diasAtrasados ?></td>
                    <td>
                        <div style="color: #DD2C00; font-weight: 700;">
                            <?php echo $penalty > 0 ? number_format($penalty / 10, 2) : ''; ?>
                        </div>
                    </td>
                    <td><?php echo $installment->delay_condoned > 0 ? number_format($installment->delay_condoned / 10, 2) : '' ?></td>
                    <td><?php echo $installment->delay_payment > 0 ? number_format($installment->delay_payment / 10, 2) : '' ?></td>
                </tr>
            <?php } ?>
            <?php

            $lastInstallment = $credit->installments[count($credit->installments) - 1];

            $diasVencidos = 0;

            if ($lastInstallment->expiration_at < date('Y-m-d')) {
                $ini = new DateTime($lastInstallment->expiration_at);
                $fin = new DateTime(date('Y-m-d'));
                $diff = $ini->diff($fin);

                $diasVencidos = $diff->days;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td style="white-space: nowrap;"><?php echo number_format($sumCapital / 10, 2) ?></td>
                <td style="white-space: nowrap;"><?php echo number_format($sumInterest / 10, 2) ?></td>
                <td style="white-space: nowrap;"><?php echo number_format(($sumCapitalPayment + $sumInterestPayment) / 10, 2) ?></td>
                <td>
                    <?php if ($diasVencidos > 0) { ?>
                        Finalizo hace <?php echo $diasVencidos ?> dias
                    <?php } ?>
                </td>
                <td></td>
                <td><?php echo $totalDiasAtrasados ?></td>
                <td><?php echo number_format($sumPenalty / 10, 2); ?></td>
                <td><?php echo number_format($sumPenaltyCondoned / 10, 2); ?></td>
                <td><?php echo number_format($sumPenaltyPayment / 10, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <table class="table-resumen" style="margin: 20px auto 0 auto;">
        <thead style="font-size: 14px;">
            <tr>
                <th>RESUMEN</th>
                <th>CAPITAL</th>
                <th>PAGADO</th>
                <th>DEUDA CAPITAL</th>
                <th>DEUDA MORA</th>
                <?php if ($credit->discount > 0) { ?>
                    <th>DESCUENTO</th>
                <?php } ?>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-size: 18px; font-weight: bold;">
                        <?php echo number_format($totalDiasAtrasados / $credit->number_installments, 2) ?>
                    </div>
                </td>
                <td>
                    <div style="font-size: 18px; font-weight: bold;">
                        S/ <?php echo number_format(($sumCapital + $sumInterest) / 10, 2) ?>
                    </div>
                </td>
                <td>
                    <div style="font-size: 18px; font-weight: bold;">
                        S/ <?php echo number_format(($sumCapitalPayment + $sumInterestPayment) / 10, 2) ?>
                    </div>
                </td>
                <td>
                    <div style="font-size: 18px; font-weight: bold; color: #DD2C00;">
                        <?php if ($credit->estado === "4") { ?>
                            <span>S/ <?php echo number_format(($sumCapitalDebt + $sumInterestDebt) / 10, 2) ?></span>
                        <?php } else { ?>
                            <span>00.00</span>
                        <?php } ?>
                    </div>
                </td>
                <td>
                    <div style="font-size: 18px; font-weight: bold; color: #DD2C00;">
                        <?php if ($credit->estado === "4") { ?>
                            <span>S/ <?php echo number_format($sumPenalty / 10, 2) ?></span>
                        <?php } else { ?>
                            <span>S/ 00.00</span>
                        <?php } ?>
                    </div>
                </td>

                <?php if ($credit->discount > 0) { ?>
                    <td>
                        <div style="font-size: 18px;font-weight: bold;">
                            S/ <?php echo number_format($credit->discount, 2) ?>
                        </div>
                    </td>
                <?php } ?>

                <td>
                    <div style="font-size: 18px; font-weight: bold; color: #DD2C00;">
                        <?php if ($credit->estado === "4") { ?>
                            <span>S/ <?php echo number_format(($sumCapitalDebt + $sumInterestDebt + $sumPenalty) / 10, 2) ?></span>
                        <?php } else { ?>
                            <span>S/ 00.00</span>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $credit->estado === '5' && $credit->discount > 0 ? 7 : 6 ?>">
                    <div style="font-size: 18px; font-weight: bold; padding: 5px 0;">
                        <?php
                        switch ($credit->estado) {
                            case '4':
                                echo 'ACTIVO';
                                break;
                            case '5':
                                if ($credit->discount > 0) {
                                    echo 'CANCELADO CON DESCUENTO';
                                } else {
                                    echo 'CANCELADO SIN DESCUENTO';
                                }
                                break;

                            default:
                                echo 'NO DEFINIDO';
                                break;
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>