<?php

use CrediSoporte\Domain\Helpers\Holiday;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Models\User;

include 'head.php';

$mesEnEspanol = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$monthsInArray = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

$numbers = $request->size === 'month' ? 30 : 12;


$endDate = date('Y-m-d');
$startDate = date('Y-m', strtotime($endDate . '-11 month')) . '-01';
// $endDate = '2022-12-10';
// $startDate = '2022-10-10';

$transactions = Transaction::select('idCAD')
    ->selectRaw('date(tcaja_usu_detal.created_at) as created_at')
    ->selectRaw('sum(tcaja_usu_detal.total) as total')
    // ->selectRaw('sum(cuota) as cuota')
    ->selectRaw('sum(tcaja_usu_detal.capital) as capital')
    ->selectRaw('sum(tcaja_usu_detal.interest) as interest')
    ->selectRaw('sum(tcaja_usu_detal.mora) as penalty')
    ->where('tcaja_usu_detal.tipo', 3)
    ->where('tcaja_usu_detal.estadodt', 2)
    ->whereRaw("date(tcaja_usu_detal.created_at) between '$startDate' and '$endDate'")
    ->when($request->size === 'month', function ($query) {
        $query->groupByRaw('date(tcaja_usu_detal.created_at)');
    }, function ($query) {
        $query->groupByRaw('year(tcaja_usu_detal.created_at), month(tcaja_usu_detal.created_at)');
    })
    ->when(in_array($request->user()->tipoU, ['3', '4', '5']), function ($query) use ($request) {
        $query->join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
            ->where('tcaja_usuario.idU', $request->user()->idU);
    })
    ->orderBy('tcaja_usu_detal.created_at', 'desc')
    ->get();

$credits = Credit::select('fechaDesembolso')
    ->selectRaw('sum(capital) as total')
    ->whereBetween('fechaDesembolso', [$startDate, $endDate])
    ->whereIn('estado', [4, 5])
    ->when($request->size === 'month', function ($query) {
        $query->groupBy('fechaDesembolso');
    }, function ($query) {
        $query->groupByRaw('year(fechaDesembolso), month(fechaDesembolso)');
    })
    ->when(in_array($request->user()->tipoU, ['3', '4', '5']), function ($query) use ($request) {
        $query->where('user_id', $request->user()->idU);
    })
    ->orderByRaw('fechaDesembolso desc')
    ->get();

$cuotas = Installment::join('tprestamo', 'tpresta_detalle.idP', 'tprestamo.idP')
    ->whereBetween('tpresta_detalle.expiration_at', [$startDate, $endDate])
    ->whereIn('tprestamo.estado', [4, 5])
    ->when($request->size === 'month', function ($query) {
        $query->groupByRaw('tpresta_detalle.expiration_at');
    }, function ($query) {
        $query->groupByRaw('year(tpresta_detalle.expiration_at), month(tpresta_detalle.expiration_at)');
    })
    ->when(in_array($request->user()->tipoU, ['3', '4', '5']), function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->user()->idU);
    })
    ->orderBy('tpresta_detalle.expiration_at', 'desc')
    ->select('tpresta_detalle.expiration_at')
    ->selectRaw('sum(tpresta_detalle.capital + tpresta_detalle.interest) as total')
    ->get();

$oneMonth = new DateInterval('P1M');
$oneDay = new DateInterval('P1D');

$capitalArray = [];
$interestArray = [];
$penaltyArray = [];
$trans = [];
$cred = [];
$cuot = [];

$ab = new DateTime('now');

$labels = [];
$desembolsos = [];
$cobros = [];
$cuotasProgramadas = [];

