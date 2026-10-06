<?php

include('head.php');

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\User;

if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '2') {
  echo "<script>location.href='inicio.php'</script>";
}

$fechaDe = $request->fechaDe;
$fechaHasta = $request->fechaHasta;
$userId = $request->userId;

$users = User::active()->whereIn('tipoU', [3, 4])->get();

$total = 0;

$credits = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
  ->leftJoin('tusuario', 'tusuario.idU', 'tprestamo.user_id')
  ->when($request->fechaDe && $request->fechaHasta, function ($query) use ($request) {
    $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['fechaDe', 'fechaHasta']));
  })
  ->whereIn('tprestamo.estado', ['4', '5'])
  ->groupBy('tprestamo.user_id')
  ->groupBy('tprestamo.idP')
  ->orderBy('tusuario.idU', 'desc')
  // ->selectRaw('count(*) as total')
  // ->selectRaw('count(distinc tprestamo.idP) as operations')
  ->select('tprestamo.user_id')
  ->selectRaw('tprestamo.capital')
  ->selectRaw('sum(tpresta_detalle.interest) as interest')
  ->selectRaw('tprestamo.interest_rate')
  ->selectRaw('concat_ws(" ",tusuario.apU, tusuario.amU, tusuario.nomU) as funcionario')
  ->get();

$nex = $credits->groupBy('funcionario');
// echo json_encode($nex);
// die();

$resume1 = Credit::select('user_id')->selectRaw('sum(tprestamo.capital) as capital')
  ->selectRaw('avg(interest_rate) average_interest_rate')
  ->when($request->fechaDe && $request->fechaHasta, function ($query) use ($request) {
    $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['fechaDe', 'fechaHasta']));
  })
  ->whereIn('tprestamo.estado', ['4', '5'])
  ->groupBy('tprestamo.user_id')
  ->get();

$resumen2 = Credit::selectRaw('sum(tpresta_detalle.interest) as interest')
  ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
  ->when($request->fechaDe && $request->fechaHasta, function ($query) use ($request) {
    $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['fechaDe', 'fechaHasta']));
  })
  ->whereIn('tprestamo.estado', ['4', '5'])
  ->groupBy('tprestamo.user_id')
  ->get();


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
        <li><a href="reporteDesembolsosTodos.php">REPORTES DESEMBOLSOS </a></li>
        <li class="active"><a data-toggle="tab" href="#tab-2">RESUMEN DESEMBOLSO</a></li>
      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane">
        </div>

        <div id="tab-2" class="tab-pane active">
          <div class="panel-body">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
              <div style="display: flex; align-items: end;">
                <div style="margin-right: 30px;">
                  <label>De</label>
                  <input type="date" name="fechaDe" value="<?php echo $request->fechaDe ?>" class="form-control">
                </div>
                <div style="margin-right: 30px;">
                  <label>Hasta</label>
                  <input type="date" name="fechaHasta" value="<?php echo $request->fechaHasta ?>" class="form-control">
                </div>
                <div>
                  <button class="btn btn-primary" style="margin-right: 10px;">Buscar</button>
                  <button class="btn btn-info" type="button" onclick="mostrarTodo()">Mostrar todos</button>
                </div>
              </div>
            </form>

            <div style="margin-top: 30px;">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>FUNCIONARIO</th>
                    <th>MONTO DESEMBOLSADO</th>
                    <th>INTERES GENERADO</th>
                    <th>N° OPERACIONES</th>
                    <th>TASA</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sumCount = 0;
                  $sumCapital = 0;
                  $sumInterest = 0;
                  $sumInterestRate = 0;
                  ?>
                  <?php foreach ($nex as $key => $credits) { ?>
                    <?php
                    $count = 0;
                    $capital = 0;
                    $interest = 0;
                    $interest_rate = 0;
                    foreach ($credits as $keyItem => $item) {
                      $count++;  
                      $capital += $item->capital;
                      $interest += $item->interest;
                      $interest_rate += $item->interest_rate;
                    }

                    $sumCount += $count;
                    $sumCapital += $capital;
                    $sumInterest += $interest;
                    $sumInterestRate += $interest_rate;

                    ?>
                    <tr>
                      <td><?php echo $key === '' ? 'OTROS' : $key ?></td>
                      <td><?php echo number_format($capital / 10, 2) ?></td>
                      <td><?php echo number_format($interest / 10, 2) ?></td>
                      <td><?php echo $count ?></td>
                      <td><?php echo $count > 0 ? round($interest_rate / $count, 2) : 0 ?>%</td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr style="background: black; color:white;">
                    <td style="background: black; color: white;">AGENCIA</td>
                    <td><?php echo number_format($sumCapital / 10, 2) ?></td>
                    <td><?php echo number_format($sumInterest / 10, 2) ?></td>
                    <td><?php echo $sumCount ?></td>
                    <td><?php echo $sumCount > 0 ? round($sumInterestRate / $sumCount, 2) : 0 ?>%</td>
                  </tr>
                </tfoot>
              </table>
            </div>
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