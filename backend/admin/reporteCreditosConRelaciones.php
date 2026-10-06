<?php

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;

include('head2.php');

$credits = Credit::with(['installments', 'relations'])
    ->select(
        'tprestamo.*',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tclie_general.dni',
        'tclie_general.direc',
        'tclie_general.cel',
        'spouse.dni as spouse_dni',
        'spouse.ap as spouse_ap',
        'spouse.am as spouse_am',
        'spouse.nom as spouse_nom',
        'spouse.direc as spouse_direc',
        'aval.dni as aval_dni',
        'aval.ap as aval_ap',
        'aval.am as aval_am',
        'aval.nom as aval_nom',
        'aval.direc as aval_direc'
    )
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tvinculacion', 'tprestamo.idV', 'tvinculacion.idV')
    ->leftJoin('tclie_general as spouse', 'tvinculacion.conyugue', 'spouse.idCG')
    ->leftJoin('tclie_general as aval', 'tvinculacion.aval', 'aval.idCG')
    ->where('tprestamo.estado', 4)
    ->get();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <h5 style="color:white">Propuestas<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <div style="display: flex; justify-content: space-between;">
            <h3 style="font-weight: bold;">CREDITOS</h3>
        </div>

        <table class="table table-striped" id="reporteTable" style="font-size:12px">
            <thead>
                <tr>
                    <th>CODIGO</th>
                    <th>DNI</th>
                    <th>APELLIDO PATERNO</th>
                    <th>APELLIDO MATERNO</th>
                    <th>NOMBRES</th>
                    <th>DIRECCIÓN</th>
                    <th>CELULAR</th>
                    <th>PRESTAMO DE</th>
                    <th>TASA</th>
                    <th>PENDIENTE CAPITAL</th>
                    <th>FECHA DESEMBOLSO</th>
                    <th>DIAS ATRASO</th>
                    <th>MORA</th>
                    <th>DEUDA TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $delay = new Delay();
                $delay->setHolidays(Holiday::pluck('date'));
                ?>

                <?php foreach ($credits as $item) { ?>

                    <?php
                    $delay->payment_period = $item->payment_period;
                    $delay->condone_dates = $item->condoneDates->pluck('date')->toArray();
                    $delay->penalty = $item->penalty;

                    $sumCapitalDebt = 0;

                    $totalMora = 0;
                    $totalCuota = 0;

                    $diasAtraso = 0;
                    foreach ($item->installments as $keyInstallment => $installment) {
                        $capitalDebt = $installment->capitalDebt();
                        $interestDebt = $installment->interestDebt();
                        
                        $sumCapitalDebt += $capitalDebt;

                        $nextInstallment = $item->installments[$keyInstallment + 1] ?? null;
                        $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                        $totalCuota += ($capitalDebt + $interestDebt);
                        $totalMora += $penaltyDebt;

                        //veradfa
                        if ($diasAtraso === 0 && (($capitalDebt + $interestDebt) > 0) && $installment->expiration_at < date('Y-m-d')) {
                            $ini = new DateTime($installment->expiration_at);
                            $fin = new DateTime(date('Y-m-d'));
                            $diasAtraso = $ini->diff($fin)->days;
                        }
                    }
                    ?>

                    <tr>
                        <td><?php echo $item->idP ?></td>
                        <td><?php echo $item->dni ?></td>
                        <td><?php echo $item->ap ?></td>
                        <td><?php echo $item->am ?></td>
                        <td><?php echo $item->nom ?></td>
                        <td><?php echo $item->direc ?></td>
                        <td><?php echo $item->cel ?></td>
                        <td><?php echo $item->capital / 10 ?></td>
                        <td><?php echo round($item->interest_rate, 2) ?></td>
                        <td><?php echo $sumCapitalDebt / 10 ?></td>
                        <td><?php echo $item->fechaDesembolso ?></td>
                        <td><?php echo $diasAtraso ?></td>
                        <td><?php echo $totalMora / 10 ?></td>
                        <td><?php echo ($totalCuota + $totalMora) / 10 ?></td>
                    </tr>

                    <?php foreach($item->relations as $relation) { ?>
                            
                        <tr>
                            <td><?php echo $item->idP ?></td>
                            <td><?php echo $relation->dni ?></td>
                            <td><?php echo $relation->ap ?></td>
                            <td><?php echo $relation->am ?></td>
                            <td><?php echo $relation->nom ?></td>
                            <td><?php echo $relation->direc ?></td>
                            <td><?php if ($relation->pivot->type === 'spouse') {
                            echo 'CONYUGE';
                            } else {
                            echo 'AVAL';
                            } ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                    <?php } ?>
                    
                    <?php if ($item->spouse_dni) { ?>
                    
                        <tr>
                            <td><?php echo $item->idP ?></td>
                            <td><?php echo $item->spouse_dni ?></td>
                            <td><?php echo $item->spouse_ap ?></td>
                            <td><?php echo $item->spouse_am ?></td>
                            <td><?php echo $item->spouse_nom ?></td>
                            <td><?php echo $item->spouse_direc ?></td>
                            <td>CONYUGE</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php } ?>

                    <?php if ($item->aval_dni) { ?>
                        <tr>
                            <td><?php echo $item->idP ?></td>
                            <td><?php echo $item->aval_dni ?></td>
                            <td><?php echo $item->aval_ap ?></td>
                            <td><?php echo $item->aval_am ?></td>
                            <td><?php echo $item->aval_nom ?></td>
                            <td><?php echo $item->aval_direc ?></td>
                            <td>AVAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php } ?>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/resource/js/plugins/dataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#reporteTable').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                /*{ extend: 'copy'},
                {extend: 'csv'},*/
                {
                    extend: 'excel',
                    title: 'Deudores '
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

<?php
include('footer.php');
?>