<?php

use CrediSoporte\Domain\Models\Credit;

include 'head.php';

$credits = Credit::with('installments')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->leftJoin('non_payment_justifications', 'tprestamo.idP', 'non_payment_justifications.credit_id')
    ->where('tprestamo.estado', 4)
    ->whereDate('tpresta_detalle.expiration_at', date('Y-m-d'))
    ->groupBy('tprestamo.idP')
    ->get()
?>

<div x-data="justification">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Cuotas atrasadas</th>
                <th>Saldo</th>
                <th>Pendiente</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($credits as $credit) { ?>

                <?php
                $saldo = 0;
                $pendiente = 0;
                $cuotasAtrasadas = 0;
                foreach ($credit->installments as $installment) {
                    $saldo += $installment->debtInstallment();

                    if ($installment->fechaProg <= date('Y-m-d')) {
                        $pendiente += $installment->debtInstallment();
                    }

                    if ($installment->fechaProg < date('Y-m-d')) {
                        $cuotasAtrasadas++;
                    }
                }
                ?>

                <tr>
                    <td><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                    <td><?php echo $cuotasAtrasadas ?></td>
                    <td style="text-align: right;">
                        <?php echo number_format($saldo, 2) ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo number_format($pendiente, 2) ?>
                    </td>
                    <td>
                        <?php if ($credit->description) { ?>
                            <button @click="openDetailsJustification('<?php echo $credit->id ?>', '<?php echo $credit->description ?>')">Ver justificación</button>
                        <?php } else if ($pendiente > 0) { ?>
                            <button @click="openModalCreateJustification('<?php echo $credit->idP ?>')">Justificar</button>
                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

    <template x-if="data">
        <div @keyup.escape.window="data = null" style="position: fixed; inset: 0; z-index: 3000; padding: 20px; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.3);">
            <div style="background: white; width: 100%; max-width: 500px; border-radius: 5px;">
                <div style="padding: 20px 20px 0;">
                    <textarea class="form-control" rows="10" x-model="data.description"></textarea>
                </div>
                <div style="padding: 20px; text-align: right;">
                    <button class="btn btn-danger" @click="deleteJustification">Eliminar</button>
                    <button class="btn btn-primary" @click="updateJustification">Actualizar</button>
                    <button class="btn btn-secundary" @click="data = null">Cerrar</button>
                </div>
            </div>
        </div>
    </template>

    <template x-if="dataCreate">
        <div @keyup.escape.window="dataCreate = null" style="position: fixed; inset: 0; z-index: 3000; padding: 20px; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.3);">
            <div style="background: white; width: 100%; max-width: 500px; border-radius: 5px;">
                <div style="padding: 20px 20px 0;">
                    <textarea class="form-control" rows="10" x-model="dataCreate.description"></textarea>
                </div>
                <div style="padding: 20px; text-align: right;">
                    <button class="btn btn-secundary" @click="dataCreate = null">Cancelar</button>
                    <button class="btn btn-primary" @click="createJustification">Guardar</button>
                </div>
            </div>
        </div>
    </template>

</div>


<script>
    const dataCreateDefault = {
        credit_id: '',
        description: ''
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('justification', () => ({
            data: null,
            dataCreate: null,
            openDetailsJustification(id, description) {
                this.data = {
                    id,
                    description
                }
            },
            deleteJustification() {
                swal({
                        title: '¿Seguro que desea eliminar la justificación?',
                        icon: 'info',
                        buttons: ['Cerrar', 'Si, eliminar']
                    })
                    .then(data => {

                        if (data) {
                            fetch(`./../app/api/deleteJustification.php?nonPaymentJustificationId=${this.data.id}`)
                                .then(response => response.json())
                                .then(data => {

                                    if (data.success) {
                                        swal({
                                                title: 'Eliminado con exito!!',
                                                icon: 'success'
                                            })
                                            .then(value => {
                                                window.location.reload();
                                            });
                                    } else {
                                        swal({
                                            title: data.message,
                                            icon: 'error'
                                        });
                                    }

                                });
                        }

                    });
            },
            updateJustification() {

                fetch(`./../app/api/updateJustification.php`, {
                        method: 'post',
                        body: JSON.stringify({
                            id: this.data.id,
                            description: this.data.description
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {
                            swal({
                                    title: 'Eliminado con exito!!',
                                    icon: 'success'
                                })
                                .then(value => {
                                    window.location.reload();
                                });
                        } else {
                            swal({
                                title: data.message,
                                icon: 'error'
                            });
                        }

                    });

            },
            openModalCreateJustification(id) {
                this.dataCreate = {
                    ...dataCreateDefault,
                    credit_id: id
                };
            },
            createJustification() {
                fetch(`../app/api/justifyNonPayment.php`, {
                        method: 'post',
                        body: JSON.stringify({
                            credit_id: this.dataCreate.credit_id,
                            description: this.dataCreate.description
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal({
                                    title: 'Registrado con exito!!',
                                    icon: 'success'
                                })
                                .then(_ => {
                                    window.location.reload();
                                });
                        } else {
                            swal({
                                title: data.message,
                                icon: 'error'
                            });
                        }
                    });
            }
        }));
    });
</script>

<script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>

<?php
include 'footer.php';
?>