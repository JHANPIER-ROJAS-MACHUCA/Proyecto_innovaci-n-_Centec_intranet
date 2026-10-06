<?php


use CrediSoporte\Domain\Models\Transaction;

$cash = $database::table('tcaja_usuario')
    ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('idCA', $_GET['cashId'])
    ->first();

// los usuario de tipo 2 y 3 solo pueden ver sus detalle de caja
// los de otro tipo pueden ver de cualquier usuario
// operadora y campo
if (($_COOKIE['tuser'] == 3 || $_COOKIE['tuser'] == 4) && $cash->idU != $_COOKIE['user1']) {
    echo 'No hay nada que mostar';
    die();
}

$otrasCajas = 0;
$designacionTransactions = [];
if ($cash->tipo == 1) {
    $designacionTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
        ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
        ->where('tcaja_usuario.idCO', $cash->idCO)
        ->where('tcaja_usu_detal.tipo', 1)
        ->where('tcaja_usu_detal.habilitacion', 4)
        ->get();

    $otrasCajas = $database->table('tcaja_usuario')
        ->where('idCO', $cash->idCO)
        ->where('idCA', '!=', $cash->idCA)
        ->sum('montoFin');
}

$asignacionTransactions = Transaction::where('idCA', $_GET['cashId'])
    ->where('tipo', 1)
    ->where('estadodt', 2)
    ->where('habilitacion', 4)
    ->get();

$cobrosTransactions = Transaction::join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
    ->leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
    ->where('tcaja_usu_detal.idCA', $_GET['cashId'])
    // ->where('tcaja_usu_detal.estadodt', 2)
    ->where('tcaja_usu_detal.tipo', 3)
    ->get();

