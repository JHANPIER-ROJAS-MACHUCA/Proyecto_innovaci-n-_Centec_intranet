<?php

include('head.php');

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\User;

if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '2') {
  echo "<script>location.href='inicio.php'</script>";
}

$perPage = 20;
$currentPage = is_numeric($request->page) ? $request->page : 1;

$fechaDe = $request->fechaDe;
$fechaHasta = $request->fechaHasta;
$userId = $request->userId;

$resume = Credit::whereIn('tprestamo.estado', [4, 5])
  ->when($fechaDe && $fechaHasta, function ($query) use ($fechaDe, $fechaHasta) {
    $query->whereBetween('tprestamo.fechaDesembolso', [$fechaDe, $fechaHasta]);
  })
  ->when($userId, function ($query) use ($userId) {
    $query->where('tprestamo.user_id', $userId);
  })
  ->selectRaw('count(*) as total')
  ->selectRaw('sum(capital) as capital')
  ->first();

$resumen1  = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
  ->selectRaw('sum(tpresta_detalle.interest) as interest')
  ->whereIn('tprestamo.estado', [4, 5])
  ->when($fechaDe && $fechaHasta, function ($query) use ($fechaDe, $fechaHasta) {
    $query->whereBetween('tprestamo.fechaDesembolso', [$fechaDe, $fechaHasta]);
  })
  ->when($userId, function ($query) use ($userId) {
    $query->where('tprestamo.user_id', $userId);
  })
  ->first();


$desembolsos = Credit::with('installments')
  ->select(
    'tprestamo.fechaDesembolso',
    'tprestamo.idP',
    'tprestamo.interest_rate',
    'tprestamo.capital'
  )
  ->selectRaw('concat_ws(" ", tclie_general.ap,tclie_general.am, tclie_general.nom) as customer')
  // ->selectRaw('concat_ws(" ", tusuario.apU,tusuario.amU, tusuario.nomU) as usuario_desembolsado')
  ->selectRaw('concat_ws(" ", funcionario_responsable.apU, funcionario_responsable.amU, funcionario_responsable.nomU) as funcionario_responsable')
  ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
  ->leftJoin('tusuario as funcionario_responsable', 'tprestamo.user_id', 'funcionario_responsable.idU')
  // ->leftJoin('tcaja_usu_detal', function ($join) {
  //   $join->on('tprestamo.idP', 'tcaja_usu_detal.conejo')
  //     ->where('tcaja_usu_detal.tipo', 2)
  //     ->where('tcaja_usu_detal.estadodt', 2);
  // })
  // ->leftJoin('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  // ->leftJoin('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
  ->when($fechaDe && $fechaHasta, function ($query) use ($fechaDe, $fechaHasta) {
    $query->whereBetween('tprestamo.fechaDesembolso', [$fechaDe, $fechaHasta]);
  })
  ->when($userId, function ($query) use ($userId) {
    $query->where('tprestamo.user_id', $userId);
  })
  ->whereIn('tprestamo.estado', [4, 5])
  ->orderBy('tprestamo.fechaDesembolso', 'desc')
  ->orderBy('tprestamo.idP', 'desc')
  ->offset($perPage * ($currentPage - 1))
  ->limit($perPage)
  ->get();

$users = User::active()->whereIn('tipoU', [3, 4])->get();


?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
    </div>
    <h5 style="color:white">Reportes <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div class="tabs-container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1">REPORTES DESEMBOLSOS </a></li>
        <li><a href="reporteDesembolsosResumen.php">RESUMEN DESEMBOLSO</a></li>
      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
          <div class="panel-body">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
              <div class="form-group row">

                <label class="col-md-1 control-label" id="">FECHA DE</label>
                <div class="col-md-2" id="lista1" style="padding-bottom:10px">
                  <input type="date" class="form-control" name="fechaDe" id="txtfechade" value="<?php echo $fechaDe ?>">
                </div>

                <label class="col-md-1 control-label" id="">FECHA HASTA</label>
                <div class="col-md-2" id="lista1" style="padding-bottom:10px">
                  <input type="date" class="form-control" name="fechaHasta" id="txtfechahasta" value="<?php echo $fechaHasta ?>">
                </div>

                <label class="col-md-1 control-label" id="">USUARIO</label>
                <div class="col-md-2" id="lista1" style="padding-bottom:10px">
                  <select class="form-control" name="userId">
                    <option value="">Todos</option>
                    <?php foreach ($users as $user) { ?>
                      <option value="<?php echo $user->idU ?>" <?php echo $user->idU == $userId ? 'selected' : '' ?>>
                        <?php echo $user->apU . ' ' . $user->amU . ' ' . $user->nomU ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <button title="Buscar Cliente" type="submit" class="btn btn-info" style="height: 29px">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </div>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-striped dataTables-example">
                <thead>
                  <tr style="color:#31708f">
                    <th>Cuenta</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Interes</th>
                    <th>Fecha</th>
                    <!-- <th>Usuario desembolsado</th> -->
                    <th>Funcionario responsable</th>
                    <!-- <th>Estado </th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($desembolsos as $key => $desembolso) { ?>

                    <?php
                    $interest = 0;

                    foreach ($desembolso->installments as $installment) {
                      $interest += $installment->interest;
                    }
                    ?>

                    <tr>
                      <td><?php echo $desembolso->idP ?></td>
                      <td><?php echo $desembolso->customer ?></td>
                      <td><?php echo number_format($desembolso->capital / 10, 2) ?></td>
                      <td><?php echo number_format($interest / 10, 2) ?></td>
                      <td><?php echo $desembolso->fechaDesembolso ?></td>
                      <!-- <td><?php echo $desembolso->usuario_desembolsado ?></td> -->
                      <td><?php echo $desembolso->funcionario_responsable ?></td>
                      <!-- <td style="color: <?php echo $desembolso->estadodt === '2' ? 'black' : 'red' ?>"><?php echo $desembolso->estadodt === '2' ? 'DESEMBOLSADO' : 'ANULADO' ?></td> -->
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>

            <div>
              <?php for ($i = 1; $i <= ceil($resume->total / $perPage); $i++) { ?>
                <button class="btn btn-sm <?php echo $currentPage == $i ? 'btn-primary' : 'btn-secundary' ?>" onclick="navigateToPage(<?php echo $i ?>)">
                  <?php echo $i ?>
                </button>
              <?php } ?>
            </div>

            <div class="row" style="margin-top: 30px;">
              <div class="col-md-4">
                <div style="font-weight: bold;">Total desembolso</div>
                <div style="border: 1px solid #B0BEC5; padding: 5px 20px;font-weight: bold; margin-top: 5px;">
                  <?php echo number_format($resume->capital / 10, 2) ?>
                </div>
              </div>
              <div class="col-md-4">
                <div style="font-weight: bold;">Total interes</div>
                <div style="border: 1px solid #B0BEC5; padding: 5px 20px;font-weight: bold; margin-top: 5px;">
                  <?php echo number_format($resumen1->interest / 10, 2) ?>
                </div>
              </div>
              <div class="col-md-4">
                <div style="font-weight: bold;">Total movimiento</div>
                <div style="border: 1px solid #B0BEC5; padding: 5px 20px;font-weight: bold; margin-top: 5px;">
                  <?php echo $resume->total ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="tab-2" class="tab-pane">
          <div class="panel-body">
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  function navigateToPage(page) {
    const query = new URLSearchParams(window.location.search);
    query.set('page', page);

    window.location.href = window.location.pathname + '?' + query.toString();
  }

  function mostrarTodo() {
    window.location.href = window.location.pathname;
  }
</script>

<?php include('footer.php'); ?>