for ($i = 0; $i < $numbers; $i++) {
    $year = $ab->format('Y');
    $month = $ab->format('m');

    $result = 0;
    $capital = 0;
    $interest = 0;
    $penalty = 0;
    foreach ($transactions as $transaction) {
        $g = explode('-', $transaction->created_at);

        if ($request->size === 'month' && $transaction->created_at === $ab->format('Y-m-d')) {
            $result = $transaction->total;
            $capital = $transaction->capital;
            $interest = $transaction->interest;
            $penalty = $transaction->penalty;
            break;
        } else if ($request->size !== 'month' && $g[0] === $year && $g[1] === $month) {
            $result = $transaction->total;
            $capital = $transaction->capital;
            $interest = $transaction->interest;
            $penalty = $transaction->penalty;
            break;
        }
    }
    $trans[] = $result;
    $capitalArray[] = $capital;
    $interestArray[] = $interest;
    $penaltyArray[] = $penalty;


    $result = 0;
    foreach ($credits as $credit) {
        $g = explode('-', $credit->fechaDesembolso);

        if ($request->size === 'month' && $credit->fechaDesembolso === $ab->format('Y-m-d')) {
            $result = $credit->total / 10;
            break;
        } else if ($request->size !== 'month' && $g[0] === $year && $g[1] === $month) {
            $result = $credit->total / 10;
            break;
        }
    }
    $cred[] = $result;

    $result = 0;
    foreach ($cuotas as $cuota) {
        $g = explode('-', $cuota->expiration_at);

        if ($request->size === 'month' && $cuota->expiration_at === $ab->format('Y-m-d')) {
            $result = $cuota->total / 10;
            break;
        } else if ($request->size !== 'month' && $g[0] === $year && $g[1] === $month) {
            $result = $cuota->total / 10;
            break;
        }
    }
    $cuot[] = $result;


    if ($request->size === 'month') {
        $labels[] = $ab->format('d/m');
    } else {
        $labels[] = $monthsInArray[$ab->format('n') - 1];
    }

    $ab->sub($request->size === 'month' ? $oneDay : $oneMonth);
}

$now = date('Y-m-d');

$goals = Goal::whereRaw("'$now' between start_at and end_at")->get();
$goal = $goals->where('user_id', $request->user()->idU)->first();

$goalSummary = []; // total capital desembolsado, operaciones, total desembolso hoy, total operaciones hoy. Agrupados por usuario.
$workDays = 0; // numero de dias laborables

if (count($goals) > 0) {
    $dateGoal = date('Y-m-t'); // año - mes - numero de dias que contiene
    $dateGoalArray = explode('-', $dateGoal);

    $goalSummary = Credit::whereIn('estado', [4, 5])
        ->whereBetween('fechaDesembolso', [$dateGoalArray[0] . '-' . $dateGoalArray[1] . '-' . '1', $dateGoal])
        ->whereIn('user_id', $goals->pluck('user_id'))
        ->select('tprestamo.user_id')
        ->selectRaw('sum(capital) as saldo')
        ->selectRaw('count(idP) as operation')
        ->selectRaw("sum(if(fechaDesembolso = '$now',capital,0)) as saldoNow")
        ->selectRaw("count(if(fechaDesembolso = '$now',idP,null)) as operationNow")
        ->groupBy('tprestamo.user_id')
        ->get();
}

if (!empty($goal)) {
    $goalResumen = (object) [
        'saldo' => 0,
        'operation' => 0,
        'saldoNow' => 0,
        'operationNow' => 0
    ];

    foreach ($goalSummary as $summary) {
        if ($summary->user_id === $goal->user_id) {
            $goalResumen->saldo += $summary->saldo;
            $goalResumen->operation += $summary->operation;
            $goalResumen->saldoNow += $summary->saldoNow;
            $goalResumen->operationNow += $summary->operationNow;
        }
    }

    // $goalResumen = Credit::selectRaw('sum(capital) as saldo')
    //     ->selectRaw('count(idP) as operation')
    //     ->selectRaw("sum(if(fechaDesembolso = '$now',capital,0)) as saldoNow")
    //     ->selectRaw("count(if(fechaDesembolso = '$now',idP,null)) as operationNow")
    //     ->whereIn('estado', [4, 5])
    //     ->where('user_id', $request->user()->idU)
    //     ->whereBetween('fechaDesembolso', [$goal->start_at, $goal->end_at])
    //     ->first();

    // $workDays = Holiday::numberWorkingDaysRemainingBetween($goal->start_at, $goal->end_at);
}

