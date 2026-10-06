<?php
include('head.php');
require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Models\Proposal;
use CrediSoporte\Domain\Request\Request;

$request = new Request();

$currentDate = date('Y-m-d');

// $inicio = $request->get('inicio', $currentDate);
// $fin = $request->get('fin', $currentDate);
// $estado = $request->get('estado');

$limit = 10;
$offset = (floatval($request->get('page', 1)) * $limit) - $limit;

$proposalQuery = Proposal::query();

$proposalQuery->join('tclie_general', 'proposals.customer_id', 'tclie_general.idCG')
    ->leftJoin('proposal_responses', 'proposals.id', 'proposal_responses.proposal_id')
    ->where(function ($query) {
        if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '2') {
            $query->where('proposals.user_id', $_COOKIE['user1']);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->get('inicio') !== '' && $request->get('fin') !== '') {
            $query->whereBetween('proposal_responses.disbursement_date', [$request->get('inicio'), $request->get('fin')]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->get('estado') != '') {
            $query->where('proposal_responses.status', $request->get('estado'));
        }
    });

$james = $proposalQuery;

$numberPages = ceil($proposalQuery->count() / $limit);

$proposals = $proposalQuery->orderBy('proposals.id', 'desc')
    ->select(
        'proposals.id',
        'tclie_general.dni',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'proposals.amount',
        'proposals.installment',
        'proposals.rate',
        'proposals.fee',
        'proposals.created_at',
        'proposal_responses.amount as amount_approved',
        'proposal_responses.disbursement_date',
        'proposal_responses.status',
        'proposal_responses.id as proposal_response_id'
    )
    ->offset($offset)
    ->limit($limit)
    ->get();

$resumen = Proposal::join('tclie_general', 'proposals.customer_id', 'tclie_general.idCG')
    ->leftJoin('proposal_responses', 'proposals.id', 'proposal_responses.proposal_id')
    ->where(function ($query) {
        if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '2') {
            $query->where('proposals.user_id', $_COOKIE['user1']);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->get('inicio') !== '' && $request->get('fin') !== '') {
            $query->whereBetween('proposal_responses.disbursement_date', [$request->get('inicio'), $request->get('fin')]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->get('estado') != '') {
            $query->where('proposal_responses.status', $request->get('estado'));
        }
    })
    ->selectRaw('sum(proposal_responses.amount) as amount_approved')
    ->selectRaw('sum(proposals.amount) as proposed_amount')
    ->get();

