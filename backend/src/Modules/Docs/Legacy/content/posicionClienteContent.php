<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;


function riskProfileToString($riskProfile)
{
    switch ($riskProfile) {
        case 'red':
            return 'Alto';
        case 'yellow':
            return 'Mediano';
        case 'gray':
            return 'No registra información en el mes';
        case 'green':
            return 'Sin riesgo';
    }
}

$request = new Request();

$customer = Customer::leftJoin('dictionaries', 'tclie_general.risk_profile_id', 'dictionaries.id')
    ->find($request->clientId);

if (!$customer) {
    http_response_code(404);
    die();
}

$conyuge = Credit::join('tvinculacion', 'tprestamo.idV', 'tvinculacion.idV')
    ->join('tclie_general', 'tvinculacion.conyugue', 'tclie_general.idCG')
    ->where('tprestamo.idCG', $request->clientId)
    ->orderBy('tprestamo.idP', 'desc')
    ->first();

$creditsVigentes = Credit::with('installments')
    ->join('tusuario', 'tprestamo.user_id', 'tusuario.idU')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->where('tprestamo.estado', 4)
    ->where('tprestamo.idCG', $request->clientId)
    ->orderBy('tprestamo.idP', 'desc')
    ->limit(5)
    ->select('tprestamo.*', 'tusuario.apU', 'credit_types.name as credit_type')
    ->get();

$creditsCancelados = Credit::with('installments')
    ->join('tusuario', 'tprestamo.user_id', 'tusuario.idU')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->where('tprestamo.estado', 5)
    ->where('tprestamo.idCG', $request->clientId)
    ->orderBy('tprestamo.idP', 'desc')
    ->limit(5)
    ->select('tprestamo.*', 'tusuario.apU', 'credit_types.name as credit_type')
    ->get();

$business = $database::table('tdatos')->first();

$path = '../../admin/titulo/img/' . $business->logo;
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posición del cliente</title>
    <style>
        html,
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 14px;
            margin: 10px 20px;
        }
    </style>
</head>

