<?php

use CrediSoporte\Domain\Helpers\Holiday;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\User;

include('head.php');

$users = [];
if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2') {
    $users = User::active()->whereIn('tipoU', [3, 4])->get();
}else{
    $users = User::active()->where('idU', $request->user()->idU)->get();
}

$meses = [
    'ENERO',
    'FEBRERO',
    'MARZO',
    'ABRIL',
    'MAYO',
    'JUNIO',
    'JULIO',
    'AGOSTO',
    'SEPTIEMBRE',
    'OCTUBRE',
    'NOVIEMBRE',
    'DICIEMBRE'
];
$dias = [
    'Domingo',
    'Lunes',
    'Martes',
    'Miercoles',
    'Jueves',
    'Viernes',
    'Sabado',
];

$month = $request->month ?? date('Y-m');
$date = date('Y-m-d', strtotime($month));
$employee = $request->employee ?? $request->user()->idU;

$goal = Goal::where('user_id', $employee)
    ->whereRaw("'$date' between start_at and end_at")
    ->first();

$resumenes = [];

if ($goal) {
    $resumenes = Credit::select('fechaDesembolso')
        ->selectRaw('sum(capital) / 10 as saldo')
        ->selectRaw('count(idP) as operation')
        ->whereIn('estado', [4, 5])
        ->where('user_id', $goal->user_id)
        ->whereBetween('fechaDesembolso', [$goal->start_at, $goal->end_at])
        ->groupBy('fechaDesembolso')
        ->get();
}

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;" x-data="data">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Metas <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <h3>Metas</h3>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); align-items: end; gap: 30px;">
                <div>
                    <label>Funcionario</label>
                    <select name="employee" class="form-control">
                        <option value="">Seleccione</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?php echo $user->idU ?>" <?php echo (string) $employee === (string) $user->idU ? 'selected' : '' ?>>
                                <?php echo $user->apU ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label>Fecha</label>
                    <input type="month" class="form-control" name="month" value="<?php echo $month ?>">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </div>
            </div>
        </form>

        <?php if ($goal) { ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; margin-top: 30px;">
                <div>
                    <label>Meta saldo</label>
                    <input type="text" class="form-control" style="color: #2E86C1;font-weight: 700; text-align: center;" value="<?php echo number_format($goal->saldo, 2) ?>">
                </div>
                <div>
                    <label>Meta operaciones</label>
                    <input type="text" class="form-control" style="color: #2E86C1;font-weight: 700; text-align: center;" value="<?php echo $goal->operation ?>">
                </div>
                <div>
                    <label>Meta mensual saldo</label>
                    <input type="text" class="form-control" style="color: #229954;font-weight: 700; text-align: center;" value="<?php echo number_format($resumenes->sum('saldo') / $goal->saldo * 100, 2) ?>%">
                </div>
                <div>
                    <label>Meta mensual operaciones</label>
                    <input type="text" class="form-control" style="color: #229954;font-weight: 700; text-align: center;" value="<?php echo number_format($resumenes->sum('operation') / $goal->operation * 100, 2) ?>%">
                </div>
            </div>

            <div style="overflow-y: auto;">
                <table class="table table-bordered" style="margin-top: 30px;">
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>DIAS LABORABLES</th>
                            <th>METAS DIARIAS</th>
                            <th>EJECUTADO AL DÍA</th>
                            <th>AVANCE</th>
                            <th>META MENSUAL</th>
                            <th>CUMPLIMIENTO</th>
                            <th>META DIARIA</th>
                            <th>EJECUTADO AL DIA</th>
                            <th>AVANCE</th>
                            <th>META MENSUAL</th>
                            <th>CUMPLIMIENTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dates = Holiday::workinDatesBetween($goal->start_at, $goal->end_at);
                        $diasRestantes = count($dates);
                        ?>

                        <?php foreach ($dates as $key => $workingDate) { ?>
                            <?php
                            $fecha = $workingDate->format('Y-m-d');
                            $saldoHastaAhora = $resumenes->whereBetween('fechaDesembolso', [$goal->start_at, $fecha])->sum('saldo');
                            $saldoHoy = $resumenes->where('fechaDesembolso', $fecha)->sum('saldo');

                            $operationHastaAhora = $resumenes->whereBetween('fechaDesembolso', [$goal->start_at, $fecha])->sum('operation');
                            $operationHoy = $resumenes->where('fechaDesembolso', $fecha)->sum('operation');

                            $goalSaldoNow = ($goal->saldo - ($saldoHastaAhora - $saldoHoy)) / $diasRestantes;
                            $isSaldoTrue = round($goalSaldoNow) <= $saldoHoy;

                            $goalOperationNow = ($goal->operation - ($operationHastaAhora - $operationHoy)) / $diasRestantes;
                            $isOperationTrue = round($goalOperationNow) <= $operationHoy;
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $key + 1 ?></td>
                                <td><?php echo $workingDate->format('d/m/Y') . ' ' . $dias[$workingDate->format('w')] ?></td>
                                <td style="text-align: right;"><?php echo number_format($goalSaldoNow, 2) ?></td>
                                <td style="text-align: right;"><?php echo number_format($saldoHoy, 2) ?></td>
                                <td style="text-align: right;"><?php echo (int) $goalSaldoNow === 0 ? 0 : number_format($saldoHoy / $goalSaldoNow * 100, 2) ?>%</td>
                                <td style="text-align: right;"><?php echo number_format($saldoHastaAhora / $goal->saldo * 100, 2) ?>%</td>
                                <td style="text-align: center;<?php echo $isSaldoTrue ? 'background: #27AE60; color: white;' : 'background: #E74C3C; color: white;' ?>">
                                    <?php echo $isSaldoTrue ? 'SI' : 'NO' ?>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($goalOperationNow, 2) ?></td>
                                <td style="text-align: right;"><?php echo $operationHoy ?></td>
                                <td style="text-align: right;"><?php echo (int) $goalOperationNow === 0 ? 0 : number_format($operationHoy / $goalOperationNow * 100, 2) ?>%</td>
                                <td style="text-align: right;"><?php echo number_format($operationHastaAhora / $goal->operation * 100, 2) ?>%</td>
                                <td style="text-align: center;<?php echo $isOperationTrue ? 'background: #27AE60; color: white;' : 'background: #E74C3C; color: white;' ?>">
                                    <?php echo $isOperationTrue ? 'SI' : 'NO' ?>
                                </td>
                            </tr>
                            <?php
                            $diasRestantes--;
                            ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

    </div>
</div>

<script>
    function consultaApi() {
        fetch('../app/api/test.php');
    }

    const factoryDefault = {
        user_id: '',
        date: <?php echo json_encode(date('Y-m-d')) ?>,
        saldo: '',
        operation: ''
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('data', () => ({
            factory: {
                ...factoryDefault
            },
            error: null,
            init() {
                this.$watch('factory.saldo', (value, oldValue) => {
                    if (isNaN(value)) {
                        this.factory.saldo = oldValue;
                        return;
                    }
                })
                this.$watch('factory.operation', (value, oldValue) => {
                    if (isNaN(value)) {
                        this.factory.operation = oldValue;
                        return;
                    }
                })
            },
            onSubmit(e) {
                e.preventDefault();

                this.error = null;

                let url = './../app/api/createMeta.php';
                if (this.factory.id) {
                    url = '../app/api/updateMeta.php';
                }

                fetch(url, {
                        method: 'post',
                        body: JSON.stringify(this.factory),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal({
                                    title: this.factory.id ?
                                        'Meta actualizado con éxito.' : 'Meta registrado con éxito.',
                                    icon: 'success'
                                })
                                .then(_ => {
                                    window.location.reload();
                                });
                        } else if (data.errors) {
                            this.error = data.errors
                        } else {
                            swal({
                                title: 'Se produjo un error desconocido.',
                                icon: 'error'
                            })
                        }
                    })
                    .catch((err) => {
                        swal({
                            title: 'Se produjo un error desconocido.',
                            icon: 'error'
                        })
                    });
            },
            cancel() {
                this.factory = {
                    ...factoryDefault
                };
            },
            onSelectToUpdate(data) {
                this.factory = {
                    id: data.id,
                    user_id: data.idU,
                    date: data.start_at,
                    saldo: data.saldo,
                    operation: data.operation,
                }
            },
            onDelete(goalId) {
                swal({
                        title: '¿Seguro que desea eliminar la meta?',
                        icon: 'warning',
                        buttons: ['No, cerrar', 'Si, eliminar meta']
                    })
                    .then(value => {
                        if (value === true) {
                            fetch(`../app/api/deleteMeta.php?goalId=${goalId}`)
                                .then(response => response.json())
                                .then(data => {
                                    swal({
                                            title: 'Eliminado con exito',
                                            icon: 'success'
                                        })
                                        .then(_ => {
                                            window.location.reload();
                                        })
                                })
                        }
                    })
            }
        }))
    })
</script>

<script src="./../public/resource/js/alpine.3.10.3.min.js" defer></script>

<?php include('footer.php') ?>