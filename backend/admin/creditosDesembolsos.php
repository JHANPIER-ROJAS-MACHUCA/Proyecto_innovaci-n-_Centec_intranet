<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\User;

include 'head.php';

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

switch ($request->user()->tipoU) {
    case '1':
    case '2':
    case '3':
        $tiposUsuariosPermitidos = ['3', '4'];
        break;

    default:
        $tiposUsuariosPermitidos = ['4'];
        break;
}

$users = User::active()
    ->whereIn('tipoU', $tiposUsuariosPermitidos)
    ->get();

$totalCredits = Credit::whereIn('tprestamo.estado', [4, 5])
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->join('tclie_general', 'tclie_general.idCG', 'tprestamo.idCG')
            ->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->count();

$credits = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->leftJoin('tusuario', 'tprestamo.user_id', 'tusuario.idU')
    ->leftJoin('tusuario as portfolio', 'tclie_general.idU', 'portfolio.idU')
    ->whereIn('tprestamo.estado', [4, 5])
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        if ($request->byPortfolio === 'on') {
            $query->where('tclie_general.idU', $request->employee);
        }else{
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->orderBy('tprestamo.fechaDesembolso', 'desc')
    ->orderBy('tprestamo.idP')
    ->offset($perPage * ($currentPage - 1))
    ->limit($perPage)
    ->select(
        'tprestamo.*', 
        'tclie_general.dni', 
        'tclie_general.ap', 
        'tclie_general.am', 
        'tclie_general.nom',
        'credit_types.name',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU',
        'portfolio.apU as portfolio_ap',
        'portfolio.amU as portfolio_am',
        'portfolio.nomU as portfolio_nom',
        )
    ->get();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Creditos desembolsos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
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
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label id="label-info"><?php echo $request->byPortfolio === 'on' ? 'Cartera' : 'Funcionario' ?></label>
                        <label>
                            <input type="checkbox" id="toggle-checkbox" name="byPortfolio" <?php echo $request->byPortfolio === 'on' ? 'checked' : '' ?>>
                            <span id="label-checkbox">Por cartera</span>
                        </label>
                    </div>
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
                    <button type="button" class="btn btn-success" onclick="mostrarTodo()">Mostrar Todos</button class="form-control">
                    <button type="submit" formaction="./../app/exel/desembolsos.php" formmethod="post" class="btn btn-warning">EXPORTAR A EXEL</button>
                </div>
            </div>
        </form>

        <div style="overflow-y: auto;">
            <table class="table" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Prestamo</th>
                        <th>Tasa</th>
                        <th>Forma pago</th>
                        <th>Producto</th>
                        <th>Periodo</th>
                        <th>Fecha desembolso</th>
                        <th>Usuario</th>
                        <th>Cartera</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credits as $credit) { ?>

                        <tr>
                            <td><?php echo $credit->dni ?></td>
                            <td><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                            <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
                            <td><?php echo round($credit->interest_rate, 3) ?></td>
                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                            <td><?php echo $credit->name ?></td>
                            <td><?php echo $credit->number_installments ?></td>
                            <td><?php echo date('d/m/Y', strtotime($credit->fechaDesembolso)) ?></td>
                            <td><?php echo $credit->apU ?></td>
                            <td><?php echo $credit->portfolio_ap ?></td>
                            <td><?php echo $credit->statusToString() ?></td>
                            <td style="width: 0;">
                                <div style="white-space: nowrap;">
                                    <button onclick="showHistorialDePago(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Historial de pagos">
                                        <img src="./../public/resource/icons/pago.png" width="20" alt="">
                                    </button>
                                    <button onclick="showResumenCredito(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Resumen credito">
                                        <img src="./../public/resource/icons/cronograma.png" width="20" alt="">
                                    </button>
                                    <button onclick="showVoucherDesembolso(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Boucher de desembolso">
                                        <img src="./../public/resource/icons/factura.png" width="20" alt="">
                                    </button>
                                    <button onclick="showContrato(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Contrato">
                                        <img src="./../public/resource/icons/contrato.png" width="20" alt="">
                                    </button>
                                    <button onclick="showLetra(<?php echo $credit->idP ?>)" style="border: 0; padding: 0 3px; background: white;" title="Letra">
                                        <img src="./../public/resource/icons/firma-digital.png" width="20" alt="">
                                    </button>
                                </div>
                            </td>
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


    </div>
</div>

<script>
    const labelInfo = document.getElementById('label-info');
    const labelChecbox = document.getElementById('label-checkbox');
    const toggleCheckbox = document.getElementById('toggle-checkbox');

    toggleCheckbox.onchange = (e) => {
        if (e.target.checked) {
            labelInfo.innerText = "Cartera"
            // labelChecbox.innerText = "Por funcionario"
        }else{
            labelInfo.innerText = "Funcionario"
            // labelChecbox.innerText = "Por cartera"
        }
    }

    function showHistorialDePago(creditId) {
        window.open('./../app/pdf/historialDePago.php?creditId=' + creditId);
    }

    function showResumenCredito(creditId) {
        window.open('./../app/pdf/resumenCredito.php?creditId=' + creditId);
    }

    function showVoucherDesembolso(creditId) {
        window.open('./../app/pdf/voucherDesembolso.php?creditId=' + creditId);
    }

    function showContrato(creditId) {
        window.open('./contrato.php?creditId=' + creditId);
    }

    function showLetra(creditId) {
        window.open('./../app/pdf/letraCredit.php?creditId=' + creditId);
    }

    function navigateToPage(page) {
        const query = new URLSearchParams(window.location.search);
        query.set('page', page);

        window.location.href = window.location.pathname + '?' + query.toString();
    }

    function mostrarTodo() {
        window.location.href = window.location.pathname;
    }
</script>

<?php
include 'footer.php'
?>