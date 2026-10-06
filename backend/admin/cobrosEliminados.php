<?php

use CrediSoporte\Domain\Models\Transaction;

require 'head.php';

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

// $startDate = $request->get('startDate', date('Y-m-d', strtotime(date('Y-m-d') . '- 1 month')));
// $endDate = $request->get('endDate', date('Y-m-d'));

$deletedTransactions = Transaction::join('tprestamo', 'tcaja_usu_detal.conejo', 'tprestamo.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
    ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
    ->where('tcaja_usu_detal.estadodt', '1')
    ->where('tcaja_usu_detal.tipo', '3')
    ->orderBy('tcaja_usu_detal.idCAD', 'desc')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->select(
        'tcaja_usu_detal.*',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU',
    )
    ->get();

$totalItems = Transaction::join('tprestamo', 'tcaja_usu_detal.conejo', 'tprestamo.idP')
    ->where('tcaja_usu_detal.estadodt', '1')
    ->where('tcaja_usu_detal.tipo', '3')
    ->count();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Cobros eliminados <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
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
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Capital</th>
                        <th>Interes</th>
                        <th>Mora</th>
                        <th>Total</th>
                        <th>Fecha creación</th>
                        <th>Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deletedTransactions as $transaction) { ?>
                        <tr>
                            <td><?php echo $transaction->idCAD ?></td>
                            <td><?php echo $transaction->ap . " " . $transaction->am . " " . $transaction->nom ?></td>
                            <td><?php echo $transaction->apU . " " . $transaction->amU . " " . $transaction->nomU ?></td>
                            <td><?php echo number_format($transaction->capital, 2) ?></td>
                            <td><?php echo number_format($transaction->interest, 2) ?></td>
                            <td><?php echo number_format($transaction->mora, 2) ?></td>
                            <td><?php echo number_format($transaction->total, 2) ?></td>
                            <td><?php echo $transaction->created_at ?></td>
                            <td>
                                <button style="border: 0; background: transparent; padding: 0;" onclick="openComprobante(<?php echo $transaction->idCAD ?>)">
                                    <img src="./../public/resource/icons/factura.png" width="25" alt="">
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php for ($i = 1; $i <= ceil($totalItems / $perPage); $i++) { ?>
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