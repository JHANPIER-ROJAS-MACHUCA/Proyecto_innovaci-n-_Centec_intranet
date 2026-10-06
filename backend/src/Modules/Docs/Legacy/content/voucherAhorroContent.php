<?php


use CrediSoporte\Domain\Request\Request;
use Luecano\NumeroALetras\NumeroALetras;

$request = new Request();
$formatter = new NumeroALetras();

$operationId = $request->get('operacion');

$office = $database->table('toficina')->first();
$empresa = $database->table('tdatos')->first();

$comentario = $empresa->comentario;

$transaction = $database->table('tclie_general')
    ->select(
        'tclie_general.idCG',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tahorro.idA',
        'tahorro_deta.idAd',
        'tahorro_deta.monto',
        'tahorro_deta.fecha',
        'tahorro_deta.tipo',
        'tahorro_deta.balance',
        'tahorro_deta.idU',
        'tahorro_deta.estad',
        'tahorro_motivo.motivo',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU'
    )
    ->join('tahorro', 'tclie_general.idCG', 'tahorro.id')
    ->join('tahorro_deta', 'tahorro.idA', 'tahorro_deta.idA')
    ->join('tusuario', 'tahorro_deta.idU', 'tusuario.idU')
    ->leftJoin('tahorro_motivo', 'tahorro_deta.moti', 'tahorro_motivo.idam')
    ->where('tahorro_deta.idAd', $operationId)
    ->first();

$lastTransaction = $database->table('tahorro_deta')
    // ->join('tahorro', 'tahorro_deta.idA', 'tahorro.idA')
    // ->join('tclie_general', 'tahorro.id', 'tclie_general.idCG')
    ->where('tahorro_deta.idA', $transaction->idA)
    ->where('tahorro_deta.idAd', '!=', $operationId)
    ->where('estad', 1)
    ->limit(3)
    ->orderBy('tahorro_deta.idAd', 'desc')
    ->get();

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
            size: 80mm 150mm;
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
            border-top: 2px dashed #808B96;
            border-bottom: 0;
        }
    </style>
</head>

<body class="text-xs px-4">

    <?php if ($transaction) { ?>

        <div class="text-center"><?php echo $empresa->abre . ' ' . $empresa->siglas ?> - AHORROS</div>
        <div class="text-center"><?php echo $empresa->direccion ?></div>

        <h4 class="text-center font-semibold"><?php echo $transaction->tipo == 7 ? 'DEPOSITO' : 'RETIRO' ?> - AHORROS</h4>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td><span class="font-semibold">Codigo de cliente:</span> <?php echo str_pad($transaction->idCG, 5, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Cliente:</span> <?php echo $transaction->ap . ' ' . $transaction->am . ' ' . $transaction->nom ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">N° cuenta:</span> <?php echo str_pad($transaction->idA, 5, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <td><span class="font-semibold">Motivo:</span> <?php echo $transaction->motivo ?></td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Número de operacion:</span> <?php echo str_pad($transaction->idAd, 5, '0', STR_PAD_LEFT) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Fecha y Hora:</span> <?php echo $transaction->fecha ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Usuario: </span> <?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="text-align: center; font-weight: 700;">
                        Ultimos movimientos
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <hr /> -->

        <?php foreach ($lastTransaction as $item) { ?>
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($item->fecha)) ?></td>
                        <td style="text-align: right;">
                            <?php echo $item->tipo === "7" ? '+' : '-' ?>
                            <?php echo 'S/ ' . number_format($item->monto, 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>

        <?php if (count($lastTransaction) === 0) { ?>
            <div style="text-align: center;">No hay nada que mostrar.</div>
        <?php } ?>

        <hr>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">
                        <?php echo $transaction->tipo == 7 ? 'TOTAL ABONADO: ' : 'TOTAL RETIRADO' ?>
                    </td>
                    <td style="font-weight: bold; font-size: 1.1em; text-align: right;">
                        <?php echo 'S/. ' . $transaction->monto ?>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2">SON <?php echo $formatter->toInvoice($transaction->monto, 2, "soles"); ?></td>
                </tr> -->
                <tr>
                    <td><span class="font-semibold">Saldo en la cuenta: </span></td>
                    <td style="text-align: right;"><?php echo 'S/. ' . number_format($transaction->balance, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 15px; margin-bottom: 15px;">
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

        <p style="text-align: center;">
            *** SU PUNTUALIDAD ES SU MEJOR GARANTIA
            PARA SU PROXIMO CREDITO ***
        </p>

        <!-- <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <span class="font-semibold">Número de operacion:</span> <?php echo str_pad($transaction->idAd, 5, '0', STR_PAD_LEFT) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Fecha y Hora:</span> <?php echo $transaction->fecha ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="font-semibold">Usuario: </span> <?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?>
                    </td>
                </tr>
            </tbody>
        </table> -->

        <?php if ($transaction->estad == '0') { ?>
            <div style="text-align: center; color: red; font-weight: 700; font-size: 20px;">TRANSACCIÓN ANULADO</div>
        <?php } ?>

    <?php } else { ?>

        <div style="text-align: center;">LA TRANSACCIÓN NO EXISTE</div>

    <?php } ?>

</body>

</html>