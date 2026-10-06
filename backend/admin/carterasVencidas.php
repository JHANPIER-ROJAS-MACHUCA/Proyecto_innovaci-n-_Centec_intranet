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

$totalCredits = Credit::where('tprestamo.estado', 4)
    ->whereIn('idP', function ($query) use ($request) {
        $query->select('tprestamo.idP')
            ->from('tprestamo')
            ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
            ->where('tprestamo.estado', 4)
            ->groupBy('tprestamo.idP')
            ->when($request->startDate and $request->endDate, function ($query) use ($request) {
                $query->havingRaw("max(tpresta_detalle.expiration_at) between '$request->startDate' and '$request->endDate'");
            }, function ($query) {
                $query->havingRaw('max(tpresta_detalle.expiration_at) < date(now())');
            });
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tclie_general.idCG', 'tprestamo.idCG')
            ->where(function ($query) use ($request) {
                $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
            });
    })
    ->count();

$credits = Credit::with('installments', 'condoneDates')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->select(
        'tprestamo.idP',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tprestamo.capital',
        'tprestamo.payment_period',
        'tprestamo.interest_rate',
        'tprestamo.penalty',
        'tprestamo.number_installments'
    )
    ->selectRaw('min(tpresta_detalle.expiration_at) as i')
    ->selectRaw('max(tpresta_detalle.expiration_at) as f')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->where('tprestamo.estado', 4)
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        });
    })
    ->groupBy('tprestamo.idP')
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->havingRaw("max(tpresta_detalle.expiration_at) between '$request->startDate' and '$request->endDate'");
    }, function ($query) {
        $query->havingRaw('max(tpresta_detalle.expiration_at) < date(now())');
    })
    ->orderByRaw('max(tpresta_detalle.expiration_at) desc')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

// todo los creditos para el resumen
$creditForResume = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 4)
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        });
    })
    ->groupBy('tprestamo.idP')
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
            ->havingRaw("max(tpresta_detalle.expiration_at) between '$request->startDate' and '$request->endDate'");
    }, function ($query) {
        $query->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
            ->havingRaw('max(tpresta_detalle.expiration_at) < date(now())');
    })
    ->select('tprestamo.*')
    ->get();

