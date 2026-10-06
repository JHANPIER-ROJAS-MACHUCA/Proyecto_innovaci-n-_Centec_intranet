<?php

use CrediSoporte\Domain\Models\Customer;

include('head.php');

$motivos = $database->table('tahorro_motivo')
    ->where('tipoM', 1)
    ->whereNotNull('monto')
    ->get();

$customer = Customer::leftJoin('tahorro', 'tclie_general.idCG', 'tahorro.id')
    ->find($request->customerId);

$transactions = [];
$saldo = 0;
$retiro = 0;
$ahorro = 0;

if (isset($customer->idA) && $customer->idA) {
    $s = $database->table('tahorro_deta')
        ->where('idA', $customer->idA)
        ->where('estad', 1)
        ->selectRaw('sum(if(tahorro_deta.tipo = 7,tahorro_deta.monto,0)) as ahorro')
        ->selectRaw('sum(if(tahorro_deta.tipo = 8,tahorro_deta.monto,0)) as retiro')
        ->first();

    $ahorro = $s->ahorro ?? 0;
    $retiro = $s->retiro ?? 0;
    $saldo = $ahorro - $retiro;

    $transactions = $database->table('tahorro_deta')
        ->select(
            'tahorro_deta.idAd',
            'tusuario.nomU',
            'tusuario.apU',
            'tusuario.amU',
            'tahorro_motivo.motivo',
            'tahorro_deta.fecha',
            'tahorro_deta.monto',
            'tahorro_deta.tipo'
        )
        ->join('tahorro_motivo', 'tahorro_deta.moti', 'tahorro_motivo.idam')
        ->join('tusuario', 'tahorro_deta.idU', 'tusuario.idU')
        ->where('tahorro_deta.idA', $customer->idA)
        ->where('tahorro_deta.estad', 1)
        ->orderBy('idAd', 'desc')
        ->get();
}

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
        </div>
        <h5 style="color:white">Detalle del ahorro <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <?php if ($customer) { ?>
            <div class="row">
                <div class="col-sm-4">
                    <div style="border: 1px solid #e7eaec; padding: 20px;">
                        <h3>Datos de la cuenta</h3>

                        <div style="margin-top: 20px;">
                            <div>Número de cuenta:</div>
                            <div style="font-size: 16px; font-weight: 600;">
                                <?php echo $customer->idA ?? 'Register un movimiento para generar la cuenta' ?>
                            </div>
                        </div>
                        <div style="margin-top: 10px;">
                            <div>Cliente:</div>
                            <div style="font-size: 16px; font-weight: 600;">
                                <?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom ?>
                            </div>
                        </div>
                        <div style="margin-top: 10px;">
                            <div>Saldo total</div>
                            <div style="font-size: 16px; font-weight: 600;">
                                <span>S/.</span>
                                <span><?php echo number_format($saldo, 2) ?></span>
                            </div>
                        </div>
                        <div style="display: flex; margin-top: 10px;">
                            <div style="flex: 1 1 0%;">
                                <div>Ahorro</div>
                                <div style="font-size: 16px; font-weight: 600;">
                                    <span style="display: inline-flex; justify-content: center; align-items: center; width: 20px; height: 20px; color: #28B463; background: #D5F5E3; border-radius: 50%;">+</span>
                                    <span>S/.</span>
                                    <span><?php echo number_format($ahorro, 2) ?></span>
                                </div>
                            </div>
                            <div style="flex: 1 1 0%;">
                                <div>Retiro</div>
                                <div style="font-size: 16px; font-weight: 600;">
                                    <span style="display: inline-flex; justify-content: center; align-items: center; width: 20px; height: 20px; color: #E74C3C; background: #FDEDEC; border-radius: 50%;">-</span>
                                    <span>S/.</span>
                                    <span><?php echo number_format($retiro, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <form id="formRegisterAhorro" style="margin-top: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                            <div>
                                <label>Tipo</label>
                                <select class="form-control" name="tipo" id="tipo">
                                    <option value="">Seleccione</option>
                                    <option value="7">Ahorro</option>
                                    <?php if (
                                        $request->user()->tipoU === "1" ||
                                        $request->user()->tipoU === "2" ||
                                        $request->user()->tipoU === "3"
                                    ) { ?>
                                        <option value="8">Retiro</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                <label>Motivo</label>
                                <select class="form-control" name="motivo" id="motivo">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($motivos as $motivo) { ?>
                                        <option value="<?php echo $motivo->idam ?>"><?php echo $motivo->motivo ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                <label>Monto</label>
                                <input type="text" class="form-control" name="monto" id="monto">
                            </div>
                            <div style="display: flex; align-items: end;">
                                <input type="hidden" class="form-control" name="customer_id" value="<?php echo $request->customerId ?>">
                                <button type="button" class="btn btn-secundary" style="margin-right: 10px;" onclick="resetForm()">Cancelar</button>
                                <button type="submit" class="btn btn-primary" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>

                    <div class="tabs-container" style="margin-top: 40px;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1">Movimientos</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Motivo</th>
                                                <th>Usuario</th>
                                                <th>Monto</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($transactions) > 0) { ?>

                                                <?php foreach ($transactions as $transaction) { ?>
                                                    <tr>
                                                        <td><?php echo $transaction->fecha ?></td>
                                                        <td><?php echo $transaction->motivo ?></td>
                                                        <td><?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?></td>
                                                        <td>
                                                            <?php echo $transaction->tipo == '7' ? '+' : '-'  ?>
                                                            <?php echo 'S/ ' . number_format($transaction->monto, 2) ?>
                                                        </td>
                                                        <td>
                                                            <img src="./../public/resource/icons/ticket_cpe.svg" width="30" height="30" alt="" onclick="showVoucher(<?php echo $transaction->idAd ?>)" style="cursor: pointer;">
                                                            <?php if (in_array($request->user()->tipoU, ['1', '2', '3'])) { ?>
                                                                <button class="btn btn-danger" onclick="deleteAhorroTransaction(<?php echo $transaction->idAd ?>)">
                                                                    <i class="glyphicon glyphicon-trash"></i>
                                                                    <span>Anular transacción</span>
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div>No hay nada que mostrar</div>
        <?php } ?>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('tipo').value = '';
        document.getElementById('motivo').value = '';
        document.getElementById('monto').value = '';
    }

    function showVoucher(ahorroTransactionId) {
        window.open(`../app/pdf/voucherAhorro.php?operacion=${ahorroTransactionId}`);
    }

    function deleteAhorroTransaction(ahorroTransactionId) {
        swal({
            title: '¿Anular transacción?',
            text: 'Una ves eliminado la transacción, ya no hay vuelta atras.',
            icon: 'info',
            buttons: ['No, salir', 'Si, anular transacción']
        }).then(value => {
            if (value) {
                deleteTransaction(ahorroTransactionId);
            }
        })
    }

    function deleteTransaction(transactionId) {
        fetch(`../app/api/anularAhorro.php?ahorroTransactionId=${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: 'Transaccción eliminado con éxito!!',
                        icon: 'success'
                    });
                    window.location.reload();
                } else {
                    swal({
                        title: 'Lo sentimos, ocurrio un error al eliminar la transacción.',
                        icon: 'error'
                    })
                }
            });
    }

    document.getElementById('formRegisterAhorro').onsubmit = e => {
        e.preventDefault();

        const formData = new FormData(e.target);

        fetch('../app/api/registrarAhorro.php', {
                method: 'post',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.open(`../app/pdf/voucherAhorro.php?operacion=${data.transactionId}`, 'voucherAhorro', 'width=500,height=850,scrollbars=NO');
                    swal({
                            title: 'Ahorro registrado',
                            icon: 'success'
                        })
                        .then(_ => window.location.reload());
                } else {
                    var myError = ``;
                    for (const key in data.errors) {
                        if (Object.hasOwnProperty.call(data.errors, key)) {
                            const element = data.errors[key];
                            myError += `${element}
                            `;
                        }
                    }

                    swal({
                        title: data.message,
                        text: myError,
                        icon: 'error'
                    });
                }
            });
    }
</script>

<?php
include('footer.php');
?>