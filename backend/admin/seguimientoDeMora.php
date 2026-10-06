<?php

use CrediSoporte\Domain\Models\Credit;

include 'head.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// $credits = Credit::join('tusuario', 'tprestamo.user_id', 'tusuario.idU')
//     ->selectRaw('concat_ws(" ",tusuario.apU, tusuario.amU, tusuario.nomU) as funcionario')
//     ->selectRaw('sum(tprestamo.montoAprovado) as desembolso')
//     ->selectRaw('count(distinct tprestamo.idCG) as clientes')
//     ->selectRaw('count(tprestamo.idP) as operaciones')
//     ->whereBetween('tprestamo.fechaDesembolso', ['2022-10-01', '2022-10-31'])
//     ->whereIn('estado', [4, 5])
//     ->groupBy('tprestamo.user_id')
//     ->get();

$DT = new DateTime($request->month ?? date('Y-m'));

// $month = $request->month ?? date('Y-m');
// $startMonth = $moth . '-01'

$credits = Credit::with('installments')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->whereIn('tprestamo.estado', ['4', '5'])
    ->whereBetween('tprestamo.fechaDesembolso', [$DT->format('Y-m-d'), $DT->format('Y-m-t')])
    ->select('tprestamo.*', 'tclie_general.idU')
    ->selectRaw('concat_ws(" ", tusuario.apU, tusuario.amU, tusuario.nomU) as funcionario')
    ->get();

