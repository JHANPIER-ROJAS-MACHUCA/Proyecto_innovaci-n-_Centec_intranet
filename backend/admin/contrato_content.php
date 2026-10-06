<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Relation;
use CrediSoporte\Domain\Request\Request;

require_once "../vendor/autoload.php";
require_once "../src/Domain/Database/bootstrap.php";

$request = new Request;

$creditId = $request->creditId;

$business = $database->table('tdatos')->first();

$credit = Credit::with('relations')->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->select('tprestamo.*', 'tclie_general.dni', 'tclie_general.direc', 'tclie_general.cel')
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as client')
    ->find($creditId);

$relation = null;
if ($credit->idV) {
    $relation = $database->table('tvinculacion')
        ->leftJoin('tclie_general as aval', 'tvinculacion.aval', 'aval.idCG')
        ->leftJoin('tclie_general as conyuge', 'tvinculacion.conyugue', 'conyuge.idCG')
        ->where('idV', $credit->idV)
        ->select(
            'conyuge.ap as conyuge_ap',
            'conyuge.am as conyuge_am',
            'conyuge.nom as conyuge_nom',
            'conyuge.dni as conyuge_dni',
            'conyuge.direc as conyuge_direc',
            'conyuge.cel as conyuge_cel',
            'aval.ap as aval_ap',
            'aval.am as aval_am',
            'aval.nom as aval_nom',
            'aval.dni as aval_dni',
            'aval.direc as aval_direc',
            'aval.cel as aval_cel'
        )
        ->first();
}

function fechaEs($fecha)
{
    $fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    // $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    // $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    // $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    // $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $numeroDia . ' días del mes de ' . $nombreMes . ' de ' . $anio;
}