<body>

    <table style="width: 100%; table-layout: layout;">
        <tbody>
            <tr>
                <td style="width: 30%;"><img src="<?php echo $base64 ?>" style="height: 40px;" /></td>
                <td style="width: 40%;">
                    <h3 style="text-align: center;">POSICION DEL CLIENTE</h3>
                </td>
                <td style="width: 30%;">
                    <div style="text-align: right;">Fecha: <?php echo date('d/m/y') ?></div>
                    <div style="text-align: right;">Hora: <?php echo date('H:i:s') ?></div>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="font-weight: bold;">Cliente:</td>
                <td><?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom ?></td>
                <td style="font-weight: bold;">Doc. Identidad:</td>
                <td><?php echo $customer->dni ?></td>
                <td style="font-weight: bold;">Nivel riesgo:</td>
                <td><?php echo riskProfileToString($customer->description) ?></td>
            </tr>
            <?php if ($conyuge) { ?>
                <tr>
                    <td style="font-weight: bold;">Nom Cyg o Conviviento:</td>
                    <td><?php echo $conyuge->ap . ' ' . $conyuge->am . ' ' . $conyuge->nom ?></td>
                    <td style="font-weight: bold;">Doc. Identidad:</td>
                    <td><?php echo $conyuge->dni ?></td>
                    <td style="font-weight: bold;">Nivel riesgo:</td>
                    <td><?php echo riskProfileToString($conyuge->description) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4 style="text-align: center;">CREDITOS VIGENTES</h4>
    <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">CUENTA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">MONTO DESEMB</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">MONTO CUOTA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">TEM</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">SALDO CAPITAL</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">FECHA VIGENCIA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">FECHA CANCELACION</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">ATRASO PROMEDIO</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">ASESOR</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">CUOTAS PAGADAS</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">TIPO PRODUCTO</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($creditsVigentes as $credit) { ?>
                <?php
                $sumCapital = 0;
                $sumInterest = 0;

                $saldoCapital = 0;
                $totalDiasAtraso = 0;
                $cuotasPagadas = 0;
                $arrayRetrasos = [];
                foreach ($credit->installments as $installment) {
                    $sumCapital += $installment->capital;
                    $sumInterest += $installment->interest;

                    $capitalDebt = $installment->capitalDebt();
                    $interestDebt = $installment->interestDebt();

                    $saldoCapital += ($capitalDebt + $interestDebt);

                    if (($capitalDebt + $interestDebt) === 0) {
                        $cuotasPagadas++;
                    }

                    $diasAtraso = 0;

                    if (($capitalDebt + $interestDebt) === 0 && $installment->expiration_at < $installment->payment_date) {
                        $ini = new DateTime($installment->expiration_at);
                        $fin = new DateTime($installment->payment_date);
                        $diasAtraso = $ini->diff($fin)->days;
                    } else if (($capitalDebt + $interestDebt) > 0 && $installment->expiration_at < date('Y-m-d')) {
                        $ini = new DateTime($installment->expiration_at);
                        $fin = new DateTime(date('Y-m-d'));
                        $diasAtraso = $ini->diff($fin)->days;
                    }

                    $totalDiasAtraso += $diasAtraso;

                    $arrayRetrasos[] = $installment->number . '/' . $diasAtraso;
                }

                $promedioDiasAtrado = round($totalDiasAtraso / $credit->number_installments, 2);
                ?>

                <tr>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $credit->idP ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo round((($sumCapital + $sumInterest) / $credit->number_installments) / 10, 1) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo round($credit->interest_rate, 2) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo number_format($saldoCapital / 10, 2) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo date('d/m/Y', strtotime($credit->installments[0]->expiration_at)) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo date('d/m/Y', strtotime($credit->installments[count($credit->installments) - 1]->expiration_at)) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $promedioDiasAtrado ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $credit->apU ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo $cuotasPagadas . '/' . $credit->number_installments ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $credit->credit_type ?></td>
                </tr>
                <tr>
                    <td colspan="11" style="border-bottom: 1px solid black;">
                        <?php echo implode(' | ', $arrayRetrasos) ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4 style="text-align: center;">CREDITOS CANCELADOS</h4>
    <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">CUOTA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">MONTO DESEMB</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">MONTO CUOTA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">TEM</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">SALDO CAPITAL</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">FECHA VIGENCIA</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">FECHA CANCELACION</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">ATRASO PROMEDIO</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">ASESOR</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">CUOTAS PAGADAS</th>
                <th style="text-align: center; padding: 5px 0; margin: 0; border-top: 2px solid black; border-bottom: 2px solid black;">TIPO PRODUCTO</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($creditsCancelados as $key => $credit) { ?>
                <?php
                $sumCapital = 0;
                $sumInterest = 0;
                $totalDiasAtraso = 0;
                $arrayRetrasos = [];
                foreach ($credit->installments as $installment) {
                    $sumCapital += $installment->capital;
                    $sumInterest += $installment->interest;
                    $days = 0;

                    if ($installment->expiration_at < $installment->payment_date) {
                        $ini = new DateTime($installment->expiration_at);
                        $fin = new DateTime($installment->payment_date);
                        $days = $ini->diff($fin)->days;
                        $totalDiasAtraso += $days;
                    }

                    $arrayRetrasos[] = $installment->number . '/' . $days;
                }
                $promedioDiasAtrado = round($totalDiasAtraso / $credit->number_installments, 2);
                ?>
                <tr>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo $credit->idP ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo round((($sumCapital + $sumInterest) / $credit->number_installments) / 10, 1) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo round($credit->interest_rate, 2) ?>%</td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo '0' ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo date('d/m/Y', strtotime($credit->installments[0]->expiration_at)) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo date('d/m/Y', strtotime($credit->installments[count($credit->installments) - 1]->expiration_at)) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $promedioDiasAtrado ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $credit->apU ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px; text-align: center;"><?php echo $credit->number_installments . '/' . $credit->number_installments ?></td>
                    <td style="text-align: center; border-bottom: 1px solid black; padding-top: 5px;"><?php echo $credit->credit_type ?></td>
                </tr>
                <tr>
                    <td colspan="11" style="border-bottom: 1px solid black;">
                        <?php echo implode(' | ', $arrayRetrasos) ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>