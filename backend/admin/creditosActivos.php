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

$totalCredits = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('estado', 4)
    ->where(function ($query) use ($request) {
        if ($request->startDate and $request->endDate) {
            $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->employee) {
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->customer) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        }
    })
    ->count();

$credits = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 4)
    ->where(function ($query) use ($request) {
        if ($request->startDate and $request->endDate) {
            $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->employee) {
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->customer) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        }
    })
    ->orderBy('tprestamo.idP', 'desc')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

$resumen = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->selectRaw('sum(tpresta_detalle.capital) as capital')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.capital_payment) as capitalAbonado')
    ->selectRaw('sum(tpresta_detalle.interest_payment) as interesAbonado')
    ->where('tprestamo.estado', 4)
    ->where(function ($query) use ($request) {
        if ($request->startDate and $request->endDate) {
            $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->employee) {
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->customer) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        }
    })
    ->first();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Creditos activos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>Fecha desembolso</label>
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
                <div>
                    <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Buscar</button>
                    <button type="button" class="btn btn-success" style="margin-right: 10px;" onclick="mostrarTodo()">Mostrar Todos</button class="form-control">
                    <button type="submit" formaction="./../app/exel/creditosActivos.php" formmethod="post" class="btn btn-warning">EXPORTAR A EXEL</button>
                </div>
            </div>
        </form>

        <div style="overflow-y: auto;">
            <table class="table table-striped" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th>Codigo credito</th>
                        <th>Cliente</th>
                        <th>Fecha desembolso</th>
                        <th>Fecha finalización</th>
                        <th>Prestamo</th>
                        <th>Tasa</th>
                        <th>Interes</th>
                        <th>Forma</th>
                        <th>Número cuotas</th>
                        <th>Total pagar</th>
                        <th>Abono capital</th>
                        <th>Abono interes</th>
                        <th>Saldo total</th>
                        <th>Saldo capital</th>
                        <th>Saldo interes</th>
                        <th>Cuotas atrasados</th>
                        <th>Monto mora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $delay = new Delay;
                    $delay->setHolidays(Holiday::pluck('date'));
                    ?>

                    <?php foreach ($credits as $key => $credit) { ?>
                        <?php

                        $delay->setPenalty($credit->penalty);
                        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
                        $delay->payment_period = $credit->payment_period;

                        $sumCapital = 0;
                        $sumInterest = 0;
                        $sumPenalty = 0;

                        $abonoCapital = 0;
                        $abonoInterest = 0;

                        $cuotasAtrasados = 0;
                        $cuotasAdelantadas = 0;

                        foreach ($credit->installments as $keyInstallment => $installment) {

                            $sumCapital += $installment->capital;
                            $sumInterest += $installment->interest;

                            $abonoCapital += $installment->capital_payment;
                            $abonoInterest += $installment->interest_payment;

                            $capitalDebt = $installment->capitalDebt();
                            $interestDebt = $installment->interestDebt();

                            $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                            $sumPenalty += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
                            
                            if ((($capitalDebt + $interestDebt) > 0) && $installment->expiration_at < date('Y-m-d')) {
                                $cuotasAtrasados++;
                            }

                            if ((($capitalDebt + $interestDebt) === 0) && $installment->expiration_at > date('Y-m-d')) {
                                $cuotasAdelantadas++;
                            }
                        }

                        $lastInstallment = $credit->installments[count($credit->installments) - 1];

                        $color = "";
                        if ($lastInstallment->expiration_at < date('Y-m-d')) {
                            // color lila
                            $color = 'background: #E8DAEF; color: #5B2C6F; font-weight: 600;';
                        } else if ($cuotasAdelantadas == 0 && $cuotasAtrasados == 0) {
                            // color blanco
                            $color = 'font-weight: 600;';
                        } else if ($cuotasAtrasados > 0) {
                            // color rojo
                            $color = 'background: #FDEDEC; color: #CB4335; font-weight: 600;';
                        } else if ($cuotasAdelantadas > 0) {
                            // color verde
                            $color = 'background: #EAFAF1; color: #1E8449; font-weight: 600;';
                        }

                        ?>
                        <tr>
                            <td><?php echo str_pad($credit->idP, 5, '0', STR_PAD_LEFT) ?></td>
                            <td style="<?php echo $color ?>"><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                            <td><?php echo date('d/m/Y', strtotime($credit->fechaDesembolso)) ?></td>
                            <td><?php echo date('d/m/Y', strtotime($credit->installments->max('expiration_at'))) ?></td>
                            <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo round($credit->interest_rate, 2) . '%' ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumInterest / 10, 2) ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td style="text-align: center;"><?php echo $credit->number_installments ?></td>
                            <td style="text-align: right;"><?php echo number_format(($sumCapital + $sumInterest) / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo number_format($abonoCapital / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo number_format($abonoInterest / 10, 2) ?></td>
                            <td style="background: #E9F7EF; color: #229954; font-weight: 600; text-align: right;"><?php echo number_format(($sumCapital + $sumInterest - $abonoCapital - $abonoInterest) / 10, 2) ?></td>
                            <td style="background: #EBF5FB; color: #2874A6; font-weight: 600; text-align: right;"><?php echo number_format(($sumCapital - $abonoCapital) / 10, 2) ?></td>
                            <td style="background: #EBF5FB; color: #2874A6; font-weight: 600; text-align: right;"><?php echo number_format(($sumInterest - $abonoInterest) / 10, 2) ?></td>
                            <td style="<?php echo $color ?> text-align: center;">
                                <?php
                                if ($cuotasAtrasados > 0) {
                                    echo $cuotasAtrasados;
                                } else {
                                    if ($cuotasAdelantadas > 0) {
                                        echo '-';
                                    }
                                    echo $cuotasAdelantadas;
                                }
                                ?>
                            </td>
                            <td style="text-align: right;"><?php echo number_format($sumPenalty / 10, 2) ?></td>
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

        <div style="display: grid; grid-template-columns: repeat(6, minmax(150px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <label>N. CRÉDITOS</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo $totalCredits ?>">
            </div>
            <div>
                <label>TOTAL A COBRAR</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo number_format(($resumen->capital + $resumen->interest) / 10, 2) ?>">
            </div>
            <div>
                <label>CAPITAL COBRADO</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #239B56;" value="<?php echo number_format($resumen->capitalAbonado / 10, 2) ?>">
            </div>
            <div>
                <label>INTERES COBRADO</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #239B56;" value="<?php echo number_format($resumen->interesAbonado / 10, 2) ?>">
            </div>
            <div>
                <label>CAPITAL PENDIENTE</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #E74C3C;" value="<?php echo number_format(($resumen->capital - $resumen->capitalAbonado) / 10, 2) ?>">
            </div>
            <div>
                <label>PENDIENTE INTERES</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #E74C3C;" value="<?php echo number_format(($resumen->interest - $resumen->interesAbonado) / 10, 2) ?>">
            </div>
        </div>

    </div>
</div>

<script>
    function navigateToPage(page) {
        const query = new URLSearchParams(window.location.search);
        query.set('page', page);

        window.location.href = window.location.pathname + '?' + query.toString();
    }

    function mostrarTodo() {
        window.location.href = window.location.pathname;
    }
</script>

<?php include 'footer.php' ?>