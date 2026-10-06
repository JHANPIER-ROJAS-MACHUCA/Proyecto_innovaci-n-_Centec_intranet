<?php
include("../conection/bdcredito.php");
  $codi=$_REQUEST['id'];
  $fe=$_REQUEST['fecha'];
  //$fe="08/10/2019";
  $f = preg_split("~/~", $fe);
  $fecha="$f[2]-$f[1]-$f[0]";
  //por cobrar
  $c=extraer("select sum(tpd.cuota) as cobro from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idU='$codi' and tpd.fechaProg='$fecha'");
  $r=mysqli_fetch_array($c);
  $cobro=$r['cobro'];
  //cobrado
  $co=extraer("SELECT sum(total) as total,sum(cuota) as cuota,sum(mora)as mora FROM tcaja_usuario tcu inner join tcaja_oficina tca on tcu.idCO=tca.idCO inner join tcaja_usu_detal tcud on tcu.idCA=tcud.idCA where tcu.idU='$codi' and ini='$fecha' and tcud.tipo='3'");
  $r2=mysqli_fetch_array($co);
  $to=$r2['total'];
  $cuo=$r2['cuota'];
  $mor=$r2['mora'];

  echo "$cobro/$to/$cuo/$mor";
 //echo json_encode(array("cobro"=>"$cobro","total"=>"$to","cuota"=>"$cuo","mora"=>"$mor"));

 ?>