$totalDesembolso = $resumen[0]['amount_approved'];
$totalPropuesto = $resumen[0]['proposed_amount'];
?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
        </div>

        <h5 style="color:white">Propuestas<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <h3 style="font-weight: bold;">PROPUESTAS</h3>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" id="formFiltros">
            <div class="row">
                <div class="col-md-2">
                    <label>Inicio fecha de desembolso</label>
                    <input type="date" name="inicio" class="form-control" value="<?php echo $request->get('inicio') ?>">
                </div>
                <div class="col-md-2">
                    <label>Fin fecha de desembolso</label>
                    <input type="date" name="fin" class="form-control" value="<?php echo $request->get('fin') ?>">
                </div>
                <div class="col-md-2">
                    <label for="">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="APROBADO" <?php echo ($request->get('estado') === 'APROBADO') ? 'selected' : '' ?>>Aprobado</option>
                        <option value="DESAPROBADO" <?php echo ($request->get('estado') === 'DESAPROBADO') ? 'selected' : '' ?>>Desaprobado</option>
                        <option value="OBSERVADO" <?php echo ($request->get('estado') === 'OBSERVADO') ? 'selected' : '' ?>>Observado</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <a href="<?php echo $_SERVER["PHP_SELF"]; ?>" class="btn btn-success" type="reset" style="margin-right: 5px;">LIMPIAR</a>
                <button class="btn btn-primary" type="submit">BUSCAR</button>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="table table-striped" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha de creación</th>
                    <th>DNI</th>
                    <th>Cliente</th>
                    <th>Monto propuesto</th>
                    <th>Plazo</th>
                    <th>Taza</th>
                    <th>Cuota promedio</th>
                    <th>Estado</th>
                    <th>Monto aprobado</th>
                    <th>Fecha desembolso</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proposals as $proposal) { ?>
    
                    <tr>
                        <td><?php echo str_pad($proposal->id, 3, '0', STR_PAD_LEFT) ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($proposal->created_at)) ?></td>
                        <td><?php echo $proposal->dni ?></td>
                        <td><?php echo $proposal->ap . ' ' . $proposal->am . ' ' . $proposal->nom ?></td>
                        <td><?php echo number_format($proposal->amount, 2) ?></td>
                        <td><?php echo $proposal->installment ?></td>
                        <td><?php echo $proposal->rate . '%' ?></td>
                        <td><?php echo number_format($proposal->fee, 2) ?></td>
                        <td>
                            <?php
                            switch ($proposal->status) {
                                case 'APROBADO':
                                    echo "<span style='color: green; font-weight:bold'>$proposal->status</span>";
                                    break;
                                case 'DESAPROBADO':
                                    echo "<span style='color: red;font-weight:bold'>$proposal->status</span>";
                                    break;
                                case 'OBSERVADO':
                                    echo "<span style='color: blue; font-weight:bold'>$proposal->status</span>";
                                    break;
    
                                default:
                                    echo 'NO RESPONDIDO';
                                    break;
                            }
                            ?>
                        </td>
                        <td><?php echo $proposal->proposal_response_id != '' ? number_format($proposal->amount_approved, 2) : '' ?></td>
                        <td><?php echo $proposal->disbursement_date ?></td>
                        <td>
                            <a href="./../app//pdf/propuesta.php?proposalId=<?php echo $proposal->id ?>" target="_blank" class="btn btn-sm btn-default" title="Imprimir propuesta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16">
                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                                    <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.701 19.701 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.716 5.716 0 0 1-.911-.95 11.642 11.642 0 0 0-1.997.406 11.311 11.311 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.27.27 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.647 12.647 0 0 1 1.01-.193 11.666 11.666 0 0 1-.51-.858 20.741 20.741 0 0 1-.5 1.05zm2.446.45c.15.162.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.881 3.881 0 0 0-.612-.053zM8.078 5.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
                                </svg>
                            </a>
                            <a href="./detallePropuesta.php?proposalId=<?php echo $proposal->id ?>" target="_blank" class="btn btn-sm btn-default" title="Ver propuesta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
                                    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                </svg>
                            </a>
                            <?php if ($proposal->proposal_response_id) { ?>
                                <a href="./detalleRespuestaPropuesta.php?proposalId=<?php echo $proposal->id ?>" target="_blank" class='btn btn-sm btn-default' title="Ver respuesta">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-square-text" viewBox="0 0 16 16">
                                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6.8L8 14.333 6.1 11.8a2 2 0 0 0-1.6-.8H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                                        <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
                                    </svg>
                                </a>
                            <?php } else if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '2') { ?>
                                <a href="./responderPropuesta.php?proposalId=<?php echo $proposal->id ?>" target="_blank" class='btn btn-sm btn-default' title="Responder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply" viewBox="0 0 16 16">
                                        <path d="M6.598 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.74 8.74 0 0 0-1.921-.306 7.404 7.404 0 0 0-.798.008h-.013l-.005.001h-.001L7.3 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L2.614 8.254a.503.503 0 0 0-.042-.028.147.147 0 0 1 0-.252.499.499 0 0 0 .042-.028l3.984-2.933zM7.8 10.386c.068 0 .143.003.223.006.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96v-.667z" />
                                    </svg>
                                </a>
                            <?php } ?>
                            <button onclick="eliminarPropuesta('./eliminarPropuesta.php?proposalId=<?php echo $proposal->id ?>', <?php echo  $proposal->id ?>)" class='btn btn-sm btn-outline btn-danger' title="Eliminar propuesta">
                                <i class="glyphicon glyphicon-trash"></i>
                            </button>
                        </td>
                    </tr>
    
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: right;">
        <?php for ($i = 0; $i < $numberPages; $i++) { ?>
            <button class="btn btn-sm <?php echo $request->get('page', 1) == $i + 1 ? 'btn-primary' : 'btn-secundary' ?>" name="page" value="<?php echo $i + 1 ?>" form="formFiltros">
                <?php echo $i + 1 ?>
            </button>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-6 col-md-4 col-lg-3">
            <label for="">TOTAL A DESEMBOLSAR</label>
            <input type="text" class="form-control" value="S/. <?php echo number_format($totalDesembolso, 2) ?>" disabled style="font-weight: bold;color: blue;">
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <label for="">TOTAL PROPUESTO</label>
            <input type="text" class="form-control" value="S/. <?php echo number_format($totalPropuesto, 2) ?>" disabled style="font-weight: bold;color: blue;">
        </div>
    </div>
</div>

<script>
    function eliminarPropuesta(url, numero) {
        if (confirm(`¿Estas seguro de eliminar la propuesta número ${numero}?`)) {
            window.location.href = url;
        }
    }
</script>

<?php include('footer.php'); ?>