<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$cashGerente = $database->table('tcaja_oficina')
  ->whereNull('montof')
  ->where('idO', 'CG')
  ->first();

if (is_null($cashGerente)) {
  die();
}

$cashOffices = $database->table('tcaja_oficina')
  ->join('tusuario', 'tcaja_oficina.idR', 'tusuario.idU')
  ->join('toficina', 'tcaja_oficina.idO', 'toficina.idO')
  ->where('tcaja_oficina.idO', '!=', 'CG')
  ->where('tcaja_oficina.ini', $cashGerente->ini)
  ->get();
?>

<div class="col-md-12 table-responsive">
  <div class="">
    <div class="col-sm-2 col-md-2">
      <label>Saldo: S/. </label>
    </div>
    <div class="col-sm-3 col-md-3">
      <input type="text" class="form-control" id="montoCierre" style="text-align:center" disabled name="" value="<?php echo $cashGerente->monto ?>">
    </div>
  </div>
  <table class="table table-striped">
    <thead>
      <tr style="background:<?php echo $jua1['color'] ?> ;color:white;text-align:center">
        <th>OFICINA</th>
        <th>RESPONSABLE</th>
        <th>INICIO CAJA</th>
        <th>HORA INICIO</th>
        <th>MONTO</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cashOffices as $cashOffice) { ?>
        <tr>
          <td><?php echo $cashOffice->direccion ?></td>
          <td><?php echo $cashOffice->apU . ' ' . $cashOffice->amU . ' ' . $cashOffice->nomU ?></td>
          <td><?php echo $cashOffice->ini ?></td>
          <td><?php echo $cashOffice->iniH ?></td>
          <td><?php echo $cashOffice->monto ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