$ahorroTransactions = Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
    ->join('tahorro_motivo', 'tahorro_deta.moti', 'tahorro_motivo.idam')
    ->join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
    ->where('tcaja_usu_detal.idCA', $_GET['cashId'])
    ->where('tcaja_usu_detal.estadodt', 2)
    ->select(
        'tcaja_usu_detal.idCAD',
        'tcaja_usu_detal.total',
        'tahorro_deta.tipo'
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->selectRaw('if(tahorro_deta.tipo = 7,"INGRESO","EGRESO") as tipo')
    ->get();

$incomeAndExpense = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
    ->where('tcaja_usu_detal.idCA', $_GET['cashId'])
    ->where('tcaja_usu_detal.estadodt', 2)
    ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
    ->whereNull('tcaja_usu_detal.idCuota')
    ->select(
        'tcaja_usu_detal.idCAD',
        'tcaja_usu_detal.total',
        'tahorro_motivo.motivo'
    )
    ->selectRaw('if(tahorro_motivo.tipoM = 1, "INGRESO", "EGRESO") as tipo')
    ->get();

$desembolsoTransactions = Transaction::join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
    ->where('tcaja_usu_detal.idCA', $_GET['cashId'])
    ->where('tcaja_usu_detal.estadodt', 2)
    ->where('tcaja_usu_detal.tipo', 2)
    ->get();

$montoOficina = $cash->tipo == 1 ? $cash->monto : 0;

$totalIncome = 0;
$totalIncomeDigital = 0;
$totalExpense = 0;
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueo de caja</title>
    <style>
        html,
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
        }

        table.table,
        .table th,
        .table td {
            border-collapse: collapse;
            border: 1px solid #B0BEC5;
            padding: 2px 20px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">REPORTE DE MOVIMIENTOS DE CAJA</h2>


    <table style="font-size: 12px;width: 100%;">
        <tbody>
            <tr>
                <td>Usuario</td>
                <td><?php echo $cash->apU . ' ' . $cash->amU . ' ' . $cash->nomU ?></td>
                <td style="width: 0; white-space: nowrap;">Fecha y Hora</td>
                <td style="width: 0; white-space: nowrap;"><?php echo date('d/m/Y H:i') ?></td>
            </tr>
        </tbody>
    </table>

    <h4>INGRESOS</h4>

    <h5>ASIGNACIÓN</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha y hora</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($asignacionTransactions as $transaction) { ?>
                <?php $totalIncome += $transaction->monto ?>
                <tr>
                    <td><?php echo $transaction->created_at ?></td>
                    <td><?php echo number_format($transaction->monto, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (count($asignacionTransactions) === 0) { ?>
                <tr>
                    <td class="text-center" colspan="2">No hay nada que mostar</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h5>COBROS</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Número operación</th>
                <th>Cliente</th>
                <th>Metodo de pago</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cobrosTransactions->where('estadodt', '2') as $transaction) { ?>
                <?php
                if ($transaction->id) {
                    $totalIncomeDigital += $transaction->total;
                } else {
                    $totalIncome += $transaction->total;
                }
                ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->ap . ' ' . $transaction->am . ' ' . $transaction->nom ?></td>
                    <td><?php echo $transaction->id ? mb_strtoupper($transaction->payment_method) : 'EFECTIVO' ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (count($cobrosTransactions) === 0) { ?>
                <tr>
                    <td class="text-center" colspan="3">No hay nada que mostar</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h5>OTROS INGRESOS</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Número operación</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incomeAndExpense->where('tipo', 'INGRESO') as $transaction) { ?>
                <?php $totalIncome += $transaction->total ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->motivo ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php foreach ($ahorroTransactions->where('tipo', 'INGRESO') as $transaction) { ?>
                <?php $totalIncome += $transaction->total ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->customer ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (
                count($incomeAndExpense->where('tipo', 'INGRESO')) === 0 &&
                count($ahorroTransactions->where('tipo', 'INGRESO')) === 0
            ) { ?>
                <tr>
                    <td class="text-center" colspan="3">No hay nada que mostar.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4>EGRESOS</h4>

    <?php if (count($designacionTransactions) > 0) { ?>
        <h5>DESIGNACIONES</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Número de operación</th>
                    <th>Usuario</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($designacionTransactions as $transaction) { ?>
                    <?php $totalExpense += $transaction->monto ?>
                    <tr>
                        <td><?php echo $transaction->idCAD ?></td>
                        <td><?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?></td>
                        <td><?php echo number_format($transaction->monto, 2) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <h5>DESEMBOLSOS</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Número operación</th>
                <th>Cliente</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($desembolsoTransactions as $transaction) { ?>
                <?php $totalExpense += $transaction->total ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->ap . ' ' . $transaction->am . ' ' . $transaction->nom ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (count($desembolsoTransactions) === 0) { ?>
                <tr>
                    <td class="text-center" colspan="3">No hay nada que mostar</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h5>PAGOS Y GASTOS</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Número operación</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incomeAndExpense->where('tipo', 'EGRESO') as $transaction) { ?>
                <?php $totalExpense += $transaction->total ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->motivo ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php foreach ($ahorroTransactions->where('tipo', 'EGRESO') as $transaction) { ?>
                <?php $totalExpense += $transaction->total ?>
                <tr>
                    <td><?php echo $transaction->idCAD ?></td>
                    <td><?php echo $transaction->customer ?></td>
                    <td><?php echo number_format($transaction->total, 2) ?></td>
                </tr>
            <?php } ?>

            <?php if (
                count($incomeAndExpense->where('tipo', 'EGRESO')) === 0 &&
                count($ahorroTransactions->where('tipo', 'EGRESO')) === 0
            ) { ?>
                <tr>
                    <td class="text-center" colspan="3">No hay nada que mostar</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4>EXTORNADOS</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Número operación</th>
                <th>Cliente</th>
                <th>Metodo de pago</th>
                <th>Estado</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $canceledTransactions = $cobrosTransactions->where('estadodt', '1');
            ?>
            <?php foreach ($canceledTransactions as $canceledTransaction) { ?>
                <tr>
                    <td><?php echo $canceledTransaction->idCAD ?></td>
                    <td><?php echo $canceledTransaction->ap . ' ' . $canceledTransaction->am . ' ' . $canceledTransaction->nom ?></td>
                    <td><?php echo $canceledTransaction->id ? mb_strtoupper($canceledTransaction->payment_method) : 'EFECTIVO' ?></td>
                    <td><?php echo 'ANULADO' ?></td>
                    <td><?php echo number_format($canceledTransaction->total, 2) ?></td>
                </tr>
            <?php } ?>
            <?php if (count($canceledTransactions) === 0) { ?>
                <tr>
                    <td colspan="5" class="text-center">No hay nada que mostrar.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="table" style="margin: 50px auto 0;">
        <thead>
            <tr>
                <?php if ($cash->tipo == 1) { ?>
                    <th>Inicio</th>
                    <th>Otras cajas</th>
                <?php } ?>
                <th colspan="2">TOTAL INGRESO</th>
                <th>TOTAL EGRESO</th>
                <th>SALDO DISPONIBLE</th>
                <th>TOTAL EFECTIVO</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center;">
                <?php if ($cash->tipo == 1) { ?>
                    <td rowspan="2"><?php echo number_format($cash->monto, 2) ?></td>
                    <td rowspan="2"><?php echo number_format($otrasCajas, 2) ?></td>
                <?php } ?>
                <td>EFECTIVO</td>
                <td>DIGITAL</td>
                <td rowspan="2"><?php echo number_format($totalExpense, 2) ?></td>
                <td rowspan="2"><?php echo number_format($montoOficina + $otrasCajas + $totalIncome + $totalIncomeDigital - $totalExpense, 2) ?></td>
                <td rowspan="2"><?php echo number_format($montoOficina + $otrasCajas + $totalIncome - $totalExpense, 2) ?></td>
            </tr>
            <tr>
                <td><?php echo number_format($totalIncome, 2) ?></td>
                <td><?php echo number_format($totalIncomeDigital, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <table style="margin: 0 auto; margin-top: 100px;">
        <tbody>
            <tr>
                <td style="border-top: 1px solid black;width: 300px;text-align: center;">
                    <?php echo $cash->apU . ' ' . $cash->amU . ' ' . $cash->nomU ?>
                </td>
                <td style="width: 100px;"></td>
                <td style="border-top: 1px solid black;width: 300px;text-align: center;">ADMINISTRACIÓN</td>
            </tr>
        </tbody>
    </table>
</body>

</html>