$usersItems = [];
foreach ($credits as $key => $credit) {
    $saldo = 0;
    $primeraCuotaAtrasada = null;
    foreach ($credit->installments as $keyInstallment => $installment) {
        $capitalDebt = $installment->capitalDebt();
        $interestDebt = $installment->interestDebt();

        if (
            ($capitalDebt + $interestDebt) > 0 &&
            $primeraCuotaAtrasada === null &&
            $installment->expiration_at < date('Y-m-d')
        ) {
            $primeraCuotaAtrasada = $installment;
        }

        $saldo += $capitalDebt;
    }

    $saldo1 = 0;
    $saldo2 = 0;
    $saldo3 = 0;

    if ($primeraCuotaAtrasada) {
        $ini = new DateTime($primeraCuotaAtrasada->expiration_at);
        $fin = new DateTime(date('Y-m-d'));
        $dias = $ini->diff($fin)->days;

        if ($dias > 30) {
            $saldo3 = $saldo;
            $saldo2 = $saldo;
            $saldo1 = $saldo;
        } else if ($dias > 8) {
            $saldo2 = $saldo;
            $saldo1 = $saldo;
        } else {
            $saldo1 = $saldo;
        }
    }

    $keyItem = array_search($credit->idU, array_column($usersItems, 'id'));
    if ($keyItem !== false) {
        $usersItems[$keyItem]['desembolso'] = ($usersItems[$keyItem]['desembolso'] ?? 0) + $credit->capital;

        $cli = $usersItems[$keyItem]['clientes'] ?? [];
        array_push($cli, $credit->idCG);
        $usersItems[$keyItem]['clientes'] = $cli;

        $usersItems[$keyItem]['saldo'] = ($usersItems[$keyItem]['saldo'] ?? 0) + $saldo;
        $usersItems[$keyItem]['saldo1'] = ($usersItems[$keyItem]['saldo1'] ?? 0) + $saldo1;
        $usersItems[$keyItem]['saldo2'] = ($usersItems[$keyItem]['saldo2'] ?? 0) + $saldo2;
        $usersItems[$keyItem]['saldo3'] = ($usersItems[$keyItem]['saldo3'] ?? 0) + $saldo3;
    } else {
        $usersItems[] = [
            'id' => $credit->idU,
            'funcionario' => $credit->funcionario,
            'desembolso' => $credit->capital,
            'clientes' => [$credit->idCG],
            'saldo' => $saldo,
            'saldo1' => $saldo1,
            'saldo2' => $saldo2,
            'saldo3' => $saldo3
        ];
    }
}

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Seguimiento de mora <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-1">Por mes</a></li>
                <li><a href="seguimientoDeMoraAll.php">Todos</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">

                        <h3>SEGUIMIENTO DE MORA</h3>

                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" style="margin-top: 20px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; align-items: end;">
                                <div>
                                    <label>Mes</label>
                                    <input type="month" name="month" class="form-control" value="<?php echo $DT->format('Y-m') ?>">
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filtar</button>
                                </div>
                            </div>
                        </form>

                        <div style="overflow-x: auto; margin-top: 30px;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Funcionario</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Saldo actual</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">N° clientes</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Desembolso</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Operaciones</th>
                                        <th colspan="2" style="text-align: center;">Mora > 1 día</th>
                                        <th colspan="2" style="text-align: center;">Mora > 8 día</th>
                                        <th colspan="2" style="text-align: center;">Mora > 30 día</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;">Monto</th>
                                        <th style="text-align: center;">Porcentaje</th>
                                        <th style="text-align: center;">Monto</th>
                                        <th style="text-align: center;">Porcentaje</th>
                                        <th style="text-align: center;">Monto</th>
                                        <th style="text-align: center;">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $agenciaSaldo = 0;
                                    $agenciaDesembolso = 0;
                                    $agenciaClientes = 0;
                                    $agenciaOperaciones = 0;
                                    $agenciaSaldo1 = 0;
                                    $agenciaSaldo2 = 0;
                                    $agenciaSaldo3 = 0;
                                    ?>
                                    <?php foreach ($usersItems as $key => $item) { ?>
                                        <?php
                                        $agenciaSaldo += $item['saldo'];
                                        $agenciaDesembolso += $item['desembolso'];
                                        $agenciaClientes += count(array_unique($item['clientes']));
                                        $agenciaOperaciones += count($item['clientes']);
                                        $agenciaSaldo1 += $item['saldo1'];
                                        $agenciaSaldo2 += $item['saldo2'];
                                        $agenciaSaldo3 += $item['saldo3'];
                                        ?>
                                        <tr>
                                            <td><?php echo $item['funcionario'] ?></td>
                                            <td><?php echo number_format($item['saldo'] / 10, 2) ?></td>
                                            <td><?php echo count(array_unique($item['clientes'])) ?></td>
                                            <td><?php echo number_format($item['desembolso'] / 10, 2) ?></td>
                                            <td><?php echo count($item['clientes']) ?></td>
                                            <td style="text-align: right; font-weight: bold; color: #0277BD;"><?php echo number_format($item['saldo1'] / 10, 2) ?></td>
                                            <td style="text-align: right;"><?php echo number_format($item['saldo1'] / $item['saldo'] * 100, 2) ?></td>
                                            <td style="text-align: right; font-weight: bold; color: #0277BD;"><?php echo number_format($item['saldo2'] / 10, 2) ?></td>
                                            <td style="text-align: right;"><?php echo number_format($item['saldo2'] / $item['saldo'] * 100, 2) ?></td>
                                            <td style="text-align: right; font-weight: bold; color: #0277BD;"><?php echo number_format($item['saldo3'] / 10, 2) ?></td>
                                            <td style="text-align: right;"><?php echo number_format($item['saldo3'] / $item['saldo'] * 100, 2) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background: black; color: white;">
                                        <td>AGENCIA</td>
                                        <td><?php echo number_format($agenciaSaldo / 10, 2) ?></td>
                                        <td><?php echo $agenciaClientes ?></td>
                                        <td><?php echo number_format($agenciaDesembolso / 10, 2) ?></td>
                                        <td><?php echo $agenciaOperaciones ?></td>
                                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($agenciaSaldo1 / 10, 2) ?></td>
                                        <td style="text-align: right;"><?php echo $agenciaSaldo > 0 ? number_format($agenciaSaldo1 / $agenciaSaldo * 100, 2) : "0.00" ?></td>
                                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($agenciaSaldo2 / 10, 2) ?></td>
                                        <td style="text-align: right;"><?php echo $agenciaSaldo > 0 ? number_format($agenciaSaldo2 / $agenciaSaldo * 100, 2) : "0.00" ?></td>
                                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($agenciaSaldo3 / 10, 2) ?></td>
                                        <td style="text-align: right;"><?php echo $agenciaSaldo > 0 ? number_format($agenciaSaldo3 / $agenciaSaldo * 100, 2) : "0.00" ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php' ?>