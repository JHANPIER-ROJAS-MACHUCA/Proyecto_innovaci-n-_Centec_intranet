<?php

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Models\User;

include "head.php";

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

$credits = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->whereIn('estado', [4, 5])
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->orderBy('tprestamo.idP', 'desc')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

$completedCreditResume = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->where('tprestamo.estado', 5)
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->select('tprestamo.*')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.delay_payment) as mora')
    ->selectRaw('count(distinct tprestamo.idP) as total')
    ->first();

$activeCredits = Credit::with('installments')
    ->where('tprestamo.estado', 4)
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->select('tprestamo.*')
    ->get();

// $total2 = Credit::whereIn('tprestamo.estado', [4, 5])
//     ->when($request->startDate && $request->endDate, function ($query) use ($request) {
//         $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
//     })
//     ->when($request->employee, function ($query) use ($request) {
//         $query->where('tprestamo.user_id', $request->employee);
//     })
//     ->when($request->customer, function ($query) {
//         $query->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
//             ->where("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$query->customer%'");
//     })
//     ->count();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Créditos activos y finalizados <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form>
            <div style="display: flex; align-items: flex-end;">
                <div style="margin-right: 20px;">
                    <label>Desembolso De</label>
                    <input type="date" name="startDate" class="form-control" value="<?php echo $request->startDate ?>" />
                </div>
                <div style="margin-right: 20px;">
                    <label>Desembolso Hasta</label>
                    <input type="date" name="endDate" class="form-control" value="<?php echo $request->endDate ?>" />
                </div>
                <div style="margin-right: 20px;">
                    <label>Funcionario</label>
                    <select name="employee" class="form-control">
                        <option value="">Seleccione</option>
                        <?php foreach (User::where('estadoU', 1)->whereIn('tipoU', [3, 4])->get() as $key => $user) { ?>
                            <option value="<?php echo $user->idU ?>" <?php echo (string) $request->employee === (string) $user->idU ? 'selected' : '' ?>>
                                <?php echo "$user->apU $user->amU $user->nomU" ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div style="margin-right: 20px;">
                    <label>Cliente</label>
                    <input type="text" name="customer" class="form-control" value="<?php echo $request->customer ?>" placeholder="Buscar por nombres y apellidos" />
                </div>
                <div style="margin-right: 15px;">
                    <button class="btn btn-success" type="submit">Buscar</button>
                </div>
                <div style="margin-right: 15px;">
                    <button class="btn btn-primary" type="button" onclick="window.location.href = window.location.pathname">Mostrar todo</button>
                </div>
                <div style="margin-right: 15px;">
                    <button type="submit" formaction="./../app/exel/creditosFinalizadosActivos.php" formmethod="post" class="btn btn-warning">EXPORTAR A EXCEL</button>
                </div>
            </div>
        </form>

        <table class="table table-striped" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>Código crédito</th>
                    <th>Cliente</th>
                    <th>Fecha desembolso</th>
                    <th>Fecha final</th>
                    <th>Prestamo</th>
                    <th>Tasa</th>
                    <th>Interes</th>
                    <th>Forma pago</th>
                    <th>N° cuotas</th>
                    <th>Total pagar</th>
                    <th>Abono capital</th>
                    <th>Abono interes</th>
                    <th>Saldo total</th>
                    <th>Saldo capital</th>
                    <th>Saldo interes</th>
                    <th>Total mora abonado</th>
                    <th>Fecha cancelado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($credits as $keyCredit => $credit) { ?>
                    <?php
                    $delay->penalty = $credit->penalty;
                    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

                    $interes = 0;

                    $sumCapitalPayment = 0;
                    $sumInterestPayment = 0;
                    $sumPenaltyPayment = 0;

                    $sumCapitalDebt = 0;
                    $sumInterestDebt = 0;
                    $sumPenaltyDebt = 0;

                    $abonoCapital = 0;
                    $abonoInteres = 0;
                    $abonoMora = 0;

                    foreach ($credit->installments as $keyInstallment => $installment) {
                        $interes += $installment->interest;

                        $sumCapitalPayment += $installment->capital_payment;
                        $sumInterestPayment += $installment->interest_payment;
                        $sumPenaltyPayment += $installment->delay_payment;


                        $sumCapitalDebt += $installment->capitalDebt();
                        $sumInterestDebt += $installment->interestDebt();
                        
                        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                        $sumCapitalDebt += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
                    }

                    $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;
                    ?>

                    <tr>
                        <td><?php echo $credit->idP ?></td>
                        <td><?php echo "$credit->ap $credit->am $credit->nom" ?></td>
                        <td><?php echo date('d/m/Y', strtotime($credit->fechaDesembolso)) ?></td>
                        <td style="white-space: nowrap;"><?php echo $lastInstallment->expiration_at ?></td>
                        <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                        <td><?php echo round($credit->interest_rate, 2) ?></td>
                        <td style="text-align: right;"><?php echo number_format($interes / 10, 2) ?></td>
                        <td><?php echo $credit->paymentPeriodToString() ?></td>
                        <td><?php echo $credit->number_installments ?></td>
                        <td><?php echo number_format(($credit->capital + $interes) / 10, 2) ?></td>
                        <td><?php echo number_format($sumCapitalPayment / 10, 2) ?></td>
                        <td><?php echo number_format($sumInterestPayment / 10, 2) ?></td>
                        <td><?php echo number_format(($sumCapitalDebt + $sumInterestDebt) / 10, 2) ?></td>
                        <td><?php echo number_format($sumCapitalDebt / 10, 2) ?></td>
                        <td><?php echo number_format($sumInterestDebt / 10, 2) ?></td>
                        <td><?php echo number_format($sumPenaltyPayment / 10, 2) ?></td>
                        <td><?php echo $retVal = ($credit->estado === '4') ? '' : $credit->finished_at; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
        $capital = 0;
        $interes = 0;
        $abonoCapital = 0;
        $abonoInteres = 0;
        $abonoMora = 0;
        $moraPendiente = 0;

        $total = $completedCreditResume->total;

        foreach ($activeCredits as $keyCredit => $credit) {
            $delay->penalty = $credit->penalty;
            $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

            $total++;

            $capital += $credit->capital;

            foreach ($credit->installments as $keyInstallment => $installment) {
                $interes += $installment->interest;

                $abonoCapital += $installment->capital_payment;
                $abonoInteres += $installment->interest_payment;
                $abonoMora += $installment->delay_payment;

                $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                $moraPendiente += $installment->debtMora($credit->mora, $nextInstallment);
            }

            $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;
            $moraPendiente += $credit->debtMora($lastInstallment);
        }
        ?>

        <div>
            <?php for ($i = 1; $i <= ceil($total / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                    <?php echo $i ?>
                </button>
            <?php } ?>
        </div>

        <div style="display: grid; grid-template-columns: repeat(7, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
            <div>
                <label>N° Credito</label>
                <input type="text" class="form-control" disabled value="<?php echo $total ?>" style="color: #273746; font-weight: bold;" />
            </div>
            <div>
                <label>Capital cobrado</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($completedCreditResume->capital + $abonoCapital, 2) ?>" style="color: #239B56; font-weight: bold;" />
            </div>
            <div>
                <label>Interes cobrado</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($completedCreditResume->interest + $abonoInteres, 2) ?>" style="color: #239B56; font-weight: bold;" />
            </div>
            <div>
                <label>Capital pendiente</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($capital - $abonoCapital, 2) ?>" style="color: #E74C3C; font-weight: bold;" />
            </div>
            <div>
                <label>Interes pendiente</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($interes - $abonoInteres, 2) ?>" style="color: #E74C3C; font-weight: bold;" />
            </div>
            <div>
                <label>Mora cobrado (Mínimo)</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($completedCreditResume->mora + $abonoMora, 2) ?>" style="color: #273746; font-weight: bold;" />
            </div>
            <div>
                <label>Mora pendiente</label>
                <input type="text" class="form-control" disabled value="<?php echo number_format($moraPendiente, 2) ?>" style="color: #273746; font-weight: bold;" />
            </div>
        </div>
    </div>

    <script>
        function navigateToPage(page) {
            const query = new URLSearchParams(window.location.search);
            query.set('page', page);

            window.location.href = window.location.pathname + '?' + query.toString();
        }
    </script>

    <?php include "footer.php" ?>