// $resumen = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
//     ->whereIn('tpresta_detalle.idP', function ($query) use ($request) {
//         $query->select('tprestamo.idP')
//             ->from('tprestamo')
//             ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
//             ->groupBy('tprestamo.idP')
//             ->when($request->startDate and $request->endDate, function ($query) use ($request) {
//                 $query->havingRaw("max(tpresta_detalle.fechaProg) between '$request->startDate' and '$request->endDate'");
//             }, function ($query) {
//                 $query->havingRaw('max(tpresta_detalle.fechaProg) < date(now())');
//             });
//     })
//     ->where('tprestamo.estado', 4)
//     ->when($request->employee, function ($query) use ($request) {
//         $query->where('tprestamo.user_id', $request->employee);
//     })
//     ->when($request->customer, function ($query) use ($request) {
//         $query->join('tclie_general', 'tclie_general.idCG', 'tprestamo.idCG')
//             ->where(function ($query) use ($request) {
//                 $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
//             });
//     })
//     ->selectRaw('sum(tpresta_detalle.cuota) as capital')
//     ->selectRaw('sum(tpresta_detalle.interest) as interest')
//     ->selectRaw('sum(case when tpresta_detalle.estado = 1 then tpresta_detalle.cuota when tpresta_detalle.estado = 2 then tpresta_detalle.montoPagado else 0 end) AS abonoCapital')
//     ->selectRaw('sum(if(tpresta_detalle.estado=2,tpresta_detalle.interest,0)) as abonoInteres')
//     ->selectRaw('if(tpresta_detalle.estado_mora = 1, tpresta_detalle.pagoMora, 0) as abonoMora')
//     ->first();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Carteras vencidas <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
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
                            <option <?php echo $user->idU == $request->employee ? 'selected' : '' ?> value="<?php echo $user->idU ?>">
                                <?php echo $user->apU . ' ' . $user->amU ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div style="margin-right: 20px;">
                    <label>Cliente</label>
                    <input type="text" name="customer" value="<?php echo $request->customer ?>" class="form-control">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Buscar</button>
                    <button type="button" class="btn btn-success" onclick="mostrarTodo()">Mostrar Todos</button class="form-control">
                    <button type="submit" formaction="./../app/exel/carterasVencidas.php" formmethod="post" class="btn btn-warning">EXPORTAR A EXCEL</button>
                </div>
            </div>
        </form>

        <table class="table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Codigo crédito</th>
                    <th>Fecha inicio</th>
                    <th>Fecha final</th>
                    <th>Forma pago</th>
                    <th>Prestamo</th>
                    <th>Interes</th>
                    <th>Valor cuota</th>
                    <th>Total deuda</th>
                    <th>Total pagado</th>
                    <th>Abono mora</th>
                    <th>Saldo de deuda pendiente</th>
                    <th>Saldo mora</th>
                    <th>Total deuda</th>
                    <th>Cuotas retrasadas</th>
                    <th>Dias vencidos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($credits as $keyCredit => $credit) { ?>

                    <?php
                    $delay->payment_period = $credit->payment_period;
                    $delay->penalty = $credit->penalty;
                    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

                    $diasAtrasados = 0;
                    $cuotasRetrazadas = 0;
                    $abonoMora = 0;
                    $saldoMora = 0;

                    $sumCapitalDebt = 0;
                    $sumInterestDebt = 0;
                    $sumPenaltyDebt = 0;

                    $capitalPagado = 0;
                    $interesPagado = 0;

                    $sumCapitalPayment = 0;
                    $sumInterestPayment = 0;
                    $sumPenaltyPayment = 0;

                    foreach ($credit->installments as $key => $installment) {
                        $capitalDebt = $installment->capitalDebt();
                        $interestDebt = $installment->interestDebt();

                        $sumCapitalDebt += $capitalDebt;
                        $sumInterestDebt += $interestDebt;

                        $nextInstallment = $credit->installments[$key + 1] ?? null;
                        $penalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
                        $sumPenaltyDebt += $penalty;

                        $capitalPayment = $installment->capital_payment;
                        $interestPayment = $installment->interest_payment;

                        $sumCapitalPayment += $capitalPayment;
                        $sumInterestPayment += $interestPayment;
                        $sumPenaltyPayment += $installment->delay_payment;

                        if (($capitalDebt + $interestDebt) > 0) {
                            $cuotasRetrazadas++;
                        }
                    }

                    $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;

                    $diasAtrasados = 0;
                    if ($lastInstallment) {
                        $ini = new DateTime($lastInstallment->expiration_at);
                        $fin = new DateTime(date('Y-m-d'));
                        $diff = $ini->diff($fin);
                        $diasAtrasados = $diff->days;
                    }

                    ?>
                    <tr>
                        <td><?php echo $credit->idP ?></td>
                        <td style="background: #F5EEF8;color: #8E44AD; font-weight: 600;"><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                        <td><?php echo date('d/m/Y', strtotime($credit->i)) ?></td>
                        <td><?php echo date('d/m/Y', strtotime($credit->f)) ?></td>
                        <td><?php echo $credit->paymentPeriodToString() ?></td>
                        <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format($credit->interest / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format((($credit->capital + $credit->interest) / $credit->number_installments) / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format(($credit->capital + $credit->interest) / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format(($sumCapitalPayment + $sumInterestPayment) / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format($sumPenaltyPayment / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format(($sumCapitalDebt + $sumInterestDebt) / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format($sumPenaltyDebt / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format(($sumCapitalDebt + $sumInterestDebt + $sumPenaltyDebt) / 10, 2) ?></td>
                        <td style="text-align: right;"><?php echo $cuotasRetrazadas ?></td>
                        <td style="text-align: right;"><?php echo $diasAtrasados ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div>
            <?php for ($i = 1; $i <= ceil($totalCredits / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                    <?php echo $i ?>
                </button>
            <?php } ?>
        </div>


        <?php
        $totalPrestamo = 0;

        $capital = 0;
        $interes = 0;
        $abonoCapital = 0;
        $abonoInteres = 0;
        $resumeSaldoMora = 0;

        $sumCapitalDebt = 0;
        $sumInterestDebt = 0;

        foreach ($creditForResume as $keyCredit => $credit) {
            $delay->payment_period = $credit->payment_period;
            $delay->penalty = $credit->penalty;
            $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
            
            $totalPrestamo++;

            foreach ($credit->installments as $keyInstallment => $installment) {
                $capital += $installment->capital;
                $interes += $installment->interest;

                $abonoCapital += $installment->capital_payment;
                $abonoInteres += $installment->interest_payment;

                $sumCapitalDebt += $installment->capitalDebt();
                $sumInterestDebt += $installment->interestDebt(); 

                $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                $resumeSaldoMora += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
            }
        }
        ?>

        <div style="display: grid; grid-template-columns: repeat(7, minmax(150px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <label>N. CRÉDITOS</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo $totalPrestamo ?>">
            </div>
            <div>
                <!-- Total deuda sin mora -->
                <label>TOTAL A COBRAR</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #1565C0;" value="<?php echo number_format(($sumCapitalDebt + $sumInterestDebt + $resumeSaldoMora) / 10, 2) ?>">
            </div>
            <div>
                <label>CAPITAL COBRADO</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #28B463;" value="<?php echo number_format($abonoCapital / 10, 2) ?>">
            </div>
            <div>
                <label>INTERES COBRADO</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #28B463;" value="<?php echo number_format($abonoInteres / 10, 2) ?>">
            </div>
            <div>
                <label>PENDIENTE CAPITAL</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #E74C3C;" value="<?php echo number_format($sumCapitalDebt / 10, 2) ?>">
            </div>
            <div>
                <label>PENDIENTE INTERES</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #E74C3C;" value="<?php echo number_format($sumInterestDebt / 10, 2) ?>">
            </div>
            <div>
                <label>DEUDA MORA</label>
                <input type="text" class="form-control" style="font-weight: bold; color: #2C3E50;" value="<?php echo number_format($resumeSaldoMora / 10, 2) ?>">
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