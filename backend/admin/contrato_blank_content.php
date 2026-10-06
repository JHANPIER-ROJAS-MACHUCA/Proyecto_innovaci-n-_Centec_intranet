<?php
include("./conection/bdcredito.php");

$smtp_business = extraer("SELECT * FROM tdatos LIMIT 1");
$business = mysqli_fetch_array($smtp_business);
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

        .font-bold {
            font-weight: 800;
        }
    </style>
</head>

<body>
<h4 class="text-center" style="margin-bottom: 5px;"><?php echo $business['nombreEmpresa'] ?></h4>
    <h3 class="text-center" style="margin-top: 0;">CONTRATO PRIVADO DE PRÉSTAMO</h3>
    <p class="text-justify">
        Conste por el presente documento el contrato de préstamo que celebran de una parte,
        el <b><?php echo $business['nombreEmpresa'] ?> <?php echo $business['siglas'] ?></b>, con RUC <b><?php echo $business['ruc'] ?></b>, con domicilio,
        en <b><?php echo $business['direccion'] ?> – SATIPO, JUNÍN,</b> debidamente representada por su Gerente General,
        <b><?php echo $business['representante'] ?></b>, identificado con D.N.I. <b><?php echo $business['dnir'] ?></b>,
        con poderes <b>Inscritos en la Partida Nº <?php echo $business['partida'] ?>,
        asiento A001</b>, de los Registros Públicos de Satipo – Junín,
        a quien en adelante se denominara <b><?php echo $business['nombreEmpresa'] ?></b>,
        y de la otra parte el <b>Sr./Sra _______________________</b>, identificado/a con <b>D.N.I. _____________</b>,
        con domicilio en _______________________________________, a quien en adelante se denominará <b>PRESTATARIO</b>,
        en los términos contenidos en las cláusulas siguientes.
    </p>

    <h5>ANTECEDENTES</h5>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top;">Primero.</td>
                <td class="text-justify"><b><?php echo $business['nombreEmpresa'] ?></b>, es una Empresa Privada, que apoya el desarrollo de las Pymes a través de Asesoría de sus proyectos y del financiamiento para sus negocios.</td>
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
                <td>El presente convenio tiene como objetivo establecer un vínculo de préstamo entre el <b><?php echo $business['nombreEmpresa'] ?></b> y el PRESTATARIO a fin de apoyar su labor empresarial.</td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top;">Tercero.</td>
                <td>El presente convenio tiene como objetivo establecer un vínculo de préstamo entre el <b><?php echo $business['nombreEmpresa'] ?></b> y el PRESTATARIO a fin de apoyar su labor empresarial.</td>
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
                <td class="text-justify">El cliente, aval, garante y/o fiador solidario, según sea el caso declaran haber emitido y suscrito a favor del <b><?php echo $business['nombreEmpresa'] ?></b> una Letra de cambio incompleto: que en caso de incumplimiento de sus obligaciones podrá ser completado por el <b><?php echo $business['nombreEmpresa'] ?></b> para su ejecución y cobro, indistintamente a cualquiera de ellos, declarando haber sido informados de los mecanismos de protección que la Ley permite para la emisión o aceptación de títulos valores incompletos.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Décimo.</td>
                <td class="text-justify">Las garantías reales otorgadas por el cliente, aval, garante y/o fiador solidario, según sea el caso, a favor del <b><?php echo $business['nombreEmpresa'] ?></b>, respaldan las obligaciones generadas en virtud del crédito, el bien dado en garantía podrá ser ejecutado en caso de incumplimiento de las obligaciones de los deudores, cargándose el saldo adeudado, más los intereses compensatorios, moratorios, pactados por pagos tardíos respectivos, comisiones, gastos derivados de la cobranza extrajudicial y judicial (costas y costos del proceso) y otros que hubiere tenido que asumir.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Undécimo.</td>
                <td class="text-justify">El cliente, aval, garante y fiador solidario, según sea el caso, declara expresamente que el <b><?php echo $business['nombreEmpresa'] ?></b> ha hecho de su conocimiento los mecanismos de protección que la Ley permite para la emisión o aceptación de títulos valores incompletos, habiendo sido plenamente informado de los alcances de las normas vigentes.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Duodécimo.</td>
                <td class="text-justify">El <b><?php echo $business['nombreEmpresa'] ?></b> podrá realizar acciones de cobranza y/o remitir comunicaciones a cualquiera de los domicilios que el cliente, aval, garante y/ o fiador solidario, según sea el caso, señalen en este u otros documentos, como también mensajes de texto, vía email y otros similares. La variación domiciliaria se hará efectiva previa comunicación por carta notarial y verificación domiciliaria efectuada por el <b><?php echo $business['nombreEmpresa'] ?></b>, de acuerdo a lo señalado en el contrato y el artículo 40 del Código Civil</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimotercero.</td>
                <td class="text-justify">Adicionalmente el PRESTATARIO pagará los gastos administrativos, el costo de evaluación de crédito, la búsqueda en Central de Riesgo y la gestión de cobranza.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimocuarto.</td>
                <td class="text-justify">Dicho crédito será amortizado en forma <b>______________</b>, pagándose primero el importe de los intereses y gastos administrativos y en las ultimas cuotas el importe del capital, por lo que el <b><?php echo $business['nombreEmpresa'] ?></b> en ese momento se emitirá la boleta de venta y/o factura, con la cancelación del Total de Crédito.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Decimoquinto.</td>
                <td class="text-justify">En todo lo no previsto por las partes en el presente convenio, ambas partes se someten a lo establecido por las normas del Código Civil y demás del sistema jurídico que resulte aplicables.</td>
            </tr>
        </tbody>
    </table>

    <p>En señal de conformidad las partes suscriben este documento en la ciudad de Satipo, a los _____ días del mes de ____________ de _______.</p>

    <table style="width: 100%;margin-top: 150px;font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 50%;vertical-align: top;">
                    <table>
                        <tbody>
                            <tr>
                                <div style="padding-right: 20px;">
                                    <hr style="margin-top: 0;">
                                    <h4 class="text-center" style="max-width: 300px;margin: 25px auto;"><?php echo $business['nombreEmpresa'] ?></h4>
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
                                            <div>DNI:</div>
                                            <div style="color: transparent;">APELLIDOS Y NOMBRES</div>
                                            <div>DOMICILIO:</div>
                                            <div>CELULAR:</div>
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
                                            <div>DNI:</div>
                                            <div style="color: transparent;">APELLIDOS Y NOMBRES</div>
                                            <div>DOMICILIO:</div>
                                            <div>CELULAR:</div>
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
                                            <div>DNI:</div>
                                            <div style="color: transparent;">APELLIDOS Y NOMBRES</div>
                                            <div>DOMICILIO:</div>
                                            <div>CELULAR:</div>
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
        </tbody>
    </table>

</body>

</html>