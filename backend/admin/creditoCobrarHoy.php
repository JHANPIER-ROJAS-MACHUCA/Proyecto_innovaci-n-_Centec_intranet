<?php

include("head.php");

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Models\User;

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$currentDate = $request->get('fecha', date('Y-m-d'));
$lugarCobro = $request->get('lugar_cobro');
$filtroUsuario = $request->get('usuario');

$users = [];
if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2' || $request->user()->tipoU === '3') {
    $users = User::active()->whereIn('tipoU', [3, 4])->get();
} else {
    $users = User::active()->where('idU', $request->user()->idU)->get();
}

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

$credits = Credit::with(['installments', 'condoneDates', 'comments' => function ($query) use ($currentDate) {
    $query->join('tusuario', 'non_payment_justifications.user_id', 'tusuario.idU')
        ->whereDate('non_payment_justifications.created_at', $currentDate)
        ->select(
            'non_payment_justifications.id',
            'non_payment_justifications.credit_id',
            'non_payment_justifications.description',
            'non_payment_justifications.created_at'
        )
        ->selectRaw('concat_ws(" ", tusuario.apU, tusuario.amU, tusuario.nomU) as author');
}])
    ->select(
        'tprestamo.idP',
        'tprestamo.capital',
        'tprestamo.interest_rate',
        'tprestamo.number_installments',
        'tprestamo.penalty',
        'tprestamo.payment_period',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tclie_general.cel',
        'tusuario.apU',
        'non_payment_justifications.id as non_payment_justification_id',
        'non_payment_justifications.created_at',
        'non_payment_justifications.description',
        'autor.apU as autor_ap',
        'autor.amU as autor_am',
        'autor.nomU as autor_nombre'
    )
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->leftJoin('non_payment_justifications', 'tprestamo.idP', 'non_payment_justifications.credit_id')
    ->leftJoin('tusuario as autor', 'non_payment_justifications.user_id', 'autor.idU')
    ->when($request->usuario, function ($query) use ($request) {
        $query->where('tclie_general.idU', $request->usuario);
    })
    ->when($request->lugar_cobro, function ($query) use ($request) {
        $query->where('tprestamo.tlocal', $request->lugar_cobro);
    })
    ->when($request->cliente, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->whereRaw("concat_ws(' ',tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->cliente%'")
                    ->orWhere('tclie_general.dni', 'like', $request->cliente . '%');
            });
        });
    })
    ->when($request->user()->tipoU !== '1' && $request->user()->tipoU !== '2', function ($query) use ($users) {
        $query->whereIn('tclie_general.idU', $users->pluck('idU'));
    })
    ->where('tpresta_detalle.expiration_at', $currentDate)
    ->where('tprestamo.estado', 4)
    ->groupBy('tprestamo.idP')
    ->orderBy('tclie_general.ap')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

$resumen = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 4)
    ->when($request->usuario, function ($query) use ($request) {
        $query->where('tclie_general.idU', $request->usuario);
    })
    ->when($request->lugar_cobro, function ($query) use ($request) {
        $query->where('tprestamo.tlocal', $request->lugar_cobro);
    })
    ->when($request->cliente, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->whereRaw("concat_ws(' ',tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->cliente%'")
                ->orWhere('tclie_general.dni', 'like', $request->cliente . '%');
        });
    })
    ->when($request->user()->tipoU !== '1' && $request->user()->tipoU !== '2', function ($query) use ($users) {
        $query->whereIn('tclie_general.idU', $users->pluck('idU'));
    })
    ->where('tpresta_detalle.expiration_at', $currentDate)
    ->selectRaw('sum(tpresta_detalle.capital) as capital')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.capital_payment + tpresta_detalle.interest_payment) as totalPagado')
    ->selectRaw('count(DISTINCT tpresta_detalle.idP) as totalRows')
    ->first();

$totalCredits = $resumen->totalRows;

//----

$resumentAtrasados = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->where('tprestamo.estado', 4) // activos a cobros
    ->where('tprestamo.payment_period', '!=', 'daily') // diferente de diario
    ->where('tpresta_detalle.expiration_at', '<', $currentDate)
    ->whereNull('tpresta_detalle.payment_date')
    ->selectRaw('count(distinct tprestamo.idP) as totalRows')
    ->selectRaw('sum(tpresta_detalle.capital + tpresta_detalle.interest) as totalCuota')
    ->selectRaw('sum(tpresta_detalle.capital_payment + tpresta_detalle.interest_payment) as totalPagado')
    ->first();

