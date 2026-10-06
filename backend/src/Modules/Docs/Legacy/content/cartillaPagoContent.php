<?php


use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;

$currentDate = date('Y-m-d');

$credit = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->join('dictionaries', 'tprestamo.modality_id', 'dictionaries.id')
    ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->select(
        'tprestamo.*',
        'tclie_general.*',
        'tusuario.apU', 'tusuario.amU', 'tusuario.nomU', 'tusuario.celU',
        'credit_types.name as credit_type',
        'dictionaries.description as modality'
    )
    ->find($_GET['creditId']);

$installments = Installment::where('idP', $_GET['creditId'])->get();

$importeTotal = $installments->sum(function ($item) {
    return $item->capital + $item->interest;
});

$business = $database::table('tdatos')->first();
$office = $database::table('toficina')->first();

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
    <title>Cartilla de pago</title>
    <style>
        html,
        body {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 9px;
        }

        .td_cartilla:first {
            padding-right: 20px;
        }

        .td_cartilla:last-child {
            padding-left: 20px;
        }

        @page {
            margin: 5px 10px;
        }
    </style>
</head>

<body>
    <table class="table" style="width: 100%; table-layout: fixed;">
        <tbody>
            <tr>
                <?php for ($i = 0; $i < 2; $i++) { ?>
                    <td class="td_cartilla" style="width: 50%;">
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 30%;">
                                        <img src="<?php echo $base64 ?>" style="height: 35px;" />
                                    </td>
                                    <td style="text-align: center; width: 40%; font-size: 8px;">
                                        <div style="font-size: 12px; font-weight: bold;"><?php echo $business->abre . ' ' . $business->siglas ?></div>
                                        <div><?php echo $office->direccion ?></div>
                                        <div><?php echo $office->telefono ? 'Celular: ' . $office->telefono : '' ?></div>
                                        <div><?php echo $office->correo ? 'Correo: ' . $office->correo : '' ?></div>
                                    </td>
                                    <td style="width: 30%;">
                                        <table style="width: 100%; padding: 0; margin: 0;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 0; margin: 0; height: 5px; white-space: nowrap; text-align: right;">Cod. crédito:</td>
                                                    <td style="padding: 0; margin: 0; height: 5px; text-align: right;"><?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="white-space: nowrap; text-align: right;">N° credito:</td>
                                                    <td style="text-align: right;"><?php echo str_pad($credit->n_credito, 2, '0', STR_PAD_LEFT) ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="white-space: nowrap; text-align: right;">N° cuotas:</td>
                                                    <td style="text-align: right;"><?php echo $credit->number_installments; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">Días:</td>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        $ini = new DateTime($credit->installments[0]->expiration_at);
                                                        $fin = new DateTime($credit->installments[count($credit->installments) - 1]->expiration_at);
                                                        echo $ini->diff($fin)->days + 1;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">Modalidad:</td>
                                                    <td style="text-align: right;"><?php echo $credit->modality ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>Cliente:</td>
                                    <td>
                                        <span style="font-weight: bold;">
                                            <?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?>
                                        </span>
                                        <span>Celular: </span>
                                        <span style="font-weight: bold;"><?php echo $credit->cel ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dirección:</td>
                                    <td style="font-weight: bold;">
                                        <?php echo $credit->telefono ?>
                                        <?php echo $credit->referencia ? ' (' . $credit->referencia . ')' : '' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Negocio:</td>
                                    <td style="font-weight: bold;"><?php echo $credit->comentario ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Fecha</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Prestamo</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Total</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">TIpo</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Producto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="text-align: center;">
                                    <td style="border: 1px solid #90A4AE; padding: 3px 5px;"><?php echo date('d/mY', strtotime($credit->fechaDesembolso)) ?></td>
                                    <td style="border: 1px solid #90A4AE; padding: 3px 5px;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                                    <td style="border: 1px solid #90A4AE; padding: 3px 5px;"><?php echo number_format($importeTotal / 10, 2) ?></td>
                                    <td style="border: 1px solid #90A4AE; padding: 3px 5px;"><?php echo $credit->paymentTypeToString() ?></td>
                                    <td style="border: 1px solid #90A4AE; padding: 3px 5px;"><?php echo $credit->credit_type ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%" style="border: 1px solid black;border-collapse: collapse; margin-top: 5px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px; width: 0;">Nº</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Fecha Prog.</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Capital</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Interes</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Cuota</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Monto Pag.</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px; width: 0;">Pago Mora</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Fecha pago</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px;">Saldo</th>
                                    <th style="border: 1px solid #90A4AE; background: #263238; color: white; padding: 4px 5px; width: 25px;">Firma</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sum = 0 ?>
                                <?php foreach ($installments as $installment) { ?>

                                    <?php
                                    $sum += $installment->capital + $installment->interest;
                                    ?>
                                    <tr style="text-align: center;">
                                        <td style="border: 1px solid #90A4AE; height: 10px;"><?php echo $installment->number ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px; font-weight: bold;"><?php echo date('d/m/y', strtotime($installment->expiration_at)) ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"><?php echo number_format($installment->capital / 10, 2) ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"><?php echo number_format($installment->interest / 10, 2) ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"><?php echo number_format(($installment->capital + $installment->interest) / 10, 2) ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"><?php echo number_format(($importeTotal - $sum) / 10, 2) ?></td>
                                        <td style="border: 1px solid #90A4AE; height: 10px;"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div style="text-align: center; margin-top: 10px;">
                            "EL PENSAMIENTO POSITIVO TE PERMITIRÁ HACERLO TODO MUCHO MEJOR QUE EL PENSAMIENTO NEGATIVO"
                        </div>

                        <table style="width: 100%; table-layout: fixed;">
                            <tbody>
                                <tr>
                                    <td>
                                        <div style="text-align: center;">Asesor <span style="font-weight: bold;"><?php echo $credit->apU . ' ' . $credit->amU . ' ' . $credit->nomU ?></span></div>
                                        <div style="text-align: center;">Celular <span style="font-weight: bold;"><?php echo $credit->celU ?></span></div>
                                    </td>
                                    <td>
                                        <div style="text-align: center; font-size: 1.3rem;">Page con YAPE</div>
                                        <div style="text-align: center; font-size: 1.3rem;"><b>936447202</b></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div style="text-align: center; margin-top: 5px;">
                            <b>NOTA:</b> Por cada día de retraso en su crédito, abonara <b>S/. <?php echo number_format($credit->penalty / 10, 2) ?></b> por intereses moratorio.
                        </div>
                        <div style="text-align: center;">
                            Para consulta y quejas comunicarse con el número: 936447202
                        </div>
                    </td>
                <?php } ?>
            </tr>
        </tbody>
    </table>

</body>

</html>