<?php


use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Request\Request;
use Luecano\NumeroALetras\NumeroALetras;

$request = new Request();
$formatter = new NumeroALetras();

$operationId = $request->get('operacion');

$office = $database->table('toficina')->first();
$empresa = $database->table('tdatos')->first();

$comentario = $empresa->comentario;

$transaction = $database->table('tcaja_usu_detal as transactions')
    ->join('tclie_general as clients', 'transactions.cliente', 'clients.idCG')
    ->join('tcaja_usuario as cash', 'transactions.idCA', 'cash.idCA')
    ->join('tusuario as users', 'cash.idU', 'users.idU')
    ->leftJoin('transaction_details', 'transactions.idCAD', 'transaction_details.transaction_id')
    ->where('transactions.idCAD', $operationId)
    ->where('transactions.tipo', '3')
    ->select(
        'transactions.*',
        'clients.idCG',
        'clients.nom',
        'clients.ap',
        'clients.am',
        'users.apU',
        'users.amU',
        'users.nomU',
        'transaction_details.payment_method'
    )
    ->first();

$credit = Credit::with('customer')->find($transaction->conejo);

$cuotas = Installment::join('installment_payment', 'tpresta_detalle.idPD', 'installment_payment.installment_id')
->where('installment_payment.payment_id', $transaction->idCAD)
    ->pluck('tpresta_detalle.number');

// $idInstallments = [];
// $installments = [];
// $numCuenta = '';
// $totalCuotas = 0;

// if ($transaction) {
//     $idInstallments = array_filter(explode(',', $transaction->idCuota));
//     $idMoras = array_filter(explode(',', $transaction->idMora));
//     $idInstallmentsAndMoras = array_unique($idInstallments + $idMoras);
//     $idInstallmentsString = implode(',', $idInstallmentsAndMoras);

//     $installments = Installment::join('tprestamo as credits', 'tpresta_detalle.idP', 'credits.idP')
//         ->whereIn('tpresta_detalle.idPD', $idInstallmentsAndMoras)
//         ->select(
//             'tpresta_detalle.idPD',
//             'tpresta_detalle.number',
//             'tpresta_detalle.idP',
//             'credits.n_cuota'
//         )
//         ->get();

//     $numCuenta = "";
//     $totalCuotas = "";
//     $numInstallmentInArray = [];

//     foreach ($installments as $installment) {
//         $numCuenta = str_pad($installment['idP'], 5, '0', STR_PAD_LEFT);
//         $numCuota = str_pad($installment['ncuota'], 2, '0', STR_PAD_LEFT);
//         $totalCuotas = $installment['n_cuota'];

//         if (in_array($installment['idPD'], $idInstallments)) {
//             array_push($numInstallmentInArray, $numCuota);
//         }
//     }
// }

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
            size: 58mm 140mm;
        }

        .text-xs {
            font-size: 6;
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
            border-color: transparent;
            border-top: 2px dashed #2C3E50;
        }
    </style>
</head>

<body class="text-xs px-4">

    <?php if ($transaction) { ?>

        <div class="text-center"><?php echo $empresa->abre . ' ' . $empresa->siglas ?> - CRÉDITOS</div>
        <div class="text-center"><?php echo $empresa->direccion ?></div>

        <h4 class="text-center font-semibold">CREDITOS - COBRANZAS</h4>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td><span class="font-semibold">Cuenta:</span> <?php echo $credit->idP ?></td>
                    <td class="text-right"><span class="font-semibold">Cod. Cliente:</span> <?php echo str_pad($credit->customer->idCG, 5, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="font-semibold">Cliente:</span> <?php echo $credit->customer->ap . ' ' . $credit->customer->am . ' ' . $credit->customer->nom ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Próxima fecha de pago:</span></td>
                    <td class="text-right"><?php echo  $transaction->next_payment ? date('d-m-Y', strtotime($transaction->next_payment)) : 'NO DEFINIDO' ?></td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Cuota:</span>
                        <?php foreach ($cuotas as $cuota) { ?>
                            <span><?php echo str_pad($cuota, 2, '0', STR_PAD_LEFT) ?></span>
                        <?php } ?>
                        <?php
                        // $i = $installments->whereIn('idPD', $idInstallments)->pluck('ncuota');
                        // if (count($i) > 5) {
                        //     echo str_pad($i[0], 2, '0', STR_PAD_LEFT) . '-' . $i[count($i) - 1];
                        // } else {
                        //     $number = 0;
                        //     foreach ($i as $number_cuota) {
                        //         if ($number === count($i)) {
                        //             echo $number_cuota;
                        //         } else {
                        //             echo $number_cuota . ',';
                        //         }
                        //         $number++;
                        //     }
                        // }
                        ?>
                    </td>
                    <td class="text-right" style="vertical-align: top;"><span class="font-semibold">Resta:</span> <?php echo $transaction->cuotas_pendientes . '/' . $credit->number_installments ?></td>
                </tr>
            </tbody>
        </table>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td><span class="font-semibold">Capital:</span></td>
                    <td class="text-right">S/. <?php echo number_format($transaction->capital, 2) ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Interes:</span></td>
                    <td class="text-right">S/. <?php echo number_format($transaction->interest, 2) ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Mora:</span></td>
                    <td class="text-right">S/. <?php echo number_format($transaction->mora, 2) ?></td>
                </tr>
                <?php if ($transaction->discount > 0) { ?>
                    <tr>
                        <td><span class="font-semibold">Descuento:</span></td>
                        <td class="text-right">- S/. <?php echo number_format($transaction->discount, 2) ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><span class="font-semibold">Total pagado:</span></td>
                    <td class="text-right">S/. <?php echo number_format($transaction->total, 2) ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-xs">SON <?php echo $formatter->toInvoice($transaction->total, 2, "soles"); ?></td>
                </tr>
            </tbody>
        </table>

        <hr>

        <table>
            <tbody>
                <tr>
                    <td colspan="2"><span class="font-semibold">Número de operación: </span> <?php echo str_pad($operationId, 5, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="font-semibold">Fecha y Hora:</span> <?php echo $transaction->created_at ? date('d/m/Y h:i A', strtotime($transaction->created_at)) : 'NO DEFINIDO' ?></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="font-semibold">Usuario:</span> <?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="font-semibold">Metodo de pago:</span> <?php echo mb_strtoupper($transaction->payment_method ?? 'Efectivo') ?></td>
                </tr>
            </tbody>
        </table>

        <hr>
        <?php if ($transaction->estadodt == '1') { ?>
            <div style="text-align: center; color: red; font-weight: 700; font-size: 20px;">TRANSACCIÓN ANULADO</div>
        <?php } ?>

        <p class="text-center">Consultas y Sugerencias: <?php echo $office->telefono ?></p>

        <!-- <p class="text-center">*** SU PUNTUALIDAD ES SU MEJOR GARANTIA PARA SU PROXIMO CREDITO ***</p> -->

    <?php } else { ?>

        <div style="text-align: center;">LA TRANSACCIÓN NO EXISTE</div>

    <?php } ?>

</body>

</html>