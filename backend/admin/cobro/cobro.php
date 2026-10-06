<?php
include("../conection/bdcredito.php");
extract($_POST);
/*$fe="02/01/2020";
$codi="4";*/
$f = preg_split("~/~", $fe);
$fecha = "$f[2]-$f[1]-$f[0]";
//por cobrar
// $usuario = extraer("select tipoU as dato, idO from tusuario where idU='$codi'");
// $ra = mysqli_fetch_array($usuario);
// $colsulta = "";
// $consul = "";

// $consul = "SELECT idCAD,@id:=cliente,@ca:=tcu.idCA,dni,concat(ap,' ',am,' ',nom)as nom ,(select sum(total) from tcaja_usu_detal where cliente=@id and idCA=@ca and tipo='3')  as total,(select sum(cuota) from tcaja_usu_detal where cliente=@id and idCA=@ca and tipo='3')  as cuota, (select sum(mora) from tcaja_usu_detal where cliente=@id and idCA=@ca and tipo='3') as mora, tcud.created_at FROM tcaja_oficina tof inner join tcaja_usuario tcu on tof.idCO=tcu.idCO inner join tcaja_usu_detal tcud on tcu.idCA=tcud.idCA inner join tclie_general tcg on tcud.cliente=tcg.idCG where tof.ini='$fecha' and tcu.idU='$codi' and total is not null group by cliente";
// if ($ra['dato'] != "4") {
//   $idO = $ra['idO'];
//   $consulta = "select sum(tpd.cuota) as cobro from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and tpd.fechaProg='$fecha' and (tpd.fechaPago='$fecha' or tpd.fechaPago is null)";
// } else {
//   $consulta = "select sum(tpd.cuota) as cobro from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idU='$codi' and tpd.fechaProg='$fecha' and (tpd.fechaPago='$fecha' or tpd.fechaPago is null)";
// }

// $c = extraer($consulta);
// $r = mysqli_fetch_array($c);
// $cobro = $r['cobro'];
// //cobrado
// $co = extraer("SELECT sum(total) as total,sum(cuota) as cuota,sum(mora)as mora FROM tcaja_usuario tcu inner join tcaja_oficina tca on tcu.idCO=tca.idCO inner join tcaja_usu_detal tcud on tcu.idCA=tcud.idCA where tcu.idU='$codi' and ini='$fecha' and tcud.tipo='3'");
// $r2 = mysqli_fetch_array($co);
// $to = da($r2['total']);
// $cuo = da($r2['cuota']);
// $mor = da($r2['mora']);
// function da($valor)
// {
//   $resul = "0.00";
//   if ($valor != "") {
//     $resul = $valor;
//   }
//   return $resul;
// }

// --------------------------------------------------

$consultaUsuarioCobro = "SELECT 
transactions.*, concat(clients.ap, ' ', clients.am, ' ', clients.nom) as client 
FROM tcaja_usu_detal as transactions
INNER JOIN tclie_general as clients ON transactions.cliente=clients.idCG
INNER JOIN tcaja_usuario as cash ON transactions.idCA=cash.idCA
INNER JOIN tcaja_oficina as cash_office ON cash.idCO=cash_office.idCO
WHERE cash.idU = $codi
AND cash_office.ini = '$fecha'
AND transactions.tipo = '3'";

define('TRANSACTION_STATUS_OK', '2');
$sumCuotas = 0;
$sumMoras = 0;
$cobrosDeUsuario = [];
$a = extraer($consultaUsuarioCobro);
while ($cobro = mysqli_fetch_array($a)) {
  $cuotaPagada = explode(',', $cobro['idCuota'])[0];

  if ($cuotaPagada) {
    if ($cobro['estadodt'] === TRANSACTION_STATUS_OK) {
      $sumCuotas += floatval($cobro['cuota']);
      $sumMoras += floatval($cobro['mora']);
    }
  
    $consultaCredit = "SELECT * FROM tpresta_detalle as installments 
    WHERE installments.idPD=$cuotaPagada LIMIT 1";
    $installment = mysqli_fetch_array(extraer($consultaCredit));
  
    $cobro['creditId'] = $installment['idP'];
  
    array_push($cobrosDeUsuario, $cobro);
  }
}

