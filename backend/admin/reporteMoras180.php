<?php include('head.php');
if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '2' && $_COOKIE['tuser'] != '3') {
  echo "<script>location.href='index.php'</script>";
  //echo "<script>alert(".$_COOKIE['tuser'].")</script>";

}

$diasDeAtrazo = isset($_GET['dias_atrazo']) && !empty($_GET['dias_atrazo']) ? $_GET['dias_atrazo'] : '';
$atrazoInicio = 0; // en dias
$atrazoFin = 180;

if ($diasDeAtrazo) {
  $a = explode('-', $diasDeAtrazo);
  if (count($a) === 2) {
    $value1 = intval($a[0]);
    $value2 = intval($a[1]);

    $atrazoInicio = $value1;
    if ($value2 > 0) {
      $atrazoFin = $value2;
    }
  }
}

$currentDate = date('Y-m-d');
$fechaHace180DiasAtras = date('Y-m-d', strtotime($currentDate . '-180 days'));
$morosos = [];

$consultaMorosos = extraer("SELECT 
concat(clientes.ap, ' ', clientes.am, ' ',clientes.nom) AS cliente_nombre,
clientes.dni,
clientes.direc,
clientes.cel,
creditos.idP,
creditos.fechaDesembolso,
creditos.montoAprovado,
creditos.taza,
creditos.n_cuota,
creditos.mora,
cuotas.ncuota,
cuotas.fechaProg,
concat(operadores.nomU, ' ', operadores.amU, ' ', operadores.apU) as operador,
COUNT(if(cuotas.tfechaMora is null, cuotas.pagoMora , null)) as cantidadMorasGeneradas,
MIN(cuotas.fechaProg) as inicioCuota,
MAX(cuotas.fechaProg) as finCuota,
SUM(cuotas.montoPagado) as totalPagadoCuota, 
SUM(cuotas.pagoMora) as totalGeneradoMora
-- @id:=creditos.idP as idP,
-- (SELECT sum(quotas.montoPagado) FROM tpresta_detalle quotas WHERE quotas.idP=@id AND quotas.estado IS NOT NULL) as totalCancelado
FROM tpresta_detalle cuotas
INNER JOIN tprestamo creditos ON cuotas.idP=creditos.idP
INNER JOIN tclie_general clientes ON creditos.idCG=clientes.idCG
INNER JOIN tusuario operadores ON clientes.idU=operadores.idU
-- WHERE (cuotas.estado IS NULL OR cuotas.estado=2)
AND creditos.estado=4
-- AND DATE(DATE_ADD(cuotas.fechaProg, INTERVAL 10 DAY))<'$currentDate'
-- AND DATE(cuotas.fechaProg)<'$fechaHace180DiasAtras'
GROUP BY cuotas.idP");

$total_MontoPorCobrar = 0;
$total_MoraPorCobrar = 0;
$total_PorCobrar = 0;
$total_CreditoMorosos = 0;

// $mifecha = date('y-m-d', strtotime('2020-02-01' . '+809 days'));
// echo $mifecha;
// die();

// $diasPasados = (strtotime($currentDate) - strtotime('2020-02-01')) / 86400;
// echo $diasPasados;
// die();

while ($fila = mysqli_fetch_object($consultaMorosos)) {
  $diasPasados = (strtotime($currentDate) - strtotime($fila->finCuota)) / 86400;
  
  if ($diasPasados < 0) {
    $fila->diasPasados = $fila->cantidadMorasGeneradas;
  }else{
    $fila->diasPasados = $diasPasados + $fila->cantidadMorasGeneradas;
  }

  $fila->moraAPagarEs = $fila->diasPasados * $fila->mora;

  if ($diasPasados < 180) {
    continue;
  }

  $interes = $fila->montoAprovado * $fila->taza / 100;
  $montoAPagar = $fila->montoAprovado + $interes;
  $deutaActual = floatval($montoAPagar) - floatval($fila->totalPagadoCuota);

  $total_MontoPorCobrar += $deutaActual;
  $total_MoraPorCobrar += $fila->moraAPagarEs;
  $total_CreditoMorosos++;

  array_push($morosos, $fila);
}
// die();
$total_PorCobrar = $total_MontoPorCobrar + $total_MoraPorCobrar

?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">


    </div>
    <h5 style="color:white">Reportes <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" id="crack">

    <table class="table table-striped" id="reporteMora">
      <thead>
        <tr>
          <th>DNI</th>
          <th>Cliente</th>
          <th>Direccion</th>
          <th>Celular</th>
          <th>Fecha desembolso</th>
          <th>Monto aprobado</th>
          <th>Taza</th>
          <th>Interes</th>
          <th>Pendiente</th>
          <th>Pagado</th>
          <th>Dias atraso</th>
          <th>Mora</th>
          <th>Operador</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($morosos as $moroso) {
          $interes = $moroso->montoAprovado * $moroso->taza / 100;
          $montoAPagar = $moroso->montoAprovado + $interes;
          $deutaActual = floatval($montoAPagar) - floatval($moroso->totalPagadoCuota);
          $diaDeRetrazo = floatval($moroso->totalGeneradoMora) / floatval($moroso->mora);
        ?>
          <tr>
            <td><?php echo $moroso->dni ?></td>
            <td><?php echo $moroso->cliente_nombre ?></td>
            <td><?php echo $moroso->direc ?></td>
            <td><?php echo $moroso->cel ?></td>
            <td><?php echo $moroso->fechaDesembolso ?></td>
            <td><?php echo $moroso->montoAprovado ?></td>
            <td><?php echo $moroso->taza . ' %' ?></td>
            <td><?php echo $interes ?></td>
            <td><?php echo number_format($deutaActual, 2) ?></td>
            <td><?php echo number_format($moroso->totalPagadoCuota, 2) ?></td>
            <td><?php echo $moroso->diasPasados . ' dias'; ?></td>
            <td><?php echo $moroso->moraAPagarEs; ?></td>
            <td><?php echo $moroso->operador; ?></td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>

    <div style="margin-top: 20px;display: grid; grid-template-columns: repeat(4, 1fr);gap: 50px;">
      <div>
        <label>Monto por cobrar</label>
        <input type="text" disabled class="form-control" value="<?php echo $total_MontoPorCobrar ?>">
      </div>
      <div>
        <label>Mora por cobrar</label>
        <input type="text" disabled class="form-control" value="<?php echo $total_MoraPorCobrar ?>">
      </div>
      <div>
        <label>Total a cobrar</label>
        <input type="text" disabled class="form-control" value="<?php echo $total_PorCobrar ?>">
      </div>
      <div>
        <label>Cantidad de credito</label>
        <input type="text" disabled class="form-control" value="<?php echo $total_CreditoMorosos ?>">
      </div>
    </div>
  </div>
</div>
<script src="extra/min.js"></script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    $('#reporteMora').DataTable({
      pageLength: 25,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        /*{ extend: 'copy'},
        {extend: 'csv'},*/
        {
          extend: 'excel',
          title: 'Deudores '
        },
        /*{extend: 'pdf', title: 'ExampleFile'},*/
        {
          extend: 'print',
          customize: function(win) {
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', 'inherit');
          }
        }
      ]
    });
  });
</script>

<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>