<?php

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Models\User;

include 'head.php';

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$users = [];
if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2' || $request->user()->tipoU === '3') {
    $users = User::active()->whereIn('tipoU', [3, 4])->get();
} else {
    $users = User::active()->where('idU', $request->user()->idU)->get();
}

$delay = new Delay();
$delay->setHolidays(Holiday::pluck('date'));

$creddIds = collect();

if ($request->with_debt === "SI" || $request->with_debt === "NO") {
    $credd = Credit::with('installments', 'condoneDates')
        ->where('estado', 5)
        ->when($request->employee, function ($query) use ($request) {
            $query->where('tprestamo.user_id', $request->employee);
        })
        ->when($request->customer, function ($query) use ($request) {
            $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
                ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        })
        ->chunk(1200, function ($credits) use ($delay, $creddIds) {
            foreach ($credits as $credit) {
                $delay->penalty = $credit->penalty;
                $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
                $delay->payment_period = $credit->payment_period;

                $deuda = 0;

                foreach ($credit->installments as $keyInstallment => $installment) {
                    $deuda += $installment->capitalDebt();
                    $deuda += $installment->interestDebt();

                    $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                    $deuda += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                    if ($deuda > 0) {
                        $creddIds->push($credit->idP);
                        break;
                    }
                }
            }
        });
}

$totalCredits = Credit::join('tclie_general', 'tclie_general.idCG', 'tprestamo.idCG')
    ->where('tprestamo.estado', 5)
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.finished_at', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->when($request->with_debt === "SI", function ($query) use ($creddIds) {
        $query->whereIn('tprestamo.idP', $creddIds);
    })
    ->when($request->with_debt === "NO", function ($query) use ($creddIds) {
        $query->whereNotIn('tprestamo.idP', $creddIds);
    })
    ->count();

$credits = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tclie_general.idCG', 'tprestamo.idCG')
    ->where('tprestamo.estado', 5)
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.finished_at', $request->only(['startDate', 'endDate']));
    })
    ->when($request->with_debt === "SI", function ($query) use ($creddIds) {
        $query->whereIn('tprestamo.idP', $creddIds);
    })
    ->when($request->with_debt === "NO", function ($query) use ($creddIds) {
        $query->whereNotIn('tprestamo.idP', $creddIds);
    })
    ->groupBy('tprestamo.idP')
    ->orderBy('tprestamo.finished_at', 'desc')
    ->select(
        'tprestamo.idP',
        'tprestamo.capital',
        'tprestamo.interest_rate',
        'tprestamo.number_installments',
        'tprestamo.n_credito',
        'tprestamo.payment_period',
        'tprestamo.penalty',
        'tprestamo.discount',
        'tprestamo.finished_at'
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

$resumen1 = Credit::selectRaw('sum(tprestamo.capital) as capital')
    ->selectRaw('sum(tprestamo.discount) as discount')
    ->selectRaw('count(*) as total')
    ->where('tprestamo.estado', 5)
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.finished_at', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->when($request->with_debt === "SI", function ($query) use ($creddIds) {
        $query->whereIn('tprestamo.idP', $creddIds);
    })
    ->when($request->with_debt === "NO", function ($query) use ($creddIds) {
        $query->whereNotIn('tprestamo.idP', $creddIds);
    })
    ->first();

