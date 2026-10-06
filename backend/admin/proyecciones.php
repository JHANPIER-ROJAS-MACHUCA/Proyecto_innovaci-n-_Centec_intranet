<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;

require 'head.php';

$startDate = $request->startDate ?? date('Y-m-d', strtotime(date('Y-m-d') . '-30 days'));
$endDate = $request->endDate ?? date('Y-m-d');

$desembolso = Credit::selectRaw('sum(montoAprovado) as totalDesembolso')
    ->whereIn('estado', [4, 5])
    ->whereBetween('fechaDesembolso', [$startDate, $endDate])
    ->first();

$cobros = Transaction::selectRaw('sum(total) as total')
    ->selectRaw('sum(cuota) as cuota')
    ->selectRaw('sum(mora) as mora')
    ->selectRaw('sum(discount) as descuento')
    ->where('estadodt', 2)
    ->where('tipo', 3)
    ->whereBetween('created_at', [$startDate, $endDate])
    ->first();

$proyeccion = Installment::selectRaw('sum(tpresta_detalle.cuota) as cuota')
    ->selectRaw('sum(tpresta_detalle.interest) as interes')
    ->join('tprestamo', 'tpresta_detalle.idP', 'tprestamo.idP')
    ->whereIn('tprestamo.estado', [4, 5])
    ->whereBetween('tpresta_detalle.fechaProg', [$startDate, $endDate])
    ->first();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Proyecciones <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: flex; align-items: end;">
                <div style="margin-right: 20px;">
                    <label>De</label>
                    <input type="date" class="form-control" name="startDate" value="<?php echo $startDate ?>">
                </div>
                <div style="margin-right: 20px;">
                    <label>Hasta</label>
                    <input type="date" class="form-control" name="endDate" value="<?php echo $endDate ?>">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">APLICAR FILTRO</button>
                </div>
            </div>
        </form>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <label>TOTAL DESEMBOLSADO</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: red;" value="<?php echo number_format($desembolso->totalDesembolso, 2) ?>">
            </div>
            <div>
                <label>TOTAL CUOTA</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: blue;" value="<?php echo number_format($cobros->cuota, 2) ?>">
            </div>
            <div>
                <label>TOTAL MORA</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: blue;" value="<?php echo number_format($cobros->mora, 2) ?>">
            </div>
            <div>
                <label>TOTAL DESCUENTO</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: blue;" value="<?php echo number_format($cobros->descuento, 2) ?>">
            </div>
            <div>
                <label>TOTAL COBRADO</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: blue;" value="<?php echo number_format($cobros->total, 2) ?>">
            </div>
            <div>
                <label>PROYECCION DE CUOTAS</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: orange;" value="<?php echo number_format($proyeccion->cuota, 2) ?>">
            </div>
            <div>
                <label>PROYECCION DE INTERESES</label>
                <input type="text" readonly class="form-control" style="font-weight: bold;color: orange;" value="<?php echo number_format($proyeccion->interes, 2) ?>">
            </div>
        </div>

    </div>
</div>

<?php require 'footer.php' ?>