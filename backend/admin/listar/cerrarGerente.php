<?php
require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$numberCashOpenOffices = $database->table('tcaja_oficina')
  ->whereNull('montof')
  ->where('idO', '!=', 'CG')
  ->count();

$date = !empty($_POST['fecha']) ? explode('/', $_POST['fecha']) : [];

$fecha = date('Y-m-d');
if (count($date) === 3) {
  $date = $date[2] . '-' . $date[1] . '-' . $date[0];
}

$saldo = $database->table('tcaja_oficina')
  ->where('idO', '!=', 'CG')
  ->where('ini', $date)
  ->sum('montof');

$cashGerente = $database->table('tcaja_oficina')
  ->whereNull('montof')
  ->where('idO', 'CG')
  ->orderBy('idCO', 'desc')
  ->first();

$cashOffices = $database->table('tcaja_oficina')
  ->join('tusuario', 'tcaja_oficina.idR', 'tusuario.idU')
  ->join('toficina', 'tcaja_oficina.idO', 'toficina.idO')
  ->where('tcaja_oficina.idO', '!=', 'CG')
  ->where('tcaja_oficina.ini', $fecha)
  ->limit(1)
  ->get();

$montoFin = $cashOffices->sum('montof');

?>

<div class="row">
  <div class="col-sm-2 col-md-2">
    <label>Saldo: S/.</label>
  </div>
  <div class="col-sm-3 col-md-3">
    <input type="text" class="form-control" id="montoCierre" style="text-align:center" disabled name="" value="<?php echo $saldo ?>">
    <input type="hidden" class="form-control" id="montoCierre1" style="text-align:center" disabled name="" value="<?php echo $saldo ?>">
  </div>
  <div class="col-sm-4 col-md-4" style="padding-bottom:15px">
    <?php if (!is_null($cashGerente) && $montoFin !== 0) { ?>
      <button type="button" name="button" class="btn btn-info" onclick="cerrarEmpresa()">CERRAR CAJA</button>
    <?php } ?>
  </div>
</div>

<div class="col-md-12 table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>OFICINA</th>
        <th>RESPONSABLE</th>
        <th>INICIO CAJA</th>
        <th>HORA INICIO</th>
        <th>MONTO</th>
        <th>FIN</th>
        <th>HORA FIN</th>
        <th>MONTO FIN</th>
        <th></th>
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
          <td><?php echo $cashOffice->fin ?></td>
          <td><?php echo $cashOffice->finH ?></td>
          <td><?php echo $cashOffice->montof ?></td>
          <td>
            <?php if (!is_null($cashGerente) and $cashOffice->montof > 0) { ?>
              <form method="get" action="javascript:abrir('<?php echo $cashOffice->idCO ?>')">
                <button type="submit" class="btn btn-success btn-xs">Abrir Caja</button>
              </form>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>