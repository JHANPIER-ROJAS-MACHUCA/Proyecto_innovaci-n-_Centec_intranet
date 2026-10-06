<?php


use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Request\Request;
use Luecano\NumeroALetras\NumeroALetras;
use Picqer\Barcode\BarcodeGeneratorHTML;

$generator = new BarcodeGeneratorHTML();

$request = new Request();
$formatter = new NumeroALetras();

$operationId = $request->get('operacion');

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

$office = $database->table('toficina')->first();
$empresa = $database->table('tdatos')->first();

$credit = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->leftJoin('tcaja_usu_detal', 'tprestamo.idP', 'tcaja_usu_detal.conejo')
    ->where('tprestamo.idP', $request->creditId)
    ->whereIn('estado', [4, 5])
    ->select('tprestamo.*', 'tclie_general.*', 'tusuario.*', 'tcaja_usu_detal.idCAD', 'tcaja_usu_detal.created_at')
    ->first();

$credits = [];

if ($credit) {
    $credits = Credit::with('installments')->where('idCG', $credit->idCG)
        ->whereIn('estado', [4, 5])
        ->where('idP', '!=', $credit->idP)
        ->limit(3)
        ->get();
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOUCHER</title>

    <style>
        html {
            margin: 25px 15px;
        }

        html,
        body {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 10;
        }

        @page {
            size: 80mm 200mm;
        }

        .text-xs {
            font-size: 9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-semibold {
            font-weight: 700;
        }

        hr {
            border-bottom: 0;
            border-top: 2px dashed #808B96;
        }
    </style>
</head>

<body class="text-xs px-4">

    <?php if ($credit) { ?>

        <div class="text-center"><?php echo $empresa->abre . ' ' . $empresa->siglas ?> - CRÉDITOS</div>
        <div class="text-center"><?php echo $empresa->direccion ?></div>

        <h4 class="text-center font-semibold">CREDITOS - DESEMBOLSO</h4>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td><span class="font-semibold">Cod. Cliente:</span> <?php echo str_pad($credit->idCG, 5, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Cliente:</span> <?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Asesor:</span> <?php echo $credit->apU . ' ' . $credit->amU . ' ' . $credit->nomU ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Número operación:</span> <?php echo $credit->idCAD ?></td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Fecha y hora:</span>
                        <?php echo $credit->created_at ? date('d/m/Y H:i:s', strtotime($credit->created_at)) : '' ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr>

        <div style="text-align: center; font-weight: bold; padding: 3px 0;">Historial de sus tres últimos creditos</div>

        <hr>

        <table style="width: 100%;">
            <tbody>

                <?php foreach ($credits as $key => $creditItem) { ?>

                    <?php
                    $delay->penalty = $credit->penalty;
                    $delay->payment_period = $credit->payment_period;
                    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

                    $moraGenerado = 0;
                    $moraPagado = 0;
                    $saldo = 0;
                    foreach ($creditItem->installments as $keyIns => $installment) {
                        $capitalDebt = $installment->capitalDebt();
                        $interestDebt = $installment->interestDebt();

                        $saldo += ($capitalDebt + $interestDebt);

                        $nextInstallment = $creditItem->installments[$keyIns + 1] ?? null;
                        $moraGenerado = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                        $moraPagado += $installment->delay_payment;
                    }

                    ?>

                    <tr>
                        <td style="<?php echo $key != 2 ? 'border-bottom: 2px dashed #808B96;' : '' ?>">
                            <table style="width: 100%; padding: 5px 0;">
                                <tbody>
                                    <tr>
                                        <td colspan="3" style="padding: 0;">Fec. Des.: <?php echo $creditItem->fechaDesembolso ?></td>
                                        <td colspan="3" style="text-align: right; padding: 0;">Desem.: <?php echo number_format($creditItem->capital / 10, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 0;">
                                            Mor. Gen.: <?php echo number_format($moraGenerado / 10, 2) ?>
                                        </td>
                                        <td colspan="2">
                                            Mor. Pag.: <?php echo number_format($moraPagado / 10, 2) ?>
                                        </td>
                                        <td colspan="2" style="text-align: right; padding: 0;">
                                            Saldo: <?php echo number_format($saldo / 10, 2) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">TOTAL DESEMBOLSADO:</td>
                    <td style="font-weight: bold; font-size: 1.1em; text-align: right;">S/. <?php echo number_format($credit->capital / 10, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 20px;">
            <tbody>
                <tr>
                    <td>Recibi conforme:</td>
                    <td style="vertical-align: bottom;">
                        <div style="border: 1px solid #2C3E50; width: 150px;"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr>

        <p class="text-center">Consultas y Sugerencias: <?php echo $office->telefono ?></p>

        <p class="text-center">*** SU PUNTUALIDAD ES SU MEJOR GARANTIA PARA SU PROXIMO CREDITO ***</p>

        <hr>


        <?php $codigo = str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?>
        <table style="width: 100%; padding-bottom: 20px;">
            <tbody>
                <tr>
                    <td>Codigo: <?php echo $codigo ?></td>
                    <td style="width: 0px;">
                        <?php echo $generator->getBarcode($codigo, $generator::TYPE_CODE_128) ?>
                    </td>
                </tr>
            </tbody>
        </table>

    <?php } else { ?>

        <div style="text-align: center;">LA TRANSACCIÓN NO EXISTE</div>

    <?php } ?>

</body>

</html>