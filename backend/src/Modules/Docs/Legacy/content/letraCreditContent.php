<?php


use CrediSoporte\Domain\Models\Credit;
use Luecano\NumeroALetras\NumeroALetras;
use Picqer\Barcode\BarcodeGeneratorHTML;

$formatter = new NumeroALetras();
$generator = new BarcodeGeneratorHTML();

$creditId = $_GET['creditId'];
$creditCod = str_pad($_GET['creditId'], 5, '0', STR_PAD_LEFT);

$business = $database::table('tdatos')->first();

$credit = Credit::with('relations')->select(
    'tprestamo.idP',
    'tprestamo.idCG',
    'tprestamo.capital',
    'tprestamo.penalty',
    'tprestamo.interest_rate',
    'tprestamo.fechaDesembolso',
    'tprestamo.idV',
    'tclie_general.ap',
    'tclie_general.am',
    'tclie_general.nom',
    'tclie_general.cel',
    'tclie_general.dni'
)
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->find($creditId);

$transaction = $database->table('tcaja_usu_detal')
    ->join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
    ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
    ->where('tcaja_usu_detal.conejo', $credit->idP)
    ->where('tcaja_usu_detal.tipo', '2')
    ->where('tcaja_usu_detal.estadodt', '2')
    ->select(
        'tcaja_usu_detal.idCAD',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU'
    )
    ->first();

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
            'aval.ap as aval_ap',
            'aval.am as aval_am',
            'aval.nom as aval_nom'
        )
        ->first();
}

$path = '../../admin/titulo/img/' . $business->logo;
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