$resumen2 = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->selectRaw('sum(tpresta_detalle.capital) as capital')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.capital_payment) as capital_payment')
    ->selectRaw('sum(tpresta_detalle.interest_payment) as interest_payment')
    ->selectRaw('sum(tpresta_detalle.delay_payment) as delay_payment')
    ->where('tprestamo.estado', 5)
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.finished_at', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->when($request->with_debt === "SI", function ($query) use ($creddIds) {
        $query->whereIn('tprestamo.idP', $creddIds);
    })
    ->when($request->with_debt === "NO", function ($query) use ($creddIds) {
        $query->whereNotIn('tprestamo.idP', $creddIds);
    })
    ->first();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Creditos finalizados <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>Fecha de cancelación</label>
                    <div style="display: flex;">
                        <div style="flex: 1 1 auto; display: flex; align-items: center; margin-right: 20px;">
                            <label style="margin-right: 20px;">De: </label>
                            <input type="date" name="startDate" value="<?php echo $request->startDate ?>" class="form-control">
                        </div>
                        <div style="flex: 1 1 auto; display: flex; align-items: center;">
                            <label style="margin-right: 20px;">A: </label>
                            <input type="date" name="endDate" value="<?php echo $request->endDate ?>" class="form-control">
                        </div>
                    </div>
                </div>
                <div style="margin-right: 20px;">
                    <label>Funcionario</label>
                    <select name="employee" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($users as $user) { ?>
                            <option <?php echo $user->idU == $request->employee ? 'selected' : '' ?> value="<?php echo $user->idU ?>"><?php echo $user->apU . ' ' . $user->amU ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div style="margin-right: 20px;">
                    <label>Cliente</label>
                    <input type="text" name="customer" value="<?php echo $request->customer ?>" class="form-control">
                </div>
                <div style="margin-right: 20px;">
                    <label>Pendiente</label>
                    <select class="form-control" name="with_debt">
                        <option value="">Todos</option>
                        <option value="SI" <?php echo $request->with_debt === "SI" ? "selected" : "" ?>>Si</option>
                        <option value="NO" <?php echo $request->with_debt === "NO" ? "selected" : "" ?>>No</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Buscar</button>
                    <button type="button" class="btn btn-success" onclick="mostrarTodo()">Mostrar Todos</button class="form-control">
                    <button type="submit" formaction="./../app/exel/creditosFinalizados.php" formmethod="post" class="btn btn-warning">EXPORTAR A EXEL</button>
                </div>
            </div>
        </form>

        <div style="overflow-y: auto;">
            <table class="table table-striped" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th>Codigo credito</th>
                        <th>Cliente</th>
                        <th>Fecha inicio</th>
                        <th>Fecha final</th>
                        <th>Forma pago</th>
                        <th>Número cuotas</th>
                        <th>Prestamo</th>
                        <th>Tasa</th>
                        <th>Interes</th>
                        <th>Abono mora</th>
                        <th>Total abonado</th>
                        <th>Fecha cancelación</th>
                        <th>Cuotas retrasados</th>
                        <th>Dias vencido</th>
                        <th>Deuda pendiente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credits as $credit) { ?>

                        <?php
                        $delay->penalty = $credit->penalty;
                        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
                        $delay->payment_period = $credit->payment_period;

                        $firstInstallment = $credit->installments[0];
                        $lastInstallment = $credit->installments[count($credit->installments) - 1];

                        $sumInterest = 0;
                        $capitalPayment = 0;
                        $interestPayment = 0;
                        $penaltyPayment = 0;

                        $deuda = 0;

                        $cuotasRetrasadas = 0;
                        foreach ($credit->installments as $keyInstallment => $installment) {
                            $sumInterest += $installment->interest;
                            $capitalPayment += $installment->capital_payment;
                            $interestPayment += $installment->interest_payment;

                            $deuda += $installment->capitalDebt();
                            $deuda += $installment->interestDebt();

                            $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                            $deuda += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                            if ($installment->expiration_at < $installment->payment_date) {
                                $cuotasRetrasadas++;
                            }
                        }

                        $diasDeRetraso = 0;
                        if ($lastInstallment->expiration_at < $credit->finished_at) {
                            $ini = new DateTime($lastInstallment->expiration_at);
                            $fin = new DateTime($credit->finished_at);
                            $diff = $ini->diff($fin);
                            $diasDeRetraso = $diff->days;
                        }

                        $statusResumenStyle = 'background: #F9EBEA; color: red; font-weight: 600;';

                        if ($cuotasRetrasadas === 0) {
                            $statusResumenStyle = 'background: #D4EFDF; color: #239B56; font-weight: 600;';
                        } else if ($diasDeRetraso > 0) {
                            $statusResumenStyle = 'background: #E8DAEF;color: #7D3C98; font-weight: 600;';
                        }

                        ?>
                        <tr>
                            <td><?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></td>
                            <td style="<?php echo $statusResumenStyle ?>"><?php echo $credit->customer ?></td>
                            <td><?php echo date('d/m/Y', strtotime($firstInstallment->expiration_at)) ?></td>
                            <td><?php echo date('d/m/Y', strtotime($lastInstallment->expiration_at)) ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td><?php echo $credit->number_installments ?></td>
                            <td><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td><?php echo round($credit->interest_rate, 2) . '%' ?></td>
                            <td><?php echo number_format($sumInterest / 10, 2) ?></td>
                            <td><?php echo number_format($penaltyPayment / 10) ?></td>
                            <td><?php echo number_format(($capitalPayment + $interestPayment) / 10, 2) ?></td>
                            <td><?php echo is_null($credit->finished_at) ? "" : date('d/m/Y', strtotime($credit->finished_at)) ?></td>
                            <td><?php echo $cuotasRetrasadas ?></td>
                            <td><?php echo $diasDeRetraso ?></td>
                            <td>
                                <?php if ($deuda > 0) {
                                    echo "SI";
                                } else {
                                    echo "NO";
                                } ?>
                            </td>
                            <td>
                                <div style="white-space: nowrap;">
                                    <button onclick="showHistorialDePago(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Historial de pagos">
                                        <img src="./../public/resource/icons/pago.png" width="20" alt="">
                                    </button>
                                    <button onclick="showResumenCredito(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Resumen credito">
                                        <img src="./../public/resource/icons/cronograma.png" width="20" alt="">
                                    </button>
                                    <button onclick="showVoucherDesembolso(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Boucher de desembolso">
                                        <img src="./../public/resource/icons/factura.png" width="20" alt="">
                                    </button>
                                    <?php if ($deuda > 0) { ?>
                                        <button class="btn btn-sm btn-primary" onclick="activarCredito(<?php echo $credit->idP ?>)">Activar</button>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php for ($i = 1; $i <= ceil($resumen1->total / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                    <?php echo $i ?>
                </button>
            <?php } ?>
        </div>

        <!-- <?php $help_interest = $resumen2->capital - $resumen1->capital ?> -->
        <div style="display: grid; grid-template-columns: repeat(6, minmax(150px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <label>N. CRÉDITOS</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo $resumen1->total ?>">
            </div>
            <div>
                <label>CAPITAL</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo number_format($resumen1->capital / 10, 2) ?>">
            </div>
            <div>
                <label>INTERES</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo number_format($resumen2->interest / 10, 2) ?>">
            </div>
            <div>
                <label>MORA</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #2C3E50;" value="<?php echo number_format($resumen2->delay_payment / 10, 2) ?>">
            </div>
            <div>
                <label>DESCUENTO</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #F39C12;" value="<?php echo number_format($resumen1->discount / 10, 2) ?>">
            </div>
        </div>

    </div>
</div>

<script>
    function showHistorialDePago(creditId) {
        window.open('./../app/pdf/historialDePago.php?creditId=' + creditId);
    }

    function showResumenCredito(creditId) {
        window.open('./../app/pdf/resumenCredito.php?creditId=' + creditId);
    }

    function showVoucherDesembolso(creditId) {
        window.open('./../app/pdf/voucherDesembolso.php?creditId=' + creditId);
    }

    function navigateToPage(page) {
        const query = new URLSearchParams(window.location.search);
        query.set('page', page);

        window.location.href = window.location.pathname + '?' + query.toString();
    }

    function mostrarTodo() {
        window.location.href = window.location.pathname;
    }

    let activando = false;

    function activarCredito(creditId) {
        if (activando) {
            return;
        }

        activando = true;

        swal({
                title: `¿Seguro que deseas activar el crédito?`,
                icon: "warning",
                buttons: ["No, salir", "Si, activar credito"],
            })
            .then(d => {
                if (d) {
                    fetch(`../app/api/activarCredito.php?creditId=${creditId}`)
                        .then(response => response.json())
                        .then(data => {
                            swal({
                                    title: "Se activo el credito.",
                                    icon: "success"
                                })
                                .then(_ => {
                                    location.reload();
                                })
                        })
                        .catch(error => {
                            swal({
                                title: "Error",
                                text: "Lo sentimos, no se pudo activar el credito.",
                                icon: "error"
                            })
                        })
                        .finally(_ => {
                            activando = false
                        });
                } else {
                    activando = false
                }
            })
    }
</script>

<?php include 'footer.php' ?>