$creditsAtrasados = Credit::with(['installments', 'condoneDates', 'comments' => function ($query) use ($currentDate) {
    $query->join('tusuario', 'non_payment_justifications.user_id', 'tusuario.idU')
        ->whereDate('non_payment_justifications.created_at', $currentDate)
        ->select(
            'non_payment_justifications.id',
            'non_payment_justifications.credit_id',
            'non_payment_justifications.description',
            'non_payment_justifications.created_at'
        )
        ->selectRaw('concat_ws(" ", tusuario.apU, tusuario.amU, tusuario.nomU) as author');
}])
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->where('tprestamo.estado', 4) // activos a cobros
    ->where('tprestamo.payment_period', '!=', 'daily') // diferente de diario
    ->where('tpresta_detalle.expiration_at', '<', $currentDate)
    ->whereNull('tpresta_detalle.payment_date')
    ->groupBy('tprestamo.idP')
    ->select('tprestamo.*', 'tclie_general.*')
    ->selectRaw('concat_ws(" ", tusuario.nomU, tusuario.apU) as portfolio')
    ->get();

?>

<div x-data="credit" class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <h5 style="color:white">Cobro de Crédito<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <div style="display: flex; flex-wrap: wrap;">
            <h3 style="font-weight: bold;">CREDITOS A COBRAR</h3>
            <div style="margin-left: auto; display: flex; flex-wrap: wrap;">
                <div style="margin-right: 10px;">
                    <label>TOTAL CREDITOS</label>
                    <input type="text" class="form-control" value="<?php echo $resumen->totalRows ?>" disabled>
                </div>
                <div style="margin-right: 10px;">
                    <label>MONTO TOTAL A COBRAR</label>
                    <input type="text" class="form-control" style="color: blue; font-weight: bold;" value="S/. <?php echo number_format(($resumen->capital + $resumen->interest) / 10, 2); ?>" disabled>
                </div>
                <div style="margin-right: 10px;">
                    <label>TOTAL COBRADO</label>
                    <input type="text" class="form-control" style="color: #27AE60; font-weight: bold;" value="S/. <?php echo number_format($resumen->totalPagado / 10, 2); ?>" disabled>
                </div>
                <div style="margin-right: 10px;">
                    <label>FALTANTE</label>
                    <input type="text" class="form-control" style="color: red; font-weight: bold;" value="S/. <?php echo number_format(($resumen->capital + $resumen->interest - $resumen->totalPagado) / 10, 2); ?>" disabled>
                </div>
            </div>
        </div>

        <form action="creditoCobrarHoy.php" method="get" style="margin-top: 30px;">
            <div class="row">
                <div class="col-md-3">
                    <label>FECHA DE COBRO</label>
                    <input name="fecha" type="date" class="form-control" value="<?php echo $currentDate; ?>">
                </div>
                <div class="col-md-3">
                    <label>FUNCIONARIO</label>
                    <select name="usuario" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        foreach ($users as $usuario) {
                        ?>
                            <option value="<?php echo $usuario->idU ?>" <?php echo $filtroUsuario == $usuario->idU ? 'selected' : '' ?>>
                                <?php echo $usuario->apU . ' ' . $usuario->amU . ' ' . $usuario->nomU ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>LUGAR DE COBRO</label>
                    <select name="lugar_cobro" class="form-control">
                        <option value="" <?php echo $lugarCobro == '' ? 'selected' : '' ?>>Todos</option>
                        <option value="2" <?php echo $lugarCobro == '2' ? 'selected' : '' ?>>Oficina</option>
                        <option value="1" <?php echo $lugarCobro == '1' ? 'selected' : '' ?>>Campo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>CLIENTE</label>
                    <input type="text" class="form-control" name="cliente" value="<?php echo $request->cliente ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Buscar</button>
                    <button type="button" class="btn btn-info" style="margin-top: 20px;" onclick="mostrarTodo()">Mostrar todo</button>
                    <button type="submit" formaction="./../app/exel/crobrosPorFecha.php" class="btn btn-warning" style="margin-top: 20px;">EXPORTAR A EXCEL</button>
                </div>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table class="table table-striped" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Celular</th>
                        <th>Prestamo</th>
                        <th>Tasa</th>
                        <th>Cuota</th>
                        <th>Tipo pago</th>
                        <th>N° cuotas</th>
                        <th>Cuotas atrasadas</th>
                        <th>Monto atrasado</th>
                        <th>Monto abonado</th>
                        <th>Monto faltante</th>
                        <th>Total mora</th>
                        <th>Total pagar</th>
                        <th style="width: 0;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credits as $credit) { ?>

                        <?php
                        $delay->setPenalty($credit->penalty);
                        $delay->payment_period = $credit->payment_period;
                        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

                        $capital = $credit->capital;
                        $interest = 0;

                        $sumPenaltyDebt = 0;

                        $sumCapitalPending = 0;
                        $sumInterestPending = 0;

                        $installmentNow = null;

                        $cuotasAtrasadas = 0;

                        foreach ($credit->installments as $keyInstallment => $installment) {
                            $interest += $installment->interest;

                            $capitalDebt = $installment->capitalDebt();
                            $interestDebt = $installment->interestDebt();

                            $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                            $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                            $sumPenaltyDebt += $penaltyDebt;

                            if ($installment->expiration_at === date('Y-m-d') && $installmentNow === null) {
                                $installmentNow = $installment;
                            }

                            if ($installment->expiration_at <= date('Y-m-d')) {
                                $sumCapitalPending += $capitalDebt;
                                $sumInterestPending += $interestDebt;
                            }

                            if (
                                $installment->expiration_at < date('Y-m-d') &&
                                (($capitalDebt + $interestDebt) > 0)
                            ) {
                                $cuotasAtrasadas++;
                            }
                        }

                        $style = (($installmentNow->capitalDebt() + $installmentNow->interestDebt()) === 0)
                            ? 'background: #27AE60; color: white; font-weight: 600;'
                            : 'background: #E74C3C; color: white; font-weight: 600;';
                        ?>

                        <tr>
                            <td style="<?php echo $style ?>"><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                            <td><?php echo $credit->cel ?></td>
                            <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td><?php echo round($credit->interest_rate, 2) . '%' ?></td>
                            <td style="text-align: right;"><?php echo number_format(($installmentNow->capital + $installmentNow->interest) / 10, 2) ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td><?php echo $credit->number_installments ?></td>
                            <td style="<?php echo $cuotasAtrasadas > 0 ? 'background: #FFA726; color: white;' : '' ?>"><?php echo $cuotasAtrasadas ?></td>
                            <td style="text-align: right;"><?php echo number_format(($sumCapitalPending + $sumInterestPending - $installmentNow->capitalDebt() + $installmentNow->interestDebt()) / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo number_format(($installmentNow->capital_payment + $installmentNow->interest_payment) / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo number_format(($installmentNow->capitalDebt() + $installmentNow->interestDebt()) / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumPenaltyDebt / 10, 2) ?></td>
                            <td style="<?php echo $style ?> text-align: right;">
                                <?php echo number_format(($sumCapitalPending + $sumInterestPending + $sumPenaltyDebt) / 10, 2) ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php if ($credit->coordinate_lat && $credit->coordinate_lng) { ?>
                                    <a href="./ubicacionCliente.php?customerId=<?php echo $credit->idCG ?>" target="_blank" style="padding: 0 3px; border: 0; background: white;">
                                        <img src="./../public/resource/icons/marcador-de-posicion.png" width="20" alt="">
                                    </a>
                                <?php } ?>
                                <button style="padding: 0 3px; border: 0; background: white;" onclick="showResumenCredito(<?php echo $credit->idP ?>)">
                                    <img src="./../public/resource/icons/cronograma.png" width="20" alt="">
                                </button>
                                <a href="./creditoCobrar.php" style="padding: 0 3px; border: 0; background: white;" title="Ir a crobros">
                                    <img src="./../public/resource/icons/pago-movil.png" width="20" alt="">
                                </a>
                                <?php if (count($credit->comments) > 0) { ?>
                                    <button style="padding: 0 3px; border: 0; background: white;" @click='openComentario(<?php echo $credit->comments[0] ?>)'>
                                        <img src="./../public/resource/icons/speech-bubble.png" width="20px" alt="">
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php for ($i = 1; $i <= ceil($totalCredits / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                    <?php echo $i ?>
                </button>
            <?php } ?>
        </div>

        <div style="display: flex; flex-wrap: wrap; align-items: end; margin-top: 30px;">
            <div>
                <h3>CRÉDITOS VIGENTES ATRASADOS</h3>
            </div>
            <div style="margin-left: auto; display: flex; flex-wrap: wrap; align-items: end;">
                <div style="margin-right: 20px;">
                    <form action="./../app/exel/creditosVigentesAtrasados.php" method="get">
                        <button class="btn btn-warning">EXPORTAR A EXEL</button>
                    </form>
                </div>
                <div style="margin-right: 20px;">
                    <label>Total créditos</label>
                    <input class="form-control" style="width: 200px; color: red; font-weight: bold;" type="text" value="<?php echo $resumentAtrasados->totalRows ?>" disabled />
                </div>
                <div>
                    <label>Total a cobrar</label>
                    <input class="form-control" style="width: 200px; color: red; font-weight: bold;" type="text" value="<?php echo number_format(($resumentAtrasados->totalCuota - $resumentAtrasados->totalPagado) / 10, 2) ?>" disabled />
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table table-striped" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Celular</th>
                        <th>Cartera</th>
                        <th>Prestamo</th>
                        <th>Cuota</th>
                        <th>Tipo pago</th>
                        <th>N° cuotas</th>
                        <th>Cuotas atrasadas</th>
                        <th>Pendiente</th>
                        <th>Mora</th>
                        <th>Total a pagar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($creditsAtrasados as $credit) { ?>

                        <?php
                        $delay->condone_dates = $credit->condoneDates()->pluck('date')->toArray();
                        $delay->payment_period = $credit->payment_period;
                        $delay->penalty = $credit->penalty;

                        $cuotasAtrasadas = 0;
                        $mora = 0;
                        $pendiente = 0;
                        foreach ($credit->installments as $key => $installment) {
                            $nextInstallment = $credit->installments[$key + 1] ?? null;

                            $capitalDebt = $installment->capitalDebt();
                            $interestDebt = $installment->interestDebt();

                            $penalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                            $mora += $penalty;

                            if ($installment->expiration_at < date('Y-m-d')) {
                                $pendiente += $capitalDebt + $interestDebt;
                            }

                            if ($installment->expiration_at < date('Y-m-d') && ($capitalDebt + $interestDebt) > 0) {
                                $cuotasAtrasadas++;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom; ?></td>
                            <td><?php echo $credit->cel; ?></td>
                            <td><?php echo $credit->portfolio; ?></td>
                            <td><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td><?php echo number_format(($credit->installments[0]->capital + $credit->installments[0]->interest) / 10, 2) ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td><?php echo $credit->number_installments ?></td>
                            <td><?php echo $cuotasAtrasadas ?></td>
                            <td><?php echo number_format($pendiente / 10, 2) ?></td>
                            <td><?php echo number_format($mora / 10, 2) ?></td>
                            <td><?php echo number_format(($pendiente + $mora) / 10, 2) ?></td>
                            <td>
                                <?php if ($credit->coordinate_lat && $credit->coordinate_lng) { ?>
                                    <a href="./ubicacionCliente.php?customerId=<?php echo $credit->idCG ?>" target="_blank" style="padding: 0 3px; border: 0; background: white;">
                                        <img src="./../public/resource/icons/marcador-de-posicion.png" width="20" alt="">
                                    </a>
                                <?php } ?>
                                <button style="padding: 0 3px; border: 0; background: white;" onclick="showResumenCredito(<?php echo $credit->idP ?>)">
                                    <img src="./../public/resource/icons/cronograma.png" width="20" alt="">
                                </button>
                                <a href="./creditoCobrar.php" style="padding: 0 3px; border: 0; background: white;" title="Ir a crobros">
                                    <img src="./../public/resource/icons/pago-movil.png" width="20" alt="">
                                </a>
                                <?php if (count($credit->comments) > 0) { ?>
                                    <button @click='openComentario(<?php echo $credit->comments[0] ?>)'>Comentario</button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <template x-if="comment">
        <div @keyup.escape.document="comment = null" x-ref="modalComment" style="position: fixed; inset: 0; z-index: 3000; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; border-radius: 5px; width: 100%; max-width: 400px;">
                <div style="padding: 20px 20px 0;">
                    <div><span style="font-weight: bold;">Fecha creado:</span> <span x-text="comment.created_at"></span></div>
                    <div style="margin-top: 5px;"><span style="font-weight: bold;">Usuario:</span> <span x-text="comment.author"></span></div>
                    <div style="margin-top: 5px;"><span style="font-weight: bold;">Descripción:</span> <span x-text="comment.description"></span></div>
                </div>
                <div style="padding: 20px; text-align: right;">
                    <button class="btn btn-secundary" @click="comment = null">Cerrar</button>
                </div>
            </div>
        </div>
    </template>
</div>


<script>
    function showResumenCredito(creditId) {
        window.open('./../app/pdf/resumenCredito.php?creditId=' + creditId);
    }

    function navigateToPage(page) {
        const query = new URLSearchParams(window.location.search);
        query.set('page', page);

        window.location.href = window.location.pathname + '?' + query.toString();
    }

    function mostrarTodo() {
        window.location.href = window.location.pathname;
    }
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('credit', () => ({
            comment: null,
            openComentario(comment) {
                this.comment = comment
            }
        }))
    })
</script>

<script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>
<?php
include("footer.php");
?>
<script src="functiones/cobro.js"></script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#table2').DataTable({
            pageLength: 15,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                /*{ extend: 'copy'},
                {extend: 'csv'},*/
                {
                    extend: 'excel',
                    title: 'Cobros '
                },
                /*{extend: 'pdf', title: 'ExampleFile'},*/
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });

    });
</script>