<?php

use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\User;

include('head.php');

$goals = Goal::join('tusuario', 'goals.user_id', 'tusuario.idU')
    ->orderBy('goals.id', 'desc')
    ->get();

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

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;" x-data="data">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Metas <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <h3>Metas</h3>

        <form x-on:submit="onSubmit">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 30px;">
                <div>
                    <label>Usuario</label>
                    <select name="" class="form-control" x-model="factory.user_id">
                        <option value="">Seleccione</option>
                        <?php foreach (User::active()->whereIn('tipoU', [3, 4])->get() as $user) { ?>
                            <option value="<?php echo $user->idU ?>"><?php echo $user->apU ?></option>
                        <?php } ?>
                    </select>
                    <div style="color: #E74C3C; font-weight: 600; font-size: 13px; margin-top: 6px;" x-show="error?.user_id" x-text="error?.user_id"></div>
                </div>
                <div>
                    <label>Fecha</label>
                    <input type="date" class="form-control" x-model="factory.date">
                    <div style="color: #E74C3C; font-weight: 600; font-size: 13px; margin-top: 6px;" x-show="error?.date" x-text="error?.date"></div>
                </div>
                <div>
                    <label>Saldo</label>
                    <input type="text" class="form-control" x-model="factory.saldo">
                    <div style="color: #E74C3C; font-weight: 600; font-size: 13px; margin-top: 6px;" x-show="error?.saldo" x-text="error?.saldo"></div>
                </div>
                <div>
                    <label>Operaciones</label>
                    <input type="text" class="form-control" x-model="factory.operation">
                    <div style="color: #E74C3C; font-weight: 600; font-size: 13px; margin-top: 6px;" x-show="error?.operation" x-text="error?.operation"></div>
                </div>
                <div>
                    <div style="white-space: nowrap;margin-top: 20px;">
                        <button type="button" class="btn btn-secundary" style="margin-right: 10px;" @click="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span x-show="!factory.id">Crear meta</span>
                            <span x-show="factory.id">Actualizar</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <table class="table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Saldo</th>
                    <th>Operaciones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($goals as $goal) { ?>
                    <?php
                    $f = date('Y-m', strtotime($goal->start_at));
                    $g = explode('-', $f);

                    $mes = $meses[$g[1] - 1];
                    ?>
                    <tr>
                        <td><?php echo $g[0] . ' ' . $mes ?></td>
                        <td><?php echo "$goal->apU $goal->amU $goal->nomU" ?></td>
                        <td><?php echo number_format($goal->saldo, 2) ?></td>
                        <td><?php echo $goal->operation ?></td>
                        <td>
                            <a class="btn btn-default" href="./../app/exel/metas.php?goalId=<?php echo $goal->id ?>" target="_blank">Descargar</a>
                            <button class="btn btn-primary" @click='onSelectToUpdate(<?php echo $goal ?>)'>Editar</button>
                            <button class="btn btn-danger" @click="onDelete(<?php echo $goal->id ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

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