function getRelationToString($value)
{
    switch ($value) {
        case 'spouse':
            return 'Conyuge';
            break;

        case 'endorsement':
            return 'Aval';
            break;
        
        default:
            return "";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letra de crédito</title>

    <style>
        html,
        body {
            font-size: 10px;
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .vertical {
            writing-mode: vertical-rl;
        }

        .upright {
            text-orientation: upright;
        }

        .sideways {
            text-orientation: sideways;
        }

        @page {
            margin: 20px;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td rowspan="4">
                    <img src="<?php echo $base64 ?>" alt="" style="max-height: 60px;">
                </td>
                <td>Desembolsado por: <b><?php echo $transaction?->apU . ' ' . $transaction?->amU . ' ' . $transaction?->nomU ?></b></td>
                <td>Monto Otorgado: <b><?php echo number_format($credit->capital / 10, 2) ?></b></td>
                <td>Fecha: <b><?php echo $credit->fechaDesembolso ?></b></td>
            </tr>
            <tr>
                <td>Cliente: <b><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></b></td>
                <td colspan="2" rowspan="3" style="text-align: center;">
                    <div>Letra Nº: <?php echo $creditCod ?></div>
                    <div style="margin: 0 auto; display: inline-block;">
                        <?php echo $generator->getBarcode($creditCod, $generator::TYPE_CODE_128) ?>
                    </div>
                </td>
            </tr>

            <?php foreach ($credit->relations as $key => $rel) { ?>
                <tr>
                    <td><?php echo getRelationToString($rel->pivot->type) ?>: <b><?php echo $rel->ap . ' ' . $rel->am . ' ' . $rel->nom ?></b></td>
                </tr>
            <?php } ?>

            <?php if ($relation) { ?>
                <tr>
                    <td>Conyuge: <b><?php echo $relation->conyuge_ap . ' ' . $relation->conyuge_am . ' ' . $relation->conyuge_nom ?></b></td>
                </tr>
                <tr>
                    <td>Aval: <b><?php echo $relation->aval_ap . ' ' . $relation->aval_am . ' ' . $relation->aval_nom ?></b></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 0; text-align: center; font-size: 11px;">
                    <div>C</div>
                    <div>L</div>
                    <div>A</div>
                    <div>U</div>
                    <div>S</div>
                    <div>U</div>
                    <div>L</div>
                    <div>A</div>
                    <div>S</div>
                    <div style="margin-top: 15px;"></div>
                    <div>E</div>
                    <div>S</div>
                    <div>P</div>
                    <div>E</div>
                    <div>C</div>
                    <div>I</div>
                    <div>A</div>
                    <div>L</div>
                    <div>E</div>
                    <div>S</div>
                </td>
                <td style="width: 0;">
                    <div style="position: relative; width: 130px;height: 370px;">
                        <div style="transform: rotate(-90deg); transform-origin: top left; position: absolute; top: 100%; width: 370px;">
                            <ol style="font-size: 9px; line-height: 0.9; padding-left: 15px;">
                                <li>En caso de mora, ésta Letra de Cambio generará las tasas de intereses compensatorio y moratorio más altas que la Ley permita a su ultimo tenedor.</li>
                                <li>El plazo de su vencimiento podrá ser prorroga por el Tenedor, por el plazo de que éste señale, sin que sea nesesaria la intervención del obligado principal ni de los solidarios.</li>
                                <li>Su importe debe ser pagado sólo en la misma moneda que expresa este titulo valor.</li>
                            </ol>
                            <table style="font-size: 10px; margin: 0 auto; width: 100%; margin-top: 20px;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 0 20px 0 0;">
                                            <div style="border-top: 1px solid black;width: 150px; text-align: center; padding-top: 4px; font-size: 9px;">ACEPTANTE</div>
                                            <div style="font-size: 8px;margin-top: 7px;">Nombre / Representante:</div>
                                            <div style="font-size: 8px;">D.O.I.:</div>
                                        </td>
                                        <td style="padding: 0 20px 0 0;">
                                            <div style="border-top: 1px solid black;width: 150px; text-align: center; padding-top: 4px; font-size: 9px;">ACEPTANTE</div>
                                            <div style="font-size: 8px;margin-top: 7px;">Nombre / Representante:</div>
                                            <div style="font-size: 8px;">D.O.I.:</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="overflow: hidden;">
                        <table style="border: 1px solid black; width: 100%; border-collapse: collapse; font-size: 8px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid black; padding: 2px 2px;">NUMERO LETRA</th>
                                    <th style="border: 1px solid black; padding: 2px 2px;">REF. DEL GIRADOR</th>
                                    <th style="border: 1px solid black; padding: 2px 2px;">LUGAR DE GIRO</th>
                                    <th style="border: 1px solid black; padding: 2px 2px;">FECHA DE GIRO</th>
                                    <th style="border: 1px solid black; padding: 2px 2px;">FECHA DE VENCIMIENTO</th>
                                    <th style="border: 1px solid black; padding: 2px 2px;">MONEDA E IMPORTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="border: 1px solid black;"></td>
                                    <td rowspan="2" style="border: 1px solid black;"></td>
                                    <td rowspan="2" style="border: 1px solid black;"></td>
                                    <td style="border: 1px solid black; font-weight: bold; text-align: center; height: 15px;">DÍA / MES / AÑO</td>
                                    <td style="border: 1px solid black; font-weight: bold; text-align: center; height: 15px;">DÍA / MES / AÑO</td>
                                    <td rowspan="2" style="border: 1px solid black;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; height: 30px;"></td>
                                    <td style="border: 1px solid black;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="font-size: 10px;margin-top: 7px;">Por esta <b>LETRA DE CAMBIO</b> se servirá(n), pagar incondicionalmente a la Orden de ............................................................................</div>
                    <div style="padding: 7px 5px; border: 1px solid black; border-radius: 5px; margin-top: 7px; font-size: 10px;">La cantidad de</div>
                    <div style="font-size: 10px;margin-top: 7px;">En el siguiente lugar de pago, o con cargo en la cuenta del Banco:</div>

                    <table style="width: 100%;margin-top: 7px;">
                        <tbody>
                            <tr>
                                <td style="width: 50%;">
                                    <div style="border: 1px solid black; border-radius: 5px; height: 85px; padding: 5px;">
                                        <div style="font-size: 10px;"><b>Aceptante</b></div>
                                        <div>...................................................................................</div>
                                        <div>...................................................................................</div>
                                        <div>Domicilio....................................................................</div>
                                        <div>................................. Localidad.................................</div>
                                        <div>D.O.I. ...................... Telefono ..................................</div>
                                    </div>
                                </td>
                                <td style="width: 50%;">
                                    <div style="border: 1px solid black;border-radius: 5px; height: 85px; padding: 5px;">
                                        <div style="font-size: 8px;">Importe a debital en la siguiente cuenta del BANCO que se indica</div>
                                        <table style="width: 100%; border: 1px solid black; border-collapse: collapse; font-size: 9px; margin-top: 5px;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid black;padding: 3px 2px;width: 20%;">BANCO</th>
                                                    <th style="border: 1px solid black;padding: 3px 2px;width: 20%;">OFICINA</th>
                                                    <th style="border: 1px solid black;padding: 3px 2px;width: 40%;">NUMERO DE CUENTA</th>
                                                    <th style="border: 1px solid black;padding: 3px 2px;width: 20%;">DC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="border: 1px solid black;height: 45px;"></td>
                                                    <td style="border: 1px solid black;height: 45px;"></td>
                                                    <td style="border: 1px solid black;height: 45px;"></td>
                                                    <td style="border: 1px solid black;height: 45px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">
                                    <div style="border: 1px solid black; border-radius: 5px; height: 85px; padding: 5px;">
                                        <div style="font-size: 10px;"><b>Aceptante</b></div>
                                        <div>...................................................................................</div>
                                        <div>...................................................................................</div>
                                        <div>Domicilio....................................................................</div>
                                        <div>................................. Localidad.................................</div>
                                        <div>D.O.I. ...................... Telefono ..................................</div>
                                    </div>
                                </td>
                                <td style="width: 50%;">
                                    <div style="border: 1px solid black;border-radius: 5px; height: 85px; padding: 5px;">
                                        <div style="font-size: 10px;">Nomber o Razón Social Girador</div>
                                        <div style="font-size: 10px;">D.O.I</div>
                                        <table style="width: 100%; margin-top: 15px;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right: 15px;">
                                                        <div style="border-top: 1px solid black;padding-top: 5px;text-align: center;">Firma</div>
                                                    </td>
                                                    <td style="padding-left: 15px;">
                                                        <div style="border-top: 1px solid black;padding-top: 5px;text-align: center;">Firma</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="font-size: 10px;">Nombre del Representante Legal</div>
                                        <div style="font-size: 10px;">D.O.I</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="border-top: 1px solid black; height: 50px; padding: 5px 0;position: relative;">
        <div style="font-size: 9px;">No escribir ni firmar debajo de esta linea</div>
        <div style="border-top: 1px solid black; width: 20px; position: absolute; bottom: 0; left: 0;"></div>
        <div style="border-top: 1px solid black; width: 20px; position: absolute; bottom: 0; right: 0;"></div>
    </div>
</body>

</html>