<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;
use Luecano\NumeroALetras\NumeroALetras;


$request = new Request();
$formatter = new NumeroALetras();

$credit = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tvinculacion', 'tprestamo.idV', 'tvinculacion.idV')
    ->where('tprestamo.idP', $request->creditId)
    ->groupBy('tprestamo.idP')
    ->select('tprestamo.*', 'tprestamo.idCG', 'tvinculacion.conyugue as conyuge', 'tvinculacion.aval')
    ->selectRaw('sum(tpresta_detalle.cuota + tpresta_detalle.interest) as total')
    ->selectRaw('min(tpresta_detalle.fechaProg) as start_at')
    ->selectRaw('max(tpresta_detalle.fechaProg) as end_at')
    ->first();

if (!$credit) {
    http_response_code(404);
    die();
}

$clientsIds = [$credit->idCG];

if ($credit->conyuge) {
    $clientsIds[] = $credit->conyuge;
}

if ($credit->aval) {
    $clientsIds[] = $credit->aval;
}

// echo json_encode($clientsIds);die();

$clients = Customer::leftJoin('ubigeo_districts', 'tclie_general.ubigeo_id', 'ubigeo_districts.id')
    ->leftJoin('ubigeo_provinces', 'ubigeo_districts.province_id', 'ubigeo_provinces.id')
    ->leftJoin('ubigeo_departments', 'ubigeo_provinces.department_id', 'ubigeo_departments.id')
    ->whereIn('tclie_general.idCG', $clientsIds)
    ->select(
        'tclie_general.idCG',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tclie_general.dni',
        'tclie_general.cel',
        'tclie_general.direc',
        'ubigeo_districts.name as district',
        'ubigeo_provinces.name as province',
        'ubigeo_departments.name as department',
    )
    ->get();

$customer = $clients->where('idCG', $credit->idCG)->first();
$conyuge = $clients->where('idCG', $credit->conyuge)->first();
$aval = $clients->where('idCG', $credit->aval)->first();

$business = $database->table('tdatos')->first();

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagare</title>
    <style>
        html,
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.5rem;
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

        .text-justify {
            text-align: justify;
        }
    </style>
</head>

