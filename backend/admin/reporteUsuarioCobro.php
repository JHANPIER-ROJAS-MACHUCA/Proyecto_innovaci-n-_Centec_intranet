<?php

use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Models\User;

include('head.php');

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$users = [];

if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2') {
  $users = User::active()->whereIn('tipoU', [2, 3, 4])->get();
} else if ($request->user()->tipoU === '3') {
  $users = User::active()->whereIn('tipoU', [3, 4])->get();
} else {
  $users = User::active()->where('idU', $request->user()->idU)->get();
}

$transactionsCredits = Transaction::join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
  ->join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->join('tcaja_oficina', 'tcaja_oficina.idCO', 'tcaja_usuario.idCO')
  ->where('tcaja_usuario.idU', $request->funcionario ?? $request->user()->idU)
  //->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 3)
  ->where('tcaja_oficina.ini', $request->fecha ?? date('Y-m-d'))
  ->orderBy('tcaja_usu_detal.created_at', 'desc')
  ->offset($perPage * ($currentPage - 1))
  ->limit($perPage)
  ->get();

$idsInstallments = [];
foreach ($transactionsCredits as $transaction) {
  $idCuotas = array_filter(explode(',', $transaction->idCuota));
  $idMoras = array_filter(explode(',', $transaction->idMora));

  $idsMerge = array_merge($idCuotas, $idMoras);

  if (count($idsMerge) === 0) {
    continue;
  }

  $idsInstallments[] = $idsMerge[0];
}

$installments = Installment::whereIn('idPD', $idsInstallments)
  ->select('idPD', 'idP')
  ->get();

$resumen = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->join('tcaja_oficina', 'tcaja_oficina.idCO', 'tcaja_usuario.idCO')
  ->where('tcaja_usuario.idU', $request->funcionario ?? $request->user()->idU)
  //->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 3)
  ->where('tcaja_oficina.ini', $request->fecha ?? date('Y-m-d'))
  ->selectRaw('sum(if(tcaja_usu_detal.estadodt = "2", tcaja_usu_detal.cuota, 0)) as totalCuota')
  ->selectRaw('sum(if(tcaja_usu_detal.estadodt = "2", tcaja_usu_detal.mora, 0)) as totalMora')
  ->selectRaw('count(tcaja_usu_detal.idCAD) as totalRows')
  ->first();

?>


<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
    </div>
    <h5 style="color:white">Cobros <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <h3>COBROS REALIZADOS</h3>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
      <div style="display: flex; flex-wrap: wrap; align-items: end;">
        <div style="margin-right: 20px;">
          <label>Funcionario</label>
          <select class="form-control" name="funcionario">
            <?php $userIdSeledted = $request->funcionario ?? $request->user()->idU ?>

            <?php foreach ($users as $user) { ?>
              <option value="<?php echo $user->idU ?>" <?php echo (string) $user->idU === (string) $userIdSeledted ? 'selected' : '' ?>>
                <?php echo $user->apU . ' ' . $user->amU . ' ' . $user->nomU ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div style="margin-right: 20px;">
          <label>Fecha</label>
          <input type="date" class="form-control" name="fecha" value="<?php echo $request->fecha ?? date('Y-m-d') ?>">
        </div>
        <div>
          <button type="submit" class="btn btn-primary">Buscar</button>
          <a href="<?php echo $_SERVER['PHP_SELF'] ?>" type="submit" class="btn btn-success">Ver mis cobros de hoy</a>
        </div>
      </div>
    </form>

    <div style="display: flex; justify-content: end; margin-top: 20px;">
      <div style="margin-right: 20px;">
        <label>Cobro cuotas</label>
        <input type="text" class="form-control" disabled style="font-weight: 700; color: #229954;" value="S/. <?php echo number_format($resumen->totalCuota, 2) ?>">
      </div>
      <div style="margin-right: 20px;">
        <label>Cobro moras</label>
        <input type="text" class="form-control" disabled style="font-weight: 700; color: #229954;" value="S/. <?php echo number_format($resumen->totalMora, 2) ?>">
      </div>
      <div>
        <label>Total cobros</label>
        <input type="text" class="form-control" disabled style="font-weight: 700; color: #229954;" value="S/. <?php echo number_format($resumen->totalCuota + $resumen->totalMora, 2) ?>">
      </div>
    </div>

    <div style="overflow-x: auto; margin-top: 30px;">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>CLIENTE</th>
            <th>COD CREDITO</th>
            <th>HORA COBRO</th>
            <th>MONTO</th>
            <th>CUOTA</th>
            <th>MORA</th>
            <th>ESTADO</th>
            <th>N° OPERACION</th>
            <th style="width: 1px;">COMPROBANTE</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transactionsCredits as $transaction) { ?>
            <tr>
              <td><?php echo $transaction->ap . ' ' . $transaction->am . ' ' . $transaction->nom ?></td>
              <td><?php echo str_pad($transaction->conejo, 5, '0', STR_PAD_LEFT) ?></td>
              <td><?php echo date('h:i A', strtotime($transaction->created_at)) ?></td>
              <td style="text-align: rigth;"><?php echo number_format($transaction->total, 2) ?></td>
              <td style="text-align: rigth;"><?php echo number_format($transaction->cuota, 2) ?></td>
              <td style="text-align: rigth;"><?php echo number_format($transaction->mora, 2) ?></td>
              <td>
                <?php
                if ($transaction->estadodt === '2') { ?>
                  <span style="color: #229954; font-weight: 700;">ÉXITO</span>
                <?php } else if ($transaction->estadodt === '1') { ?>
                  <span style="color: #E74C3C; font-weight: 700;">ANULADO</span>
                <?php } ?>
              </td>
              <td><?php echo str_pad($transaction->idCAD, 5, '0', STR_PAD_LEFT) ?></td>
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
      <?php for ($i = 1; $i <= ceil($resumen->totalRows / $perPage); $i++) { ?>
        <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
          <?php echo $i ?>
        </button>
      <?php } ?>
    </div>

  </div>
</div>

<script>
  function openComprobante(paymentId) {
    window.open('../app/pdf/voucher.php?operacion=' + paymentId, "voucher",
      "width=600,height=800,scrollbars=NO");
  }

  function navigateToPage(page) {
    const query = new URLSearchParams(window.location.search);
    query.set('page', page);

    window.location.href = window.location.pathname + '?' + query.toString();
  }
</script>




<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>