if (count($goals) > 0 && !empty($goal)) {
    $workDays = Holiday::numberWorkingDaysRemainingBetween($goal->start_at, $goal->end_at);
}

/*
cumpleaños
*/
$birthDateTime = new DateTime();
$birthStart = $birthDateTime->format('Y-m-d');
$birthDateTime->add(new DateInterval('P5D'));
$birthEnd = $birthDateTime->format('Y-m-d');

$usersBirthdate = User::active()
    ->whereBetween('birthdate', [$birthStart, $birthEnd])
    ->orderBy('birthdate')
    ->get();

?>

<div style="background: white; padding: 24px 16px; border-radius: 10px;">
    <h3>Hola, bienvenido de nuevo.</h3>
    <?php
    $userLoginBirthdate = $usersBirthdate->where('idU', $request->user()->idU)
        ->where('birthdate', $birthStart)
        ->first();
    ?>

    <?php if ($userLoginBirthdate) { ?>
        <div style="display: flex; font-size: 14px;">
            <i class="fa fa-birthday-cake" aria-hidden="true" style="margin-right: 10px; padding-top: 2px;"></i>
            <div>Feliz cumpleaños <b><?php echo $userLoginBirthdate->nomU ?></b></div>
        </div>
    <?php } ?>
</div>

<!-- Meta oficina -->
<?php if (count($goals) > 0 && in_array($request->user()->tipoU, ['1', '2'])) { ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap: 30px; margin-top: 30px;">
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta oficina desembolso · <?php echo $mesEnEspanol[date('m') - 1] ?></h4>
            <?php $percentage = ($goalSummary->sum('saldo') / 10) / $goals->sum('saldo') * 100 ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;">S/ <?php echo number_format($goalSummary->sum('saldo') / 10, 2) ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage > 100 ? 100 : $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div>S/ <?php echo number_format($goals->sum('saldo'), 2) ?></div>
                </div>
            </div>
        </div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta oficina desembolso · Hoy</h4>
            <?php
            $goalNow = $workDays === 0 ? 0 : ($goals->sum('saldo') - (($goalSummary->sum('saldo') - $goalSummary->sum('saldoNow')) / 10)) / $workDays;
            $percentage = $goalNow > 0 ? ($goalSummary->sum('saldoNow') / 10) / $goalNow * 100 : 0;
            ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;">S/ <?php echo number_format($goalSummary->sum('saldoNow') / 10, 2) ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div>S/ <?php echo number_format($goalNow, 2) ?></div>
                </div>
            </div>
        </div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta oficina operaciones · <?php echo $mesEnEspanol[date('m') - 1] ?></h4>
            <?php $percentage = $goalSummary->sum('operation') / $goals->sum('operation') * 100 ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;"><?php echo $goalSummary->sum('operation') ?? 0 ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div><?php echo $goals->sum('operation') ?></div>
                </div>
            </div>
        </div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta oficina operaciones · Hoy</h4>
            <?php
            $goalNow = $workDays === 0 ? 0 : ($goals->sum('operation') - ($goalSummary->sum('operation') - $goalSummary->sum('operationNow'))) / $workDays;
            $percentage = $goalNow > 0 ? $goalSummary->sum('operationNow') / $goalNow * 100 : 0;
            ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;"><?php echo $goalSummary->sum('operationNow') ?? 0 ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div><?php echo number_format($goalNow) ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- Meta por usuario -->
<?php if (!empty($goal)) { ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap: 30px; margin-top: 30px;">
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta desembolso · <?php echo $mesEnEspanol[date('m') - 1] ?></h4>
            <?php $percentage = ($goalResumen->saldo / 10) / $goal->saldo * 100 ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;">S/ <?php echo number_format($goalResumen->saldo / 10, 2) ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage > 100 ? 100 : $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div>S/ <?php echo number_format($goal->saldo, 2) ?></div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta desembolso · Hoy</h4>
            <?php
            $goalNow = $workDays === 0 ? 0 : ($goal->saldo - (($goalResumen->saldo - $goalResumen->saldoNow) / 10)) / $workDays;
            $percentage = ($goalResumen->saldoNow / 10) / $goalNow * 100;
            ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;">S/ <?php echo number_format($goalResumen->saldoNow / 10, 2) ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div>S/ <?php echo number_format($goalNow, 2) ?></div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta operaciones · <?php echo $mesEnEspanol[date('m') - 1] ?></h4>
            <?php $percentage = $goalResumen->operation / $goal->operation * 100 ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;"><?php echo $goalResumen->operation ?? 0 ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div><?php echo $goal->operation ?></div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Meta operaciones · Hoy</h4>
            <?php
            $goalNow = $workDays === 0 ? 0 : ($goal->operation - ($goalResumen->operation - $goalResumen->operationNow)) / $workDays;
            $percentage = $goalResumen->operationNow / $goalNow * 100;
            ?>
            <div style="margin-top: 30px;">
                <div style="text-align: center; font-size: 2em; font-weight: 600;"><?php echo $goalResumen->operationNow ?? 0 ?></div>
                <div style="position: relative; height: 20px; background: #EAECEE; display: flex; justify-content: center; align-items: center; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                    <div style="position: absolute;  top: 0; left: 0; bottom: 0; width: <?php echo $percentage ?>%; background: #52BE80; border-radius: 10px;"></div>
                    <span style="font-weight: 700; position: relative;"><?php echo number_format($percentage, 2) ?>%</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>0</div>
                    <div><?php echo number_format($goalNow) ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 30px;margin-top: 30px; margin-bottom: 30px;">
    <div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <h4>Desembolsos</h4>
                <div>
                    <a class="btn btn-<?php echo $request->size === 'month' ? 'default' : 'primary' ?>" href="<?php echo $_SERVER['PHP_SELF'] ?>">Año</a>
                    <a class="btn btn-<?php echo $request->size === 'month' ? 'primary' : 'default' ?>" href="<?php echo $_SERVER['PHP_SELF'] ?>?size=month">Mes</a>
                </div>
            </div>
            <canvas id="chartDesembolsos"></canvas>
        </div>
    </div>
    <div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <h4>Rentabilidad</h4>
                <div>
                    <a class="btn btn-<?php echo $request->size === 'month' ? 'default' : 'primary' ?>" href="<?php echo $_SERVER['PHP_SELF'] ?>">Año</a>
                    <a class="btn btn-<?php echo $request->size === 'month' ? 'primary' : 'default' ?>" href="<?php echo $_SERVER['PHP_SELF'] ?>?size=month">Mes</a>
                </div>
            </div>
            <canvas id="chartCostEffectiveness"></canvas>
        </div>
    </div>

    <?php if (
        ($request->user()->tipoU === '1' || $request->user()->tipoU === '2') &&
        count($usersBirthdate) > 0
    ) { ?>
        <div>
            <div style="background: white; padding: 24px 16px; border-radius: 10px;">
                <h4>Cumpleaños</h4>

                <div style="line-height: 2rem;">
                    <?php foreach ($usersBirthdate as $userBirthdate) { ?>
                        <?php
                        $start = new DateTime(date('Y-m-d'));
                        $end = new DateTime($userBirthdate->birthdate);
                        $days = $start->diff($end)->days;
                        ?>

                        <?php if ($days === 0) { ?>
                            <div style="display: flex; align-items: start;">
                                <i class="fa fa-birthday-cake" aria-hidden="true" style="margin-right: 10px; padding-top: 4px;"></i>
                                <div>
                                    Hoy es el cumpleaños de <b><?php echo "$userBirthdate->nomU $userBirthdate->apU" ?></b>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div style="display: flex; align-items: start;">
                                <i class="fa fa-clock-o" aria-hidden="true" style="margin-right: 10px; padding-top: 4px;"></i>
                                <span>
                                    Faltan <?php echo $days ?> <?php echo $days > 1 ? 'dias' : 'dia' ?> para el cumpleaños de
                                    <b><?php echo "$userBirthdate->nomU $userBirthdate->apU" ?></b>
                                </span>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- <div style="background: white; padding: 20px 16px; border-radius: 10px;">
        <h4>Desembolsos por usuarios</h4>
        <canvas id="chartDesembolsosPorUsuarios"></canvas>
    </div> -->
</div>

<div style="display: none;margin-top: 20px;">
    <div style="flex: 1 1 auto; display: grid; grid-template-columns: repeat(4,minmax(100px, 1fr)); gap: 10px;">
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Clientes</h4>
            <div style="font-size: 30px; font-weight: bold;">25</div>
        </div>
        <div style="background: white; padding: 24px 16px; border-radius: 10px;">
            <h4>Clientes</h4>
            <div style="font-size: 30px; font-weight: bold;">25</div>
        </div>
    </div>
    <div style="flex: none; width: 350px;">
        <div style="background-color: white; border-radius: 10px;padding: 20px 25px;">
            <h4>Caja</h4>
            <?php if ($cash) { ?>
                <div>
                    <span style=" font-size: 40px; margin-right: 10px;">S/</span>
                    <span style=" font-size: 40px;"><?php echo number_format($saldo, 2) ?></span>
                </div>
            <?php } else { ?>
                <div style="font-size: 25px; color: #E33636; font-weight: bold;">Cerrado</div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

// $usersToChart = User::active()->get();
// $desembolsosPorUsuario = Credit::join('tusuario', 'tusuario.idU', 'tprestamo.user_id')
//     ->select('tprestamo.fechaDesembolso')
//     ->selectRaw('sum(tprestamo.montoAprovado) as total')
//     ->whereBetween('tprestamo.fechaDesembolso', [$startDate, $endDate])
//     ->whereIn('tprestamo.estado', [4, 5])
//     ->whereIn('tusuario.idU', $usersToChart->pluck('idU'))
//     ->groupByRaw('year(tprestamo.fechaDesembolso), month(tprestamo.fechaDesembolso), tprestamo.user_id')
//     ->get();

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    const labels = <?php echo json_encode(array_reverse($labels)); ?>;
    const data = {
        labels: labels,
        datasets: [{
            label: 'Desembolso',
            data: <?php echo json_encode(array_reverse($cred)) ?>,
            backgroundColor: ['rgba(255, 99, 132, 0.2)'],
            borderColor: ['rgb(255, 99, 132)'],
            borderWidth: 1.5
        }, {
            label: 'Cobros',
            data: <?php echo json_encode(array_reverse($trans)) ?>,
            backgroundColor: ['rgba(54, 162, 235, 0.2)'],
            borderColor: ['rgb(54, 162, 235)'],
            borderWidth: 1.5
        }, {
            label: 'Cuotas programadas',
            data: <?php echo json_encode(array_reverse($cuot)) ?>,
            backgroundColor: ['rgba(255, 205, 86, 0.2)'],
            borderColor: ['rgb(255, 205, 86)'],
            borderWidth: 1.5
        }]
    };

    const dataRentabilidad = {
        labels: labels,
        datasets: [{
            label: 'Capital cobrado',
            data: <?php echo json_encode(array_reverse($capitalArray)) ?>,
            borderWidth: 1.5
        }, {
            label: 'Interes cobrado',
            data: <?php echo json_encode(array_reverse($interestArray)) ?>,
            borderWidth: 1.5
        }, {
            label: 'Mora cobrado',
            data: <?php echo json_encode(array_reverse($penaltyArray)) ?>,
            borderWidth: 1.5
        }]
    }

    const config = {
        type: 'line',
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const config2 = {
        type: 'line',
        data: dataRentabilidad,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const myChart = new Chart(
        document.getElementById('chartDesembolsos'),
        config
    );

    const costEffectiveness = new Chart(
        document.getElementById('chartCostEffectiveness'),
        config2
    )
</script>

<?php
include 'footer.php';
?>