// --------------------------------------------------

$CODIGO_INGRESO = 1;

// creamos la consulta para optener otros ingresos
$consultaOtrosCobros = extraer("SELECT transactions.total, transactions.comentario, transactions.created_at
            FROM tcaja_usu_detal AS transactions
            INNER JOIN tahorro_motivo AS transaction_types ON transaction_types.idam=transactions.tipo 
            INNER JOIN tcaja_usuario AS caja ON transactions.idCA=caja.idCA
            INNER JOIN tcaja_oficina AS caja_oficina ON caja.idCO=caja_oficina.idCO
            WHERE transactions.tipo!=3 
            AND caja.idU=$codi
            AND transactions.estadodt=2
            AND caja_oficina.ini='$fecha'
            AND transaction_types.tipoM=$CODIGO_INGRESO");

$dataOtrosCobros = [];
$totalOtrosIngresos = 0;

while ($fila = mysqli_fetch_array($consultaOtrosCobros)) {
  $totalOtrosIngresos += floatval($fila['total']);
  array_push($dataOtrosCobros, $fila);
}

$montoTotalIngresado =$totalOtrosIngresos;

// le damos formato de moneda
$totalOtrosIngresos = number_format($totalOtrosIngresos, 2);
$montoTotalIngresado = number_format($montoTotalIngresado, 2);

?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="col-sm-3 col-xs-3">
  <label for="">MONTO A COBRAR</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmontoACobrar" id="txtmontoACobrar" value="<?php echo $cobro; ?>">
</div>
<div class="col-sm-3 col-xs-3">
  <label for="">CUOTAS COBRADAS</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtcuotasCobradas" id="txtcuotasCobradas" value="<?php echo $sumCuotas ?>">
</div>
<div class="col-sm-3 col-xs-3">
  <label for="">MORAS COBRADAS</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmoraCobradas" id="txtmoraCobradas" value="<?php echo $sumMoras ?>">
</div>
<div class="col-sm-3 col-xs-3">
  <label for="">TOTAL MONTO COBRADO</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmontoCobrado" id="txtmontoCobrado" value="<?php echo $sumCuotas + $sumMoras ?>">
</div>
<!-- <div class="col-sm-3 col-xs-3">
  <label for="">CUOTAS COBRADAS</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtcuotasCobradas" id="txtcuotasCobradas" value="<?php echo $cuo ?>">
</div>
<div class="col-sm-3 col-xs-3">
  <label for="">MORAS COBRADAS</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmoraCobradas" id="txtmoraCobradas" value="<?php echo $mor ?>">
</div>
<div class="col-sm-3 col-xs-3">
  <label for="">TOTAL MONTO COBRADO</label>
  <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmontoCobrado" id="txtmontoCobrado" value="<?php echo $to ?>">
</div> -->
<div class="col-sm-12 col-xs-12">
  <br>
  <table class="table table-striped dataTables-example">
    <thead>
      <tr style="color:#31708f">
        <th>CLIENTE</th>
        <th>COD CREDITO</th>
        <th>FECHA COBRADA</th>
        <th>MONTO</th>
        <th>CUOTA</th>
        <th>MORA</th>
        <th>ESTADO</th>
        <th>N° Operación</th>
        <th>Comprobante</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($cobrosDeUsuario as $cobro) {
      ?>
        <tr>
          <td><?php echo $cobro['client']; ?></td>
          <td><?php echo $cobro['creditId']; ?></td>
          <td><?php echo $cobro['created_at']; ?></td>
          <td><?php echo 'S/. ' . $cobro['total']; ?></td>
          <td><?php echo 'S/. ' . $cobro['cuota']; ?></td>
          <td><?php echo 'S/. ' . $cobro['mora']; ?></td>
          <td><?php echo $cobro['estadodt'] === '1' ? "<span style='color: red;font-weight:bold;'>ELIMINADO</span>" : "<span style='color: green;font-weight:bold;'>ÉXITO</span>" ?></td>
          <td><?php echo $cobro['idCAD'] ?></td>
          <td><button onclick="openComprobante(<?php echo $cobro['idCAD'] ?>)">Ver comprobante</button></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>

<div>
  <h4>OTROS COBROS</h4>
  <div style="display: flex; justify-content: end;">
    <div>
      <label>TOTAL OTROS INGRESOS</label>
      <input type="text" disabled value="<?php echo $totalOtrosIngresos; ?>" class="form-control" style="text-align:right;color:blue;font-weight:bold" />
    </div>
  </div>
  <div>
    <table class="table table-striped">
      <thead>
        <tr style="border-bottom: 1px solid black">
          <th>Fecha</th>
          <th>Monto</th>
          <th>Descripcion</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($dataOtrosCobros as $cobro) {
        ?>
          <tr>
            <td><?php echo $cobro['created_at'] ?></td>
            <td>S/ <?php echo $cobro['total'] ?></td>
            <td><?php echo $cobro['comentario'] ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<div style="display: flex; justify-content: center; margin-top: 70px;">
  <div>
    <label>MONTO TOTAL COBRADO E INGRESADO</label>
    <input type="text" disabled class="form-control" style="color:blue; font-weight: bold;" value="<?php echo $montoTotalIngresado; ?>">
  </div>
</div>
<br>
<?php /*
  if($ra['dato']!="4")
  {
    $idO=$ra['idO'];
    $consulta="select sum(tpd.cuota) as cobro from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and tpd.fechaProg='$fecha' and (tpd.fechaPago='$fecha' or tpd.fechaPago is null)";
  }
  else
  {
      $consulta="select sum(tpd.cuota) as cobro from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idU='$codi' and tpd.fechaProg='$fecha' and (tpd.fechaPago='$fecha' or tpd.fechaPago is null)";
  }

   ?>
  <div class="col-sm-3 col-xs-3">
    <label for="">TOTAL MONTO NO COBRADO</label>
    <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" name="txtmontoCobrado" id="txtmontoCobrado" value="<?php echo $to ?>">
  </div>
  <div class="col-sm-12 col-xs-12">
  <table class="table table-striped dataTables-example">
      <thead>
      <tr style="color:#31708f">
          <th>CLIENTE</th>
          <th>FECHA PROGRAMADA</th>
          <th>MONTO</th>
          <th>CUOTA</th>
          <th>MORA</th>
      </tr>
      </thead>
      <tbody>
        <?php
        $consula=extraer($consul);
          while($row =mysqli_fetch_array($consula))
          {
            $monto="0.00";
            if($row['cuota']!="")
            {
              $monto=$row['cuota'];
            }
            $mora="0.00";
            if($row['mora']!="")
            {
              $mora=$row['mora'];
            }
        ?>
      <tr>
        <td><?php echo $row['nom']; ?></td>
        <td><?php echo $fe; ?></td>
        <td><?php echo 'S/. '.$row['total']; ?></td>
        <td><?php echo 'S/. '.$monto; ?></td>
        <td><?php echo 'S/. '.$mora; ?></td>
      <!--  <td>
      </td>-->
      </tr>
    <?php } ?>
    </tbody>
  </table>
  */ ?>
</div>

<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    var fecha = $('#txtfecha').val();

    $('.dataTables-example').DataTable({
      pageLength: 15,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        /*{ extend: 'copy'},
        {extend: 'csv'},*/
        {
          extend: 'excel',
          title: 'Cobros ' + fecha
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

  function openComprobante(paymentId) {
    window.open('../app/pdf/voucher.php?operacion=' + paymentId, "voucher",
      "width=600,height=800,scrollbars=NO");
  }
</script>