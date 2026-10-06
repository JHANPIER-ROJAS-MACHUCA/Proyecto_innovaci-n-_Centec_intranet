<?php

use CrediSoporte\Domain\Models\Transaction;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$transactions = Transaction::join('tcaja_usuario', 'tcaja_usuario.idCA', 'tcaja_usu_detal.idCA')
  ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
  ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
  ->join('tahorro_motivo', 'tahorro_motivo.idam', 'tcaja_usu_detal.tipo')
  ->where('tahorro_motivo.tipoM', '2')
  ->whereNull('tahorro_motivo.monto')
  ->whereNull('tahorro_motivo.fecha')
  ->where('tahorro_motivo.estado', '1')
  ->when(!in_array($_COOKIE['tuser'], ['1', '2']), function ($query) {
    $query->where('tcaja_usuario.idU', $_COOKIE['user1']);
  })
  ->orderBy('idCAD', 'desc')
  // ->select('tcaja_usu_detal.idCAD', 'tcaja_usu_detal.idCA', 'tcaja_usu_detal.')
  ->get();
?>

<div class="col-sm-12 table-responsive" style="overflow-y:scroll;height:450px">
  <table class="table table-striped dataTables-example">
    <thead>
      <tr>
        <th>TIPO</th>
        <th>MONTO</th>
        <th>MOTIVO</th>
        <th>USUARIO</th>
        <th>FECHA</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transactions as $transaction) { ?>
        <tr>
          <td style="font-size:12px"><?php echo $transaction->motivo ?></td>
          <td style="font-size:12px"><?php echo "S/. " . $transaction->total ?></td>
          <td style="font-size:12px"><?php echo $transaction->comentario ?></td>
          <td style="font-size:12px"><?php echo $transaction->apU . ' ' . $transaction->amU . ' ' . $transaction->nomU ?></td>
          <td style="font-size:12px"><?php echo date('d/m/Y', strtotime($transaction->created_at)) ?></td>
          <?php if ($transaction->montofin === "") { ?>
            <td>
              <a title="Eliminar solicitud" onclick="elimini(<?php echo $transaction->idCAD ?>)">
                <i class="fa fa-rocket" style="color:red;font-size:25px"></i>
              </a>
            </td>
          <?php } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