<body>
    <h4 class="text-center" style="margin-bottom: 0;"><?php echo $business->nombreEmpresa ?></h4>
    <div class="text-center" style="margin-top: 2px;"><?php echo $business->direccion ?></div>
    <div class="text-center" style="margin-top: 2px;">PAGARE N° <?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></div>

    <table style="border-collapse: collapse; width: 100%; table-layout: fixed; margin-top: 20px;">
        <thead>
            <tr>
                <th style="background: #CFD8DC;">IMPORTE ORIGINAL</th>
                <th style="background: #CFD8DC;">IMPORTE DEUDOR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">S/. <?php echo number_format($credit->montoAprovado, 2) ?></td>
                <td style="text-align: center;">S/. <?php echo number_format($credit->total, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <table style="border-collapse: collapse; width: 100%; table-layout: fixed; margin-top: 10px;">
        <thead>
            <tr>
                <th style="background: #CFD8DC;">FECHA DE INICIO DE LA DEUDA</th>
                <th style="background: #CFD8DC;">FECHA DE VENCIMIENTO DE LA DEUDA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;"><?php echo $credit->start_at ?></td>
                <td style="text-align: center;"><?php echo $credit->end_at ?></td>
            </tr>
        </tbody>
    </table>

    <p class="text-justify">
        YO (NOSOTROS), <b><?php echo "$customer->ap $customer->am $customer->nom" ?></b>, IDENTIFICADO CON D.N.I. Nº <b><?php echo $customer->dni ?></b>,
        CON DOMICILIO REAL UBICADO EN, <?php echo $customer->direc ?>,
        <?php if ($customer->province === $customer->district) { ?>
            DEL DISTRITO Y PROVINCIA DE <?php echo $customer->province ?>, DEPARTAMENTO DE <?php echo $customer->department ?>
        <?php } else { ?>
            DEL DISTRITO DE <?php echo $customer->district ?>,
            PROVINCIA DE <?php echo $customer->district ?> Y
            DEPARTAMENTO DE <?php echo $customer->district ?>,
        <?php } ?>
        RECONOZCO (RECONOCEMOS) QUE ADEUDO (ADEUDAMOS) Y PAGARÉ
        (PAGAREMOS) INCONDICIONALMENTE EN LA FECHA DE VENCIMIENTO CONSIGNADO EN EL
        PRESENTE PAGARÉ, A LA ORDEN DE <?php echo "$business->nombreEmpresa $business->siglas" ?>,
        EN ADELANTE <?php echo $business->abre ?>, O A QUIEN ÉSTA SE LO HUBIERA CEDIDO, EN SU
        DOMICILIO SOCIAL O DONDE SE PRESENTARE PARA SU COBRO; EL IMPORTE DE
        <b><?php echo $formatter->toInvoice($credit->montoAprovado, 2, "soles") ?> (S/. <?php echo number_format($credit->montoAprovado, 2) ?>)</b>,
        SIN LUGAR A RECLAMO DE CLASE ALGUNA, PARA CUYO FIEL Y
        EXACTO CUMPLIMIENTO, ME OBLIGO CON TODOS MIS BIENES PRESENTE Y FUTUROS EN LA
        MEJOR FORMA DE DERECHO. AL EFECTO, ASUMO LA OBLIGACIÓN EN LAS SIGUIENTES
        CONDICIONES:
    </p>

    <h3><u>CONDICIONES GENERALES DEL TITULO</u></h3>

    <p class="text-justify">
        <b>PRIMERA:</b> ESTE PAGARÉ SERÁ PAGADO SÓLO EN LA MISMA MONEDA QUE EXPRESA ESTE TÍTULO VALOR.
    </p>

    <p class="text-justify">
        <b>SEGUNDA:</b> A SU VENCIMIENTO, PODRÁ SER PRORROGADO POR <b><?php echo $business->abre ?></b>,
        O POR SU TENEDOR, POR EL PLAZO QUE ÉSTE SEÑALE EN
        ESTE MISMO DOCUMENTO, SIN QUE SEA NECESARIO INTERVENCIÓN ALGUNA DEL OBLIGADO
        PRINCIPAL NI DE LOS AVALISTAS SOLIDARIOS.
    </p>

    <p class="text-justify">
        <b>TERCERA:</b> EL IMPORTE DE ESTE PAGARÉ, Y /O DE LAS CUOTAS DEL CRÉDITO QUE
        REPRESENTA, GENERARÁN DESDE LA FECHA DE EMISIÓN HASTA LA FECHA DE SU
        RESPECTIVO(S) VENCIMIENTO(S), UN INTERÉS COMPENSATORIO QUE SE PACTA EN LA TASA
        DE <?php echo $credit->taza ?>% MENSUAL.
    </p>

    <p class="text-justify">
        <b>CUARTA:</b> EN CASO DE INCUMPLIMIENTO EN EL PAGO DE UNA O MÁS CUOTAS PACTADAS, AL
        IMPORTE DEUDOR SE LE APLICARÁN LOS INTERESES COMPENSATORIOS E INTERESES
        MORATORIOS A LAS TASAS MÁXIMAS APROBADAS POR <b><?php echo $business->abre ?></b>
        DESDE LA FECHA DE VENCIMIENTO HASTA SU TOTAL
        CANCELACIÓN, SIN QUE SEA NECESARIO EFECTUAR REQUERIMIENTO PREVIO DE PAGO
        PARA CONSTITUIR EN MORA AL OBLIGADO PRINCIPAL NI A LOS AVALISTAS SOLIDARIOS,
        INCURRIÉNDOSE EN ÉSTA AUTOMÁTICAMENTE POR EL SOLO HECHO DEL VENCIMIENTO.
    </p>

    <p class="text-justify">
        <b>QUINTA:</b> EL CLIENTE Y SU CÓNYUGE OBLIGADOS PRINCIPALES Y LOS DEUDORES SOLIDARIOS
        ACEPTAN IGUALMENTE QUE LAS TASAS DE INTERÉS COMPENSATORIO Y/O MORATORIO
        PUEDAN SER VARIADAS POR <b><?php echo $business->abre ?></b> O SU
        TENEDOR SIN NECESIDAD DE AVISO PREVIO, DE ACUERDO A LAS TASAS QUE ÉSTA TENGA
        VIGENTES.
    </p>

    <p class="text-justify">
        <b>SEXTA:</b> LOS OBLIGADOS PRINCIPALES Y SOLIDARIOS SUSCRIBIENTES DEL PRESENTE
        PAGARÉ DEJAN CONSTANCIA QUE ESTE DOCUMENTO NO REQUIERE EL PROTESTO POR
        FALTA DE PAGO, PROCEDIENDO SU EJECUCIÓN POR EL SÓLO MÉRITO DE HABER VENCIDO
        SU PLAZO Y NO HABER SIDO PRORROGADO; SALVO EL PROTESTO DE LA CUOTA IMPAGA SI
        SE OPTA POR LA EL VENCIMIENTO ACELERADO DISPUESTO POR EL ART. 1323 DEL CÓDIGO
        CIVIL, Y/O LA PRECLUSIÓN DE LOS PLAZOS.
    </p>

    <p class="text-justify">
        <b>SÉPTIMA:</b> EL IMPORTE DE ESTE PAGARÉ PODRÁ SER PACTADO EN UNA O MAS CUOTAS,
        SEGÚN EL/LOS IMPORTE(S) Y VENCIMIENTO QUE INDIQUE EL CORRESPONDIENTE
        CRONOGRAMA DE PAGOS, QUE NO REQUERIRÁ DE SUSCRIPCIÓN ADICIONAL AL PRESENTE
        DOCUMENTO.
    </p>

    <p class="text-justify">
        <b>OCTAVA:</b> SERÁN DE CARGO DE LOS OBLIGADOS PRINCIPALES Y LOS SOLIDARIOS, EL PAGO
        ÍNTEGRO DE LOS TRIBUTOS Y GASTOS QUE AFECTEN A ÉSTE PAGARÉ O A LA OBLIGACIÓN EN
        ÉL CONTENIDA, LOS MISMOS QUE SERÁN CALCULADOS Y DETERMINADOS POR <b><?php echo $business->abre ?></b>
        O SU TENEDOR EN LA OPORTUNIDAD EN QUE
        ELLO SE VERIFIQUE.
    </p>

    <p class="text-justify">
        <b>NOVENA:</b> EL O LOS OBLIGADO (S) PRINCIPAL (ES) Y LOS AVALISTAS SOLIDARIOS AUTORIZAN
        EXPRESAMENTE A <b><?php echo $business->abre ?></b> A CARGAR
        DIRECTAMENTE EN SUS CUENTAS (SEA EN MONEDA NACIONAL Y/O EXTRANJERA) QUE
        MANTENGAN EN ELLA, EL O LAS CUOTAS DEL CRÉDITO QUE REPRESENTA EL PAGARÉ, ASÍ
        COMO A COMPENSARLOS CON CUALQUIER OTRO TIPO DE BIEN QUE PUDIERA TENER EN SU
        PODER, SIN QUE ELLO OBLIGUE O SIGNIFIQUE RESPONSABILIDAD PARA <b><?php echo $business->abre ?></b>.
    </p>

    <?php
    $meses = [
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre'
    ];
    $valuesDate = explode('-', $credit->fechaDesembolso);
    ?>
    <p style="text-align: right;">
        <b>Satipo, <?php echo $valuesDate[2] ?> de <?php echo $meses[$valuesDate[1] - 1] ?> de <?php echo $valuesDate[0] ?></b>
    </p>

    <div style="height: 120px;"></div>
    <table style="width: 100%; table-layout: fixed;">
        <tbody>
            <tr>
                <td>
                    <div style="border-top: 2px solid black; width: 125px; margin: auto;"></div>
                    <h4 class="text-center" style="margin-top: 10px;">TITULAR</h4>

                    <div>NOMBRES: <?php echo $customer->nom ?></div>
                    <div>APELLIDOS: <?php echo "$customer->ap $customer->am" ?></div>
                    <div>D.N.I.: <?php echo $customer->dni ?></div>
                    <div>DOMICILIO: <?php echo $customer->direc ?></div>
                </td>
                <td>
                    <div style="border-top: 2px solid black; width: 125px; margin: auto;"></div>
                    <h4 class="text-center" style="margin-top: 10px;">CONYUGE</h4>

                    <div>NOMBRES: <?php echo $conyuge ? $conyuge->nom : '' ?></div>
                    <div>APELLIDOS: <?php echo $conyuge ? "$conyuge->ap $conyuge->am" : '' ?></div>
                    <div>D.N.I.: <?php echo $conyuge ? $conyuge->dni : '' ?></div>
                    <div>DOMICILIO: <?php echo $conyuge ? $conyuge->direc : '' ?></div>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="page-break-after:always;"></div>

    <h2 class="text-center">Gerente General</h2>
    <h3 class="text-center"><u>AVALISTAS</u></h3>

    <p class="text-justify">
        NOSOTROS, LOS ABAJO FIRMANTES, NOS CONSTITUIMOS EN AVALISTAS PERMANENTES Y EN
        GARANTES SOLIDARIOS DE LOS OBLIGADOS PRINCIPALES Y ENTRE NOSOTROS MISMOS,
        PARA LO CUAL COMPROMETEMOS NUESTRO PATRIMONIO AL <b><?php echo $business->abre ?></b>
        EN GARANTÍA DEL PAGO DE LAS OBLIGACIONES CONTENIDAS EN
        EL PRESENTE PAGARÉ, OBLIGÁNDONOS POR LA CANTIDAD ADEUDADA Y ACEPTANDO SIN
        LIMITACIONES NI RESTRICCIONES TODAS Y CADA UNA DE LAS CLÁUSULAS ESPECIALES QUE
        FIGURAN EN EL PRESENTE PAGARÉ.
    </p>

    <p style="text-align: right;">
        <b>Satipo, <?php echo $valuesDate[2] ?> de <?php echo $meses[$valuesDate[1] - 1] ?> de <?php echo $valuesDate[0] ?></b>
    </p>

    <div style="height: 120px;"></div>
    <div>
        <div style="border: 1px solid black; width: 125px; margin: auto"></div>
        <div style="text-align: center; margin-top: 10px;"><b>AVALISTA</b></div>
    </div>

    <div>NOMBRES: <?php echo $aval ? $aval->nom : '' ?></div>
    <div>APELLIDOS: <?php echo $aval ? "$aval->ap $aval->am" : '' ?></div>
    <div>CLIENTE N°:</div>
    <div>D.N.I. o L.E.: <?php echo $aval ? $aval->dni : '' ?></div>
    <div>DOMICILIO: <?php echo $aval ? $aval->direc : '' ?></div>
</body>

</html>