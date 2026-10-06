<?php


use CrediSoporte\Domain\Models\Dictionary;
use CrediSoporte\Domain\Models\Proposal;
use CrediSoporte\Domain\Request\Request;

$request = new Request();

$dictionaries = Dictionary::all(['id', 'description']);

function getDescriptionOf($itemId, $items)
{
    $value = $items->find($itemId);

    if (is_null($value)) {
        return '';
    }

    return $value->description;
};

$proposal = Proposal::with('response')
    ->leftJoin('tusuario', 'proposals.user_id', 'tusuario.idU')
    ->join('tclie_general', 'proposals.customer_id', 'tclie_general.idCG')
    ->leftJoin('tclie_general as spouse', 'proposals.spouse_id', 'spouse.idCG')
    ->leftJoin('tclie_general as aval', 'proposals.aval_id', 'aval.idCG')
    // ->leftJoin('dictionaries as client_type', 'tclie_general.client_type', 'client_type.id')
    // ->leftJoin('dictionaries as client_type', 'tclie_general.client_type', 'client_type.id')
    ->select(
        'proposals.*',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU',
        'tclie_general.idCG',
        'tclie_general.dni as document',
        'tclie_general.nom as name',
        'tclie_general.cel as cell_phone',
        'tclie_general.client_type as client_type_id',
        'tclie_general.risk_profile_id',
        'spouse.idCG as spouse_id',
        'spouse.dni as spouse_document',
        'spouse.nom as spouse_name',
        'spouse.cel as spouse_cell_phone',
        'spouse.risk_profile_id as spouse_risk_profile_id',
        'aval.idCG as aval_id',
        'aval.dni as aval_document',
        'aval.nom as aval_name',
        'aval.cel as aval_cell_phone',
        'aval.risk_profile_id as aval_risk_profile_id'
    )
    ->selectRaw('concat(tclie_general.ap, " ", tclie_general.am) as surname')
    ->selectRaw('concat(spouse.ap, " ", spouse.am) as spouse_surname')
    ->selectRaw('concat(aval.ap, " ", aval.am) as aval_surname')
    ->find($request->get('proposalId'));

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta</title>

    <style>
        html,
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
        }

        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid black;
            position: relative;
        }

        .checkbox.checked::after {
            content: '';
            display: inline-block;
            width: 4px;
            height: 9px;
            border-bottom: 3px solid black;
            border-right: 3px solid black;

            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            /* transform-origin: bottom right; */
        }

        .subtitle {
            text-align: center;
            background: #ECEFF1;
            padding: 5px 0;
        }

        .font-bold {
            font-weight: bold;
        }

        hr {
            border-top: 1px solid black;
            border-bottom: 0;
            border-left: 0;
            border-right: 0;
        }

        @page {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">SOLICITUD DE CRÉDITO <?php echo str_pad($request->get('proposalId'), 2, '0', STR_PAD_LEFT) ?></h2>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td><b>Asesor:</b> <?php echo $proposal->apU . ' ' . $proposal->amU . ' ' . $proposal->nomU ?></td>
                <td style="text-align: right;">FECHA/HORA REG: <?php echo date('d/m/Y H:i', strtotime($proposal->created_at)) ?></td>
            </tr>
        </tbody>
    </table>

    <h4 class="subtitle">DATOS DEL CLIENTE</h4>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td>
                    <b>Documento:</b>
                    <div><?php echo 'DNI' ?></div>
                </td>
                <td>
                    <b>Número de documento:</b>
                    <div><?php echo $proposal->document ?></div>
                </td>
                <td>
                    <b>Tipo de cliente:</b>
                    <div><?php echo getDescriptionOf($proposal->client_type_id, $dictionaries) ?></div>
                </td>
                <td>
                    <b>Telefono:</b>
                    <div><?php echo $proposal->cell_phone ?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Código:</b>
                    <div><?php echo $proposal->idCG ?></div>
                </td>
                <td>
                    <b>Apellidos:</b>
                    <div><?php echo $proposal->surname ?></div>
                </td>
                <td>
                    <b>Nombres:</b>
                    <div><?php echo $proposal->name ?></div>
                </td>
                <td>
                    <b>Perfil de riesgo</b>
                    <div>
                        <?php
                        switch (getDescriptionOf($proposal->risk_profile_id, $dictionaries)) {
                            case 'red':
                                echo '<span style="color: red; font-weight: bold;">Alto Riesgo</span>';
                                break;
                            case 'yellow':
                                echo '<span style="color: #FFC400; font-weight: bold;">Mediano riesgo</span>';
                                break;
                            case 'gray':
                                echo '<span style="color: gray; font-weight: bold;">No registra información en el mes</span>';
                                break;
                            case 'green':
                                echo '<span style="color: green; font-weight: bold;">Sin riesgo</span>';
                                break;
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <h4 class="subtitle">PROPUESTA DE CRÉDITO</h4>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td>
                    <div class="font-bold">Monto Propuesto</div>
                    <div>S/. <?php echo number_format($proposal->amount, 2) ?></div>
                </td>
                <td>
                    <div class="font-bold">Tipo de crédito</div>
                    <div><?php echo getDescriptionOf($proposal->credit_type_id, $dictionaries) ?></div>
                </td>
                <td>
                    <div class="font-bold">Modalidad</div>
                    <div><?php echo getDescriptionOf($proposal->modality_id, $dictionaries) ?></div>
                </td>
                <td>
                    <div class="font-bold">Tipo de pago</div>
                    <div><?php echo getDescriptionOf($proposal->payment_type_id, $dictionaries) ?></div>
                </td>
                <td>
                    <div class="font-bold">Plazo propuesto</div>
                    <div><?php echo $proposal->installment ?></div>
                </td>
                <td>
                    <div class="font-bold">Tasa</div>
                    <div><?php echo $proposal->rate ?>%</div>
                </td>
                <td>
                    <div class="font-bold">Cuota</div>
                    <div><?php echo number_format($proposal->fee, 2) ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4"><b>Situación de domicilio:</b> <?php echo getDescriptionOf($proposal->housing_type_id, $dictionaries) ?></td>
                <td colspan="3"><b>Situación de negocio:</b> <?php echo getDescriptionOf($proposal->business_type_id, $dictionaries) ?></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td>CONYUGE</td>
                <td>
                    <?php if ($proposal->aval_id) { ?>
                        SI <span class="checkbox checked"></span>
                    <?php } else { ?>
                        NO <span class="checkbox"></span>
                    <?php } ?>
                </td>
                <td>
                    <b>DNI:</b>
                    <div><?php echo $proposal->spouse_document ?></div>
                </td>
                <td>
                    <b>Apellidos y Nombres:</b>
                    <div><?php echo $proposal->spouse_surname . ' ' . $proposal->spouse_name ?></div>
                </td>
                <td>
                    <b>Telefono:</b>
                    <div><?php echo $proposal->spouse_cell_phone ?></div>
                </td>
                <td>
                    <b>Perfil de riesgo:</b>
                    <div>
                        <?php
                        switch (getDescriptionOf($proposal->spouse_risk_profile_id, $dictionaries)) {
                            case 'red':
                                echo '<span style="color: red; font-weight: bold;">Alto Riesgo</span>';
                                break;
                            case 'yellow':
                                echo '<span style="color: #FFC400; font-weight: bold;">Mediano riesgo</span>';
                                break;
                            case 'gray':
                                echo '<span style="color: gray; font-weight: bold;">No registra información en el mes</span>';
                                break;
                            case 'green':
                                echo '<span style="color: green; font-weight: bold;">Sin riesgo</span>';
                                break;
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>AVAL</td>
                <td>
                    <?php if ($proposal->aval_id) { ?>
                        SI <span class="checkbox checked"></span>
                    <?php } else { ?>
                        NO <span class="checkbox"></span>
                    <?php } ?>
                </td>
                <td>
                    <b>DNI:</b>
                    <div><?php echo $proposal->aval_document ?></div>
                </td>
                <td>
                    <b>Apellidos y Nombres:</b>
                    <div><?php echo $proposal->aval_surname . ' ' . $proposal->aval_name ?></div>
                </td>
                <td>
                    <b>Telefono:</b>
                    <div><?php echo $proposal->aval_cell_phone ?></div>
                </td>
                <td>
                    <b>Telefono:</b>
                    <div>
                        <?php
                        switch (getDescriptionOf($proposal->aval_risk_profile_id, $dictionaries)) {
                            case 'red':
                                echo '<span style="color: red; font-weight: bold;">Alto Riesgo</span>';
                                break;
                            case 'yellow':
                                echo '<span style="color: #FFC400; font-weight: bold;">Mediano riesgo</span>';
                                break;
                            case 'gray':
                                echo '<span style="color: gray; font-weight: bold;">No registra información en el mes</span>';
                                break;
                            case 'green':
                                echo '<span style="color: green; font-weight: bold;">Sin riesgo</span>';
                                break;
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%;border-collapse: collapse; table-layout: fixed;">
        <tbody>
            <tr>
                <td style="border: 1px solid black; height: 50px; vertical-align: bottom; padding: 10px;">
                    <hr>
                    <div style="text-align: center;">Firma del solicitante</div>
                </td>
                <td style="border: 1px solid black; vertical-align: bottom; padding: 10px;">
                    <hr>
                    <div style="text-align: center;">Firma del conyuge</div>
                </td>
                <td style="border: 1px solid black; vertical-align: bottom; padding: 10px;">
                    <hr>
                    <div style="text-align: center;">Firma del aval</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div>
        <div class="font-bold" style="margin-top: 10px;">Sobre el negocio</div>
        <div><?php echo $proposal->about_business ?></div>
    </div>
    <div>
        <div class="font-bold" style="margin-top: 10px;">Sobre el destino del crédito</div>
        <div><?php echo $proposal->about_destiny ?></div>
    </div>
    <div>
        <div class="font-bold" style="margin-top: 10px;">Sobre la experiencia crediticia</div>
        <div><?php echo $proposal->about_experience ?></div>
    </div>
    <div>
        <div class="font-bold" style="margin-top: 10px;">Sobre el entorno familiar</div>
        <div><?php echo $proposal->about_family ?></div>
    </div>
    <div>
        <div class="font-bold" style="margin-top: 10px;">Evaluación</div>
        <div><?php echo $proposal->evaluation ?></div>
    </div>
    <div>
        <div class="font-bold" style="margin-top: 10px;">Garantias del crédito</div>
        <div id="james"><?php echo $proposal->guaranty ?></div>
    </div>

    <table style="border-collapse: collapse; width: 100%; margin-top: 10px;">
        <tbody>
            <tr>
                <td style="border: 1px solid black; height: 60px; vertical-align: bottom;padding: 10px;">
                    <div style="max-width: 150px;margin: 0 auto;">
                        <hr>
                        <div style="text-align: center;">Firma del Asesor</div>
                    </div>
                    <div>Declaro bajo juramento que toda información ingresada en esta plataforma son reales y requeridos.</div>
                </td>
            </tr>
        </tbody>
    </table>

    <?php if (!is_null($proposal->response)) { ?>
        <h4 class="subtitle">RESOLUCIÓN DE COMITÉ</h4>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="text-align: right;">FECHA DE RESOLUCIÓN <?php echo date('d/m/Y H:i', strtotime($proposal->response->created_at)) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="font-bold">Propuesta del comite de crédito</div>
        <div><?php echo $proposal->response->committee_response ?></div>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <div class="font-bold">Monto aprobado</div>
                        <div>S/. <?php echo number_format($proposal->response->amount, 2) ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Tipo de prestamo</div>
                        <div><?php echo getDescriptionOf($proposal->response->credit_type_id, $dictionaries) ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Tipo de pago</div>
                        <div><?php echo getDescriptionOf($proposal->response->payment_type_id, $dictionaries) ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Tasa</div>
                        <div><?php echo floatval($proposal->response->rate) ?>%</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="font-bold">Monto a desembolsar</div>
                        <div>S/. <?php echo number_format($proposal->response->amount, 2) ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Modalidad</div>
                        <div><?php echo getDescriptionOf($proposal->response->modality_id, $dictionaries) ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Plazo</div>
                        <div><?php echo $proposal->response->installment ?></div>
                    </td>
                    <td>
                        <div class="font-bold">Cuota</div>
                        <div>S/. <?php echo $proposal->response->fee ?></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="font-bold">Condición de la propuesta</div>
        <table>
            <tbody>
                <tr>
                    <td>
                        <?php if ($proposal->response->status === 'APROBADO') { ?>
                            <span class="checkbox checked"></span> Aprobado
                        <?php } else { ?>
                            <span class="checkbox"></span> Aprobado
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($proposal->response->status === 'DESAPROBADO') { ?>
                            <span class="checkbox checked"></span> Desaprobado
                        <?php } else { ?>
                            <span class="checkbox"></span> Desaprobado
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($proposal->response->status === 'OBSERVADO') { ?>
                            <span class="checkbox checked"></span> Observado
                        <?php } else { ?>
                            <span class="checkbox"></span> Observado
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $proposal->response->observation ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="border-collapse: collapse; border: 1px solid black; width: 100%; table-layout: fixed;">
            <tbody>
                <tr>
                    <td style="vertical-align: bottom; height: 50px; padding: 10px;">
                        <hr>
                        <div style="text-align: center;">Firma del funcionario</div>
                    </td>
                    <td style="vertical-align: bottom; height: 50px; padding: 10px;">
                        <hr>
                        <div style="text-align: center;">Firma del funcionario</div>
                    </td>
                    <td style="vertical-align: bottom; height: 50px; padding: 10px; border: 1px solid black">
                        <hr>
                        <div style="text-align: center;">Firma del administrador</div>
                    </td>
                </tr>
            </tbody>
        </table>

    <?php } ?>
</body>

</html>