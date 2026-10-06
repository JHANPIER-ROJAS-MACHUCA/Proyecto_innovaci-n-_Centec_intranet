  <?php

  use CrediSoporte\Domain\Request\Request;

  require_once '../../vendor/autoload.php';
  require_once '../../src/Domain/Database/bootstrap.php';

  $request = new Request();

  $cash = $database->table('tcaja_oficina')
    ->where('idO', $_COOKIE['tofi'])
    ->orderBy('idCO', 'desc')
    ->first();

  $cashes = $database->table('tcaja_usuario')
    ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
    ->where('tcaja_usuario.idCO', $cash->idCO)
    ->whereNull('tcaja_usuario.tipo')
    ->get();

  ?>
  <div class="col-md-12 table-responsive">
    <div class="">
      <div class="col-sm-2 col-md-2">
        <label>Saldo: S/.</label>
      </div>
      <div class="col-sm-3 col-md-3">
        <input type="text" class="form-control" id="montoCierre" style="text-align:center" disabled name="" value="<?php echo $cashes->sum('montofin') ?>">
      </div>
      <div class="col-sm-4 col-md-4" style="padding-bottom:15px">
        <?php if ($cashes->whereNull('montofin')->count() === 0 && $cash->montof === null) { ?>
          <a class="btn btn-danger" href="cerrarAdmin.php">CERRAR CAJA</a>
          <!--<button type="button" name="button" class="btn btn-info" onclick="cerrarOficina()">CERRAR CAJA</button>-->
        <?php } ?>
      </div>
    </div>


    <table class="table table-striped">
      <thead>
        <tr style="background:<?php echo $jua1['color'] ?> ;color:white;text-align:center">
          <th>
            USUARIO
          </th>
          <th>
            INICIO CAJA
          </th>
          <th>
            HORA INICIO
          </th>
          <th>
            CIERRE DE CAJA
          </th>
          <th>
            HORA CIERRE
          </th>
          <th>OPCIONES</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cashes as $item) { ?>
          <tr>
            <td><?php echo "$item->apU $item->amU $item->nomU" ?></td>
            <td>S/. <?php echo number_format($item->montoIni, 2) ?></td>
            <td><?php echo $item->hini ?></td>
            <td><?php echo $item->montofin > 0 ? 'S/. ' . number_format($item->montofin, 2) : '' ?></td>
            <td><?php echo $item->hfin ?></td>
            <td>
              <?php if ($cash->montof === null && $item->montofin > 0) { ?>
                <button class="btn btn-sm btn-info" type="button" name="button" onclick="abrirCaja(<?php echo  $item->idCA ?>)">Abrir Caja</button>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>