<?php


use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;

$credit = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->find($_GET['creditId']);

// $installmentsIds = Installment::where('idP', $_GET['creditId'])
//     ->select('idPD')
//     ->get()
//     ->pluck('idPD');

// $transactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
//     ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
//     ->where('cliente', $credit->idCG)
//     ->where('estadodt', '2')
//     ->orderBy('idCAD', 'desc')
//     ->limit(300)
//     ->select(
//         'tcaja_usu_detal.total',
//         'tcaja_usu_detal.cuota',
//         'tcaja_usu_detal.idCuota',
//         'tcaja_usu_detal.mora',
//         'tcaja_usu_detal.idMora',
//         'tcaja_usu_detal.created_at',
//         'tusuario.apU'
//     )
//     ->get();

// $_transactions = [];
// foreach ($transactions as $transaction) {
//     $cuotasPagadas = explode(',', $transaction->idCuota);
//     $morasPagadas = explode(',', $transaction->idMora);
//     $cuotasIds = array_unique($cuotasPagadas + $morasPagadas);

//     foreach ($installmentsIds as $installmentId) {
//         if (in_array($installmentId, $cuotasIds)) {
//             array_push($_transactions, $transaction);
//             break;
//         }
//     }
// }

$transactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
    ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
    ->where('tcaja_usu_detal.conejo', $_GET['creditId'])
    ->where('tcaja_usu_detal.tipo', '3')
    ->where('tcaja_usu_detal.estadodt', '2')
    ->get();

$size = 13 * count($transactions) + 230;

$totalCuota = 0;
$totalMora = 0;
$total = 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de pago</title>
    <style>
        html {
            margin: 5px 15px;
        }

        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 9;
        }

        @page {
            size: 80mm <?php echo $size ?>px;
        }

        .text-xs {
            font-size: 9;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th {
            border-bottom: 1px dashed #90A4AE;
            padding-bottom: 5px;
        }

        .table td {
            border-collapse: collapse;
            /* border-top: 1px solid #90A4AE;
            border-bottom: 1px solid #90A4AE; */
            height: 13px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="text-align: center;margin-top: 20px;font-weight: bold;">DETALLE DE PAGOS</div>

    <table style="width: 100%; font-size: 10px; margin-top: 5px;">
        <tbody>
            <tr>
                <td colspan="2" style="text-align: right;">Fecha y hora: <?php echo date('d/m/Y H:i') ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CLIENTE:</td>
                <td><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CUENTA:</td>
                <td><?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">N° CREDITO:</td>
                <td><?php echo str_pad($credit->n_credito, 2, '0', STR_PAD_LEFT) ?></td>
            </tr>
        </tbody>
    </table>


    <table class="table" style="margin-top: 10px; font-size: 10px;">
        <thead>
            <tr>
                <th>USUARIO</th>
                <th>FECHA</th>
                <th>CUOTA</th>
                <th>MORA</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction) { ?>
                <?php
                $totalCuota += $transaction->cuota;
                $totalMora += $transaction->mora;
                $total += $transaction->total;
                ?>
                <tr>
                    <td><?php echo $transaction->apU ?></td>
                    <td><?php echo $transaction->created_at ? date('d/m/y H:i', strtotime($transaction->created_at)) : '' ?></td>
                    <td style="white-space: nowrap;"><?php echo number_format($transaction->cuota, 2) ?></td>
                    <td style="white-space: nowrap;"><?php echo number_format($transaction->mora, 2) ?></td>
                    <td style="white-space: nowrap; font-weight: bold;"><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (count($transactions) === 0) { ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No hay nada que mostrar</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td style="border: 1px solid #B0BEC5; font-weight: bold;"><?php echo number_format($totalCuota, 2) ?></td>
                <td style="border: 1px solid #B0BEC5; font-weight: bold;"><?php echo number_format($totalMora, 2) ?></td>
                <td style="border: 1px solid #B0BEC5; font-weight: bold;"><?php echo number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>