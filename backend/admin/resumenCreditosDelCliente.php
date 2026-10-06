<?php

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Holiday;

include 'head.php';

$customers = Customer::get();

$credits = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.idCG', $request->customer)
    ->whereIn('tprestamo.estado', [4, 5])
    ->orderBy('tprestamo.idP', 'desc')
    ->limit($request->number ?? 5)
    ->select(
        'tprestamo.*',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom'
    )
    ->get();

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Resumen de creditos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>Cliente</label>
                    <div>
                        <select id="txtdni" name="customer">
                            <option value="">Seleccione</option>
                            <?php foreach ($customers as $customer) { ?>
                                <option value="<?php echo $customer->idCG ?>" <?php echo $request->customer == $customer->idCG ? 'selected' : '' ?>>
                                    <?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div style="margin-right: 20px;">
                    <label>Número creditos</label>
                    <select name="number" class="form-control">
                        <option value="5" <?php echo $request->number === '5' ? 'selected' : '' ?>>5 ultimos créditos</option>
                        <option value="10" <?php echo $request->number === '10' ? 'selected' : '' ?>>10 ultimos créditos</option>
                        <option value="20" <?php echo $request->number === '20' ? 'selected' : '' ?>>20 ultimos créditos</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="./../app/pdf/posicionCliente.php?clientId=<?php echo $request->customer ?>" target="_blank" class="btn btn-warning">Imprimir</a>
                </div>
            </div>
        </form>

        <div style="overflow-x: auto; margin-top: 30px;">
            <table class="table" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <!-- <th>Cliente</th> -->
                        <th style="width: 90px;">N° crédito</th>
                        <th style="width: 100px;">Prestamo</th>
                        <th style="width: 50px;">Tasa</th>
                        <th style="width: 100px;">Estado</th>
                        <th style="width: 50%; min-width: 350px;">Cuotas</th>
                        <th style="width: 100px;">Total dias</th>
                        <th style="width: 100px;">Resumen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credits as $credit) { ?>

                        <?php
                        $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
                        $delay->payment_period = $credit->payment_period;
                        $delay->penalty = $credit->penalty;

                        $totalDiasAtrasados = 0;
                        ?>
                        <tr>
                            <!-- <td><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td> -->
                            <td style="text-align: right;"><?php echo $credit->n_credito ?></td>
                            <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td style="text-align: right;"><?php echo round($credit->interest_rate, 2) . '%' ?></td>
                            <td><?php echo $credit->estado === '4' ? 'ACTIVO' : 'FINALIZADO' ?></td>
                            <td>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(25px, 1fr)); gap: 10px;">
                                    <?php foreach ($credit->installments as $key => $installment) { ?>

                                        <?php
                                        $capitalDebt = $installment->capitalDebt();
                                        $interestDebt = $installment->interestDebt();

                                        $nextInstallment = $credit->installments[$key + 1] ?? null;
                                        $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

                                        $diasAtrasados = 0;
                                        if (($capitalDebt + $interestDebt) > 0) {
                                            $ini = new DateTime($installment->expiration_at);
                                            $fin = new DateTime(date('Y-m-d'));
                                            $diasAtrasados = $ini->diff($fin)->days;

                                            if ($installment->expiration_at > date('Y-m-d')) {
                                                $diasAtrasados = $diasAtrasados * -1;
                                            }else{
                                                $totalDiasAtrasados += $diasAtrasados;
                                            }
                                        } else {
                                            $ini = new DateTime($installment->expiration_at);
                                            $fin = new DateTime($installment->payment_date);
                                            $diasAtrasados = $ini->diff($fin)->days;

                                            if ($installment->expiration_at > $installment->payment_date) {
                                                $diasAtrasados = $diasAtrasados * -1;
                                            } else {
                                                $totalDiasAtrasados += $diasAtrasados;
                                            }
                                        }

                                        $background = "#EAECEE";
                                        $color = "#2C3E50";
                                        $borderColor = "#2C3E50";

                                        if (($capitalDebt + $interestDebt + $penaltyDebt) === 0 && $diasAtrasados > 0) {
                                            $background = "#F9EBEA";
                                            $color = "#C0392B";
                                            $borderColor = "#C0392B";
                                        } else if (($capitalDebt + $interestDebt + $penaltyDebt) === 0 && $diasAtrasados <= 0) {
                                            $background = "#EAFAF1";
                                            $color = "#27AE60";
                                            $borderColor = "#27AE60";
                                        }

                                        ?>
                                        <div style="border: 1px solid <?php echo $borderColor ?>; border-radius: 10px; overflow: hidden;">
                                            <div style="text-align: center; font-size: 0.9em;">
                                                <?php echo $installment->number ?>
                                            </div>
                                            <!-- <div style="text-align: center;"><?php echo date('d/m/Y', strtotime($installment->expiration_at)) ?></div> -->
                                            <div style="text-align: center; background: <?php echo $background ?>; color: <?php echo $color ?>; border-top: 1px solid <?php echo $borderColor ?>; font-weight: bold; font-size: 1.2em;">
                                                <?php echo $diasAtrasados ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td><?php echo $totalDiasAtrasados ?></td>
                            <td>
                                <?php echo number_format($totalDiasAtrasados / $credit->number_installments, 2) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        $("#txtdni").select2({
            minimumResultsForSearch: 2,
            placeholder: "Seleccione Cliente",
            allowClear: false,
            width: '400px'
        });

    });
</script>

<?php include 'footer.php' ?>