function getFormaDePago($type)
{
    $tpago = "";
    switch ($type) {
        case 1:
            $tpago = "diario";
            break;
        case 2:
            $tpago = "semanal";
            break;
        case 3:
            $tpago = "pago único";
            break;
        case 4:
            $tpago = "mensual";
            break;
        case 5:
            $tpago = "quincenal";
            break;
    }
    return $tpago;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato</title>

    <style>
        html,
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 13px;
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
    <h4 class="text-center" style="margin-bottom: 5px;"><?php echo $business->nombreEmpresa ?></h4>
    <h3 class="text-center" style="margin-top: 0;">CONTRATO PRIVADO DE PRÉSTAMO</h3>
    <div class="text-center">NUMERO DE CONTRATO <?php echo str_pad($creditId, 5, '0', STR_PAD_LEFT) ?></div>
    <p class="text-justify">
        Conste por el presente documento el contrato de préstamo que celebran de una parte,
        el <b><?php echo $business->nombreEmpresa ?> <?php echo $business->siglas ?></b>, con RUC <b><?php echo $business->ruc ?></b>, con domicilio,
        en <b><?php echo $business->direccion ?> – SATIPO, JUNÍN,</b> debidamente representada por su Gerente General,
        <b><?php echo $business->representante ?></b>, identificado con D.N.I. <b><?php echo $business->dnir ?></b>,
        con poderes <b>Inscritos en la Partida Nº <?php echo $business->partida ?>,
            asiento A001</b>, de los Registros Públicos de Satipo – Junín,
        a quien en adelante se denominara <b><?php echo $business->nombreEmpresa ?></b>,
        y de la otra parte el <b>Sr./Sra <?php echo $credit->client ?></b>, identificado/a con <b>D.N.I. <?php echo $credit->dni ?></b>,
        con domicilio en <?php echo $credit->direc ?>, a quien en adelante se denominará <b>PRESTATARIO</b>,
        en los términos contenidos en las cláusulas siguientes.
    </p>

    <h5>ANTECEDENTES</h5>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top;">Primero.</td>
                <td class="text-justify"><b><?php echo $business->nombreEmpresa ?></b>, es una Empresa Privada, que apoya el desarrollo de las Pymes a través de Asesoría de sus proyectos y del financiamiento para sus negocios.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Segundo.</td>
                <td class="text-justify">EL <b>PRESTATARIO</b> es una persona natural, comerciante empresaria de esta ciudad.</td>
            </tr>
        </tbody>
    </table>

    <h5>OBJETIVO DEL CONTRATO</h5>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top;">Tercero.</td>
                <td>El presente convenio tiene como objetivo establecer un vínculo de préstamo entre el <b><?php echo $business->nombreEmpresa ?></b> y el PRESTATARIO a fin de apoyar su labor empresarial.</td>
            </tr>
        </tbody>
    </table>

    <h5>OBLIGACIONES DE LAS PARTES</h5>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top;">Cuarto.</td>
                <td class="text-justify">Por el presente contrato el <b>PRESTATARIO</b>, se compromete en devolver el capital y pagar como interés compensatorio, el equivalente a la tasa promedio del sistema financiero para créditos a la microempresa, vigente a la fecha del presente contrato.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Quinto.</td>
                <td class="text-justify">La tasa de interés moratoria será de aplicación en caso de mora desde el primer día de incumplimiento, en forma adicional al interés compensatorio que seguirá devengándose a la tasa pactada. Todo interés moratorio será exigible sin necesidad que sea constituido en mora o requerido al pago, de conformidad con lo dispuesto en el artículo 1333° del código civil</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Sexto.</td>
                <td class="text-justify">El cliente podrá efectuar en horario de atención al público el pago de sus cuotas en la oficina. Para mayor información sobre la institución financiera, llamar al Call Center 064-402207</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Séptimo.</td>
                <td class="text-justify">En caso se pacte un periodo de gracia para el pago de préstamo, los intereses generados en dicho periodo serán capitalizados e incorporados en el respectivo cronograma de pagos. A elección del cliente, estos intereses, se podrá distribuir en una o más cuotas.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Octavo.</td>
                <td class="text-justify">El cliente podrá realizar pagos anticipados o adelantados de cuotas en forma total o parcial, sin aplicación de comisiones, gastos, penalidades de ningún tipo. El pago anticipado es aquel pago mayor al valor de cinco cuotas y se aplica al crédito. El adelanto de cuotas es un pago menor o igual al equivalente de cinco cuotas y se aplica el monto pagado a las cuotas inmediatas siguientes no vencidas, sin que produzca una reducción de los intereses, las comisiones y los gastos.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Noveno.</td>
                <td class="text-justify">El cliente, aval, garante y/o fiador solidario, según sea el caso declaran haber emitido y suscrito a favor del <b><?php echo $business->nombreEmpresa ?></b> una Letra de cambio incompleto: que en caso de incumplimiento de sus obligaciones podrá ser completado por el <b><?php echo $business->nombreEmpresa ?></b> para su ejecución y cobro, indistintamente a cualquiera de ellos, declarando haber sido informados de los mecanismos de protección que la Ley permite para la emisión o aceptación de títulos valores incompletos.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Décimo.</td>
                <td class="text-justify">Las garantías reales otorgadas por el cliente, aval, garante y/o fiador solidario, según sea el caso, a favor del <b><?php echo $business->nombreEmpresa ?></b>, respaldan las obligaciones generadas en virtud del crédito, el bien dado en garantía podrá ser ejecutado en caso de incumplimiento de las obligaciones de los deudores, cargándose el saldo adeudado, más los intereses compensatorios, moratorios, pactados por pagos tardíos respectivos, comisiones, gastos derivados de la cobranza extrajudicial y judicial (costas y costos del proceso) y otros que hubiere tenido que asumir.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Undécimo.</td>
                <td class="text-justify">El cliente, aval, garante y fiador solidario, según sea el caso, declara expresamente que el <b><?php echo $business->nombreEmpresa ?></b> ha hecho de su conocimiento los mecanismos de protección que la Ley permite para la emisión o aceptación de títulos valores incompletos, habiendo sido plenamente informado de los alcances de las normas vigentes.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Duodécimo.</td>
                <td class="text-justify">El <b><?php echo $business->nombreEmpresa ?></b> podrá realizar acciones de cobranza y/o remitir comunicaciones a cualquiera de los domicilios que el cliente, aval, garante y/ o fiador solidario, según sea el caso, señalen en este u otros documentos, como también mensajes de texto, vía email y otros similares. La variación domiciliaria se hará efectiva previa comunicación por carta notarial y verificación domiciliaria efectuada por el <b><?php echo $business->nombreEmpresa ?></b>, de acuerdo a lo señalado en el contrato y el artículo 40 del Código Civil</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimotercero.</td>
                <td class="text-justify">Adicionalmente el PRESTATARIO pagará los gastos administrativos, el costo de evaluación de crédito, la búsqueda en Central de Riesgo y la gestión de cobranza.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimocuarto.</td>
                <td class="text-justify">Dicho crédito será amortizado en forma <b><?php echo mb_strtoupper($credit->paymentPeriodToString()) ?></b>, pagándose primero el importe de los intereses y gastos administrativos y en las ultimas cuotas el importe del capital, por lo que el <b><?php echo $business->nombreEmpresa ?></b> en ese momento se emitirá la boleta de venta y/o factura, con la cancelación del Total de Crédito.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimoquinto.</td>
                <td class="text-justify">En todo lo no previsto por las partes en el presente convenio, ambas partes se someten a lo establecido por las normas del Código Civil y demás del sistema jurídico que resulte aplicables.</td>
            </tr>
        </tbody>
    </table>

    <p>En señal de conformidad las partes suscriben este documento en la ciudad de Satipo, a los <?php echo fechaEs($credit->fechaDesembolso) ?>.</p>

    <table style="width: 100%;margin-top: 150px;font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 50%;vertical-align: top;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <div style="padding-right: 20px;">
                                    <hr style="margin-top: 0;">
                                    <h4 class="text-center" style="max-width: 300px;margin: 25px auto;"><?php echo $business->nombreEmpresa ?></h4>
                                </div>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 50%;vertical-align: top;">
                    <div style="padding-left: 20px;">
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div style="padding-right: 20px;">
                                            <hr style="margin-top: 0;">
                                            <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;">PRESTATARIO</h4>
                                            <div>DNI: <?php echo $credit->dni ?></div>
                                            <div><?php echo $credit->client ?></div>
                                            <div>DOMICILIO: <?php echo $credit->direc ?></div>
                                            <div>CELULAR: <?php echo $credit->cel ?></div>
                                        </div>
                                    </td>
                                    <td style="width: 0;vertical-align: top;">
                                        <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <?php if ($relation && $relation->aval_dni && $relation->conyuge_dni) { ?>
                <tr>
                    <td style="width: 50%;">
                        <div style="padding-left: 20px;padding-top: 100px;">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <div style="padding-right: 20px;">
                                                <hr style="margin-top: 0;">
                                                <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;">AVAL</h4>
                                                <div>DNI: <?php echo $relation->aval_dni ?></div>
                                                <div><?php echo $relation->aval_ap . ' ' . $relation->aval_am . ' ' . $relation->aval_nom ?></div>
                                                <div>DOMICILIO: <?php echo $relation->aval_direc ?></div>
                                                <div>CELULAR: <?php echo $relation->aval_cel ?></div>
                                            </div>
                                        </td>
                                        <td style="width: 0;vertical-align: top;">
                                            <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td style="width: 50%;">
                        <div style="padding-left: 20px;padding-top: 100px;">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <div style="padding-right: 20px;">
                                                <hr style="margin-top: 0;">
                                                <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;">CÓNYUGE</h4>
                                                <div>DNI: <?php echo $relaion->conyuge_dni ?></div>
                                                <div><?php echo $relation->conyuge_ap . ' ' . $relation->conyuge_am . ' ' . $relation->conyuge_nom ?></div>
                                                <div>DOMICILIO: <?php echo $relaion->conyuge_direc ?></div>
                                                <div>CELULAR: <?php echo $relaion->conyuge_cel ?></div>
                                            </div>
                                        </td>
                                        <td style="width: 0;vertical-align: top;">
                                            <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php } else if ($relation && $relation->aval_dni) { ?>
                <tr>
                    <td colspan="2">
                        <div style="padding-left: 20px;padding-top: 100px; width: 330px; margin: 0 auto;">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <div style="padding-right: 20px;">
                                                <hr style="margin-top: 0;">
                                                <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;">AVAL</h4>
                                                <div>DNI: <?php echo $relation->aval_dni ?></div>
                                                <div><?php echo $relation->aval_ap . ' ' . $relation->aval_am . ' ' . $relation->aval_nom ?></div>
                                                <div>DOMICILIO: <?php echo $relation->aval_direc ?></div>
                                                <div>CELULAR: <?php echo $relation->aval_cel ?></div>
                                            </div>
                                        </td>
                                        <td style="width: 0;vertical-align: top;">
                                            <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php } else if ($relation && $relation->conyuge_dni) { ?>
                <tr>
                    <td colspan="2">
                        <div style="padding-left: 20px;padding-top: 100px;width: 330px; margin: 0 auto;">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <div style="padding-right: 20px;">
                                                <hr style="margin-top: 0;">
                                                <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;">CÓNYUGE</h4>
                                                <div>DNI: <?php echo $relation->conyuge_dni ?></div>
                                                <div><?php echo $relation->conyuge_ap . ' ' . $relation->conyuge_am . ' ' . $relation->conyuge_nom ?></div>
                                                <div>DOMICILIO: <?php echo $relation->conyuge_direc ?></div>
                                                <div>CELULAR: <?php echo $relation->conyuge_cel ?></div>
                                            </div>
                                        </td>
                                        <td style="width: 0;vertical-align: top;">
                                            <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if (count($credit->relations) > 0) { ?>
        <table style="width: 100%;margin-top: 40px;font-size: 12px;">
            <tbody>
                <?php foreach ($credit->relations->chunk(2) as $key => $items) { ?>
                    <tr>
                        <?php if (count($items) > 1) { ?>
                            <?php foreach ($items as $key => $item) { ?>
                                <td style="width: 50%;">
                                    <div style="padding-left: 20px;padding-top: 100px;">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <div style="padding-right: 20px;">
                                                            <hr style="margin-top: 0;">
                                                            <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;"><?php echo strtoupper(Relation::typeToString($item->pivot->type)) ?></h4>
                                                            <div>DNI: <?php echo $item->dni ?></div>
                                                            <div><?php echo "$item->ap $item->am $item->nom" ?></div>
                                                            <div>DOMICILIO: <?php echo $item->direc ?></div>
                                                            <div>CELULAR: <?php echo $item->cel ?></div>
                                                        </div>
                                                    </td>
                                                    <td style="width: 0;vertical-align: top;">
                                                        <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            <?php } ?>
                        <?php } else { ?>
                            <td colspan="2">
                                <div style="padding-left: 20px;padding-top: 100px;width: 330px; margin: 0 auto;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <div style="padding-right: 20px;">
                                                        <hr style="margin-top: 0;">
                                                        <h4 style="font-weight: bold;text-align: center;margin-bottom: 5px;"><?php echo strtoupper(Relation::typeToString($items[0]->pivot->type)) ?></h4>
                                                        <div>DNI: <?php echo $items[0]->dni ?></div>
                                                        <div><?php echo $items[0]->ap . " " . $items[0]->am . " " . $items[0]->nom ?></div>
                                                        <div>DOMICILIO: <?php echo $items[0]->direc ?></div>
                                                        <div>CELULAR: <?php echo $items[0]->cel ?></div>
                                                    </div>
                                                </td>
                                                <td style="width: 0;vertical-align: top;">
                                                    <div style="border: 1px solid black;width: 80px; height: 100px;"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

</body>

</html>