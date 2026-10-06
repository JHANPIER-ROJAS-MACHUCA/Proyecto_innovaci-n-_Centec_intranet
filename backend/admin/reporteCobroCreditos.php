<?php

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Models\User;

require 'head.php';

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$startDate = $request->get('startDate', date('Y-m-d', strtotime(date('Y-m-d') . '- 1 month')));
$endDate = $request->get('endDate', date('Y-m-d'));

$credits = Credit::with('installments', 'condoneDates', 'customer')
    ->join('tcaja_usu_detal', 'tprestamo.idP', 'tcaja_usu_detal.conejo')
    ->whereRaw("date(tcaja_usu_detal.created_at) between '$startDate' and '$endDate'")
    ->where('tcaja_usu_detal.estadodt', '2') // activo
    ->where('tcaja_usu_detal.tipo', '3') // cobro
    ->when($request->portfolio, function ($query) use ($request) {
        $query->where('tcaja_usu_detal.portfolio_id', $request->portfolio);
    })
    ->groupBy('tprestamo.idP')
    ->orderBy('last_transaction_id', 'desc')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->select('tprestamo.*')
    ->selectRaw('sum(tcaja_usu_detal.capital) as capital_payment')
    ->selectRaw('sum(tcaja_usu_detal.interest) as interest_payment')
    ->selectRaw('sum(tcaja_usu_detal.mora) as penalty_payment')
    ->selectRaw('max(tcaja_usu_detal.idCAD) as last_transaction_id')
    ->get();

$summary = Credit::join('tcaja_usu_detal', 'tprestamo.idP', 'tcaja_usu_detal.conejo')
    ->whereRaw("date(tcaja_usu_detal.created_at) between '$startDate' and '$endDate'")
    ->where('tcaja_usu_detal.estadodt', '2') // activo
    ->where('tcaja_usu_detal.tipo', '3') // cobro
    ->when($request->portfolio, function ($query) use ($request) {
        $query->where('tcaja_usu_detal.portfolio_id', $request->portfolio);
    })
    ->selectRaw('sum(tcaja_usu_detal.capital) as capital_payment')
    ->selectRaw('sum(tcaja_usu_detal.interest) as interest_payment')
    ->selectRaw('sum(tcaja_usu_detal.mora) as penalty_payment')
    ->selectRaw('count(distinct tprestamo.idP) as total')
    ->first();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Reporte cobro creditos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>Cobro de:</label>
                    <input type="date" class="form-control" name="startDate" value="<?php echo $startDate ?>">
                </div>
                <div style="margin-right: 20px;">
                    <label>Cobro hasta</label>
                    <input type="date" class="form-control" name="endDate" value="<?php echo $endDate ?>">
                </div>
                <div style="margin-right: 20px;">
                    <label>Cartera</label>
                    <select name="portfolio" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach (User::where('estadoU', '1')->get() as $key => $user) { ?>
                            <option value="<?php echo $user->idU ?>" <?php echo $request->portfolio === (string) $user->idU ? 'selected' : '' ?>>
                                <?php echo $user->monU . ' ' . $user->apU ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">APLICAR FILTRO</button>
                </div>
            </div>
        </form>

        <div style="margin-top: 30px; overflow-x: auto;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Prestamo</th>
                        <th>Tasa</th>
                        <th>Número crédito</th>
                        <th>Periodo de pago</th>
                        <th>Capital cobrado</th>
                        <th>Interes cobrado</th>
                        <th>Mora cobrado</th>
                        <th>Saldo capital</th>
                        <th>Saldo interes</th>
                        <th>Saldo mora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $delay = new Delay;
                    $delay->setHolidays(Holiday::pluck('date'));
                    ?>

                    <?php foreach ($credits as $key => $credit) { ?>

                        <?php
                        $delay->penalty = $credit->penalty;
                        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
                        $delay->payment_period = $credit->payment_period;

                        $sumCapitalDebt = 0;
                        $sumInterestDebt = 0;
                        $sumPenaltyDebt = 0;

                        foreach ($credit->installments as $keyInstallment => $installment) {
                            $sumCapitalDebt += $installment->capitalDebt();
                            $sumInterestDebt += $installment->interestDebt();

                            $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
                            $sumPenaltyDebt += $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
                        }
                        ?>

                        <tr>
                            <td><?php echo $credit->idP ?></td>
                            <td><?php echo $credit->customer->ap . " " . $credit->customer->am . " " . $credit->customer->nom ?></td>
                            <td><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td><?php echo round($credit->interest_rate, 2) ?></td>
                            <td><?php echo $credit->n_credito ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td><?php echo number_format($credit->capital_payment, 2) ?></td>
                            <td><?php echo number_format($credit->interest_payment, 2) ?></td>
                            <td><?php echo number_format($credit->penalty_payment, 2) ?></td>
                            <td><?php echo number_format($sumCapitalDebt / 10, 2) ?></td>
                            <td><?php echo number_format($sumInterestDebt / 10, 2) ?></td>
                            <td><?php echo number_format($sumPenaltyDebt / 10, 2) ?></td>
                            <td>
                                <?php if ($credit->estado === '4') { ?>
                                    <span style="color: blue; font-weight: 600;">ACTIVO</span>
                                <?php } else if ($credit->estado === '5') { ?>
                                    <span style="color: green; font-weight: 600;">CANCELADO</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php for ($i = 1; $i <= ceil($summary->total / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                    <?php echo $i ?>
                </button>
            <?php } ?>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <label>Capital cobrado</label>
                <input type="text" class="form-control" value="<?php echo $summary->capital_payment ?>" style="font-weight: 600; color: #1565C0;" />
            </div>
            <div>
                <label>Interes cobrado</label>
                <input type="text" class="form-control" value="<?php echo $summary->interest_payment ?>" style="font-weight: 600; color: #1565C0;" />
            </div>
            <div>
                <label>Mora cobrado</label>
                <input type="text" class="form-control" value="<?php echo $summary->penalty_payment ?>" style="font-weight: 600; color: #1565C0;" />
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

<?php require 'footer.php'; ?>