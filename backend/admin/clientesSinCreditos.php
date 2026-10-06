<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Transaction;

require 'head.php';

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$customers = Customer::with(['credits' => function ($query) {
    $query->where('estado', '5');
}, 'credits.installments'])
    ->join('tprestamo', 'tclie_general.idCG', 'tprestamo.idCG')
    // ->where('tprestamo.estado', '5')
    ->groupBy('tclie_general.idCG')
    ->orderBy('tclie_general.idCG', 'desc')
    ->havingRaw('tprestamo.estado = 5 and tprestamo.estado not in (4)')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->get();

$summary = Customer::join('tprestamo', 'tclie_general.idCG', 'tprestamo.idCG')
    ->groupBy('tclie_general.idCG')
    ->havingRaw('tprestamo.estado = 5 and tprestamo.estado not in (4)')
    // ->selectRaw('count(DISTINCT tclie_general.idCG) as total')
    ->first();

echo $summary->total;

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Reporte cobro creditos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <!-- <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>Cobro de:</label>
                    <input type="date" class="form-control" name="startDate" value="<?php echo $startDate ?>">
                </div>
                <div style="margin-right: 20px;">
                    <label>Cobro hasta</label>
                    <input type="date" class="form-control" name="endDate" value="<?php echo $endDate ?>">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">APLICAR FILTRO</button>
                </div>
            </div>
        </form> -->

        <div style="overflow-x: auto;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Estado civil</th>
                        <th>Tipo vivienda</th>
                        <th>Direccion</th>
                        <th>Celular</th>
                        <th>Rubro</th>
                        <th>Dirección negocio</th>
                        <th>Promedio mora</th>
                        <th>Promedio desembolso</th>
                        <th>Carta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer) { ?>

                        <?php
                        foreach ($customer->credits as $credit) {
                            foreach ($credit->installments as $installment) {
                            }
                        }
                        ?>

                        <tr>
                            <td><?php echo $customer->idCG ?></td>
                            <td><?php echo $customer->dni ?></td>
                            <td><?php echo $customer->ap . " " . $customer->am . " " . $customer->nom ?></td>
                            <td><?php echo $customer->civilStatusToString() ?></td>
                            <td><?php echo $customer->tipo ?></td>
                            <td><?php echo $customer->direc ?></td>
                            <td><?php echo $customer->cel ?></td>
                            <td><?php echo $customer->rubro ?></td>
                            <td><?php echo $customer->comentario ?></td>
                            <td><?php echo 0 ?></td>
                            <td><?php echo 0 ?></td>
                            <td>

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

    function openComprobante(paymentId) {
        window.open('../app/pdf/voucher.php?operacion=' + paymentId, "voucher",
            "width=600,height=800,scrollbars=NO");
    }
</script>

<?php require 'footer.php'; ?>