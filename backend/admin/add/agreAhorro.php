<?php
include("../conection/bdcredito.php");
//extraer todos los inputs
extract($_POST);
$usuario=$_COOKIE['user1'];
/*
$Atipo='7';
$lstmotivo="11/3.00";
$Aid="24";
$Ati="1";
$Afecha="27/12/2019";
$Amonto="3.50";*/
function recoger($id1,$id2,$moti)
{
  $resul="";
  $conteo=extraer("select count(*) as con,idA from tahorro where tipoA='$id1' and id='$id2'");//" and motivo='$moti'");
  $row =mysqli_fetch_array($conteo);
  if($row['con']=='0')
  {
    enviar("INSERT INTO tahorro (tipoA,id,motivo) VALUES ('$id1','$id2','$moti')");
    $dea=extraer("SELECT idA as dea from tahorro order by idA desc limit 1");
    $row1=mysqli_fetch_array($dea);
    $resul=$row1['dea'];
  }
  else
  {
    $resul=$row['idA'];
  }
  return $resul;
}
$fe=preg_split("~/~", $Afecha);
$fec=$fe['2'].'-'.$fe['1'].'-'.$fe['0'];
//convertimos de cadena a tipo date
$Afecha = date("Y-m-d",strtotime($fec));
//verificamos si existe o no el ahorro si no existe creamos el ahorro
$moti=preg_split("~/~",$lstmotivo);
$moti=$moti[0];
$idAhorro=recoger($Ati,$Aid,$moti);


enviar("INSERT INTO tahorro_deta (idA, monto, fecha, tipo,moti, idU) VALUES ('$idAhorro','$Amonto','$Afecha','$Atipo','$moti','$usuario')");

$dear=extraer("SELECT idAd as ide from tahorro_deta order by idAd desc limit 1");
$row=mysqli_fetch_array($dear);

$idU=$_COOKIE['user1'];

$oper=registraOperacion($idU,$moti,$Aid,$Amonto,$Amonto,$row["ide"]);
function registraOperacion($id,$tipo,$cliente,$monto,$total,$identificador)
{
  $datoC=extraer("SELECT idCA as id FROM tcaja_usuario where idU='$id' order by idCA desc limit 1");
  $r=mysqli_fetch_array($datoC);
  if($_COOKIE['tuser']=='2')
  {
    //vemos la caja abierta
    $ido=$_COOKIE['tofi'];
    $fs=extraer("SELECT idCO FROM tcaja_oficina where idO='$ido' order by idCO desc limit 1");
    $re=mysqli_fetch_array($fs);
    $ofi=$re['idCO'];

    $datoC=extraer("SELECT idCA as id FROM tcaja_usuario where idCO='$ofi' order by idCA asc limit 1");
    $r=mysqli_fetch_array($datoC);
  }
  $idCA=$r['id'];
  /*
  echo $idCA." 1<br>";
  echo $tipo." 2<br>";
  echo $monto." 3<br>";
  echo $identificador." 4<br>";
  echo $total." 5<br>";
  echo $cliente." 6<br>";*/
  enviar("INSERT INTO tcaja_usu_detal (idCA,tipo,cuota,idCuota,total,cliente) VALUES ('$idCA','$tipo','$monto','$identificador','$total','$cliente')");

}

?>
