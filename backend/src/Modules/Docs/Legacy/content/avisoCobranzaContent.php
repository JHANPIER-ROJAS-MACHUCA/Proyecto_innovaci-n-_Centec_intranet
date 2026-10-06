<?php


use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use Carbon\Carbon;

$request = new Request();

$credit = Credit::with(['installments', 'condoneDates', 'customer' => function ($query) {
    $query->leftJoin('ubigeo_districts', 'tclie_general.ubigeo_id', 'ubigeo_districts.id')
        ->select('tclie_general.*', 'ubigeo_districts.name as district');
}])->find($request->creditId);

if (!$credit) {
    http_response_code(404);
    die();
}

$delay = new Delay();
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->payment_period = $credit->payment_period;
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->penalty = $credit->penalty;

$diasAtraso = 0;

$sumCapitalDebt = 0;
$sumInterestDebt = 0;
$sumPenaltyDebt = 0;

$montoARegularizar = 0;
$capitalPending = 0;
$interestPending = 0;

foreach ($credit->installments as $key => $installment) {
    $capitalDebt = $installment->capitalDebt();
    $interestDebt = $installment->interestDebt();

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

    $sumCapitalDebt += $capitalDebt;
    $sumInterestDebt += $interestDebt;
    $sumPenaltyDebt += $penaltyDebt;

    if ($installment->expiration_at <= date('Y-m-d')) {
        $montoARegularizar += ($capitalDebt + $interestDebt + $penaltyDebt);
        $capitalPending += $capitalDebt;
        $interestPending += $interestDebt;
    }

    if (($capitalDebt > 0 || $interestDebt > 0 || $penaltyDebt > 0) && $diasAtraso === 0) {
        $inicio = new Carbon($installment->expiration_at);
        $fin = new Carbon();

        $diasAtraso = $inicio->diffInDays($fin);
    }
}

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
    <title>Aviso Cobransa</title>
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            line-height: 1.5;
            padding: 0 40px;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid #78909C;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <img src="<?php echo $base64 ?>" alt="" style="max-height: 50px;">

    <h2 style="text-align: center;">AVISO COBRANZA</h2>

    <p style="text-align: right;">FECHA: <?php echo date('d/m/Y') ?></p>

    <p style="text-align: justify;">
        Estimado Señor(a): <?php echo $credit->customer->ap . ' ' . $credit->customer->am . ' ' . $credit->customer->nom ?>. <br><br>

        Dirección: <?php echo $credit->customer->direc ?> <?php echo $credit->customer->district !== null ? ' - ' . $credit->customer->district : '' ?>.<br><br>

        Por la presente nos dirigimos a usted, con la finalidad de comunicarle que, el crédito que posee con nuestra Entidad
        <b><?php echo $business->titulo ?></b> cuyo saldo capital asciende a S/. <?php echo number_format($capitalPending / 10, 2) ?>,
        a la fecha mantiene <?php echo $diasAtraso ?> días retrasados, monto a regularizar
        S/. <?php echo number_format($montoARegularizar / 10, 2) ?> soles. <br><br>

        En tal sentido, le cursamos esta comunicación de cobranza, invitándole(a) para que una vez recibada, se acerque a nuestras
        oficinas con la finalidad de cancelar y/o regularizar la deuda a la brevedad.
        <b>
            <i>
                <u>
                    Caso contrario nuestra Entidad informara a las centrales de riesgo privadas INFOCORP Y EXPERIAN. El cual perjudicaría
                    su calificación y récord crediticio cuando solicite su crédito en los BANCOS y CAJAS.
                </u>
            </i>
        </b>

        <br><br>

        Atentamente,
    </p>

    <table class="table" style="margin: 0 auto; table-layout: fixed; width: 440px; margin-top: 150px;">
        <thead>
            <tr>
                <th colspan="2" style="padding: 10px 5px;">COMPROMISO GENERADO</th>
            </tr>
        </thead>
        <tbody>
            <tr style="width: 220px;">
                <td style="padding: 10px 5px;">FECHA DE AMORTIZACIÓN</td>
                <td style="padding: 10px 5px;"></td>
            </tr>
            <tr style="width: 220px;">
                <td style="padding: 10px 5px;">FIRMA</td>
                <td style="padding: 10px 5px;"></td>
            </tr>
        </tbody>
    </table>

    <p>* En caso de incumplimiento del compromiso me notifican con carta notarial el cual generaría gastos y persona asumirá.</p>
</body>

</html>