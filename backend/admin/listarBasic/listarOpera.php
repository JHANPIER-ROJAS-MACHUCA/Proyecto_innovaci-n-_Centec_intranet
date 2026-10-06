<?php
include("../conection/bdcredito.php");
extract($_POST);

$startD = isset($start) && !empty($start) ? $start : '2020-01-01';
$endD = isset($end) && !empty($end) ? $end : date('Y-m-d');

// $startD = date('Y-m-d', strtotime('2022-06-27'));
// $endD = date('Y-m-d', strtotime('2022-06-27'));

if (isset($buscar)) {
  $consulta = "SELECT 
    idCAD, 
    tcud.tipo, 
    total, 
    cuota, 
    idCuota, 
    mora, 
    idMora, 
    tcud.comentario,
    concat(ap,' ',am,' ',nom) as dato,
    concat(apU,' ',amU,' ',nomU) as usu,
    ini,
    tcud.estadodt
    FROM tcaja_usuario tcu 
    inner join tcaja_usu_detal tcud on tcu.idCA=tcud.idCA 
    inner join tclie_general tc on tcud.cliente=tc.idCG 
    inner join tusuario tus on tcu.idU=tus.idU 
    inner join tcaja_oficina tco on tcu.idCO=tco.idCO 
    where (tc.ap like '%$buscar%' or tc.am like '%$buscar%' or tc.nom like '%$buscar%' or idCAD like '%$buscar%') 
    and date(tcud.created_at) BETWEEN '$startD' AND '$endD'
    and tcud.tipo!='1' 
    and estadodt='2' 
    and (habilitacion='4' or habilitacion is null) 
    order by idCAD desc 
    limit 100";
} else {
  $consulta = "SELECT 
    idCAD, 
    tcud.tipo, 
    total, 
    cuota, 
    idCuota, 
    mora, 
    idMora, 
    tcud.comentario,
    concat(ap,' ',am,' ',nom) as dato,
    concat(apU,' ',amU,' ',nomU) as usu,
    ini,
    tcud.estadodt 
    FROM tcaja_usuario tcu 
    inner join tcaja_usu_detal tcud on tcu.idCA=tcud.idCA 
    inner join tclie_general tc on tcud.cliente=tc.idCG 
    inner join tusuario tus on tcu.idU=tus.idU 
    inner join tcaja_oficina tco on tcu.idCO=tco.idCO 
    where tcud.tipo!='1'
    and date(tcud.created_at) BETWEEN '$startD' AND '$endD'
    and estadodt='2' 
    and (habilitacion='4' or habilitacion is null) 
    order by idCAD desc limit 100";
}


$dta = extraer($consulta);
?>
<div class="col-sm-12 table-responsive" style="overflow:scroll;height:350px">
  <table class="table table-striped">
    <thead>
      <tr style="background:<?php echo $jua1['color'] ?> ;color:white;text-align:center">
        <th>
          COD
        </th>
        <th>
          CLIENTE
        </th>
        <th>
          TIPO
        </th>
        <th>
          SUB TOTAL
        </th>
        <th>
          MORA
        </th>
        <th>
          TOTAL
        </th>
        <th>
          USUARIO
        </th>
        <th>
          FECHA
        </th>
        <th>

        </th>
      </tr>
    </thead>
    <tbody style="font-size:10px">
      <?php
      while ($row = mysqli_fetch_array($dta)) {
        $f = preg_split("~-~", $row['ini']);
        $fecha = $f[2] . "/" . $f[1] . "/" . $f[0];
        $pto = "";
        switch ($row['tipo']) {
          case 1:
            $pto = "DESIGNACIÓN";
            break;
          case 2:
            $pto = "DESEMBOLSO";
            break;
          case 3:
            $pto = "COBRO";
            break;
          case 4:
            $pto = "GASTO ADMINISTARTIVO";
            break;
        }
      ?>
        <tr>
          <td><?php echo $row['idCAD'] ?></td>
          <td><?php echo $row['dato'] ?></td>
          <td><?php echo $pto; ?></td>
          <td><?php echo "S/. " . $row['cuota'] ?></td>
          <td><?php echo "S/. " . $row['mora'] ?></td>
          <td><strong><?php echo "S/. " . $row['total'] ?></strong></td>

          <td><?php echo $row['usu'] ?></td>
          <td><?php echo $fecha; ?></td>
          <td>
            <?php
            if ($row['estadodt'] == '2') {
            ?>
              <a title="Extornar operación" onclick="extor(<?php echo $row['idCAD'] ?>)"><i class="fa fa-rocket" style="color:red;font-size:25px"></i></a>
            <?php
            }
            ?>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>
<?php

?>