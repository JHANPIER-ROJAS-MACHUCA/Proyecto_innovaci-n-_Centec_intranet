<?php
include('../conection/bdcredito.php');
extract($_POST);
/*$inicio="23/12/2019";
$canti="100";
$id="12";*/

$fecha=convert($inicio);
enviar("UPDATE tahorro_motivo SET fecha= '$inicio',dias='$canti' WHERE idam='$id'");
$inicial=verificador($id);
//echo $inicial."<br>";
$com="1 days";

//calculamos la fecha final
//contamos la diferecnia de Meses
$mesesin=0;
$meca="0";
for ($i=0; $i <$canti; $i++)
    {
      $fecha=date("Y/m/d",strtotime($fecha."-".$com));
      agregarMes($id,$fecha);
      $mes=preg_split("~/~",$fecha);
      $dia=date("w", strtotime($fecha));
      $limitador=despejarDias($id,$mes[1],$dia);
      if($meca!=$mes[1])
      {
        $meca=$mes[1];
        //echo $meca."<br>";
        $mesesin++;
      }
      if(!$limitador)
      {
        $i--;
      }
      else
      {
        //  echo $fecha."  ".$i."<br>";
      }
    }
$fecha=convert($fecha);
$finalizacion=$mesesin;
//echo $finalizacion."<br>";
//devolvemos el valor
echo json_encode(array("fecha1"=>$fecha));

//procedemos a quitar los meses que no necesitamos
eliMeses($id,$inicial,$finalizacion);
function eliMeses($id,$ini,$fin)
{
  $resul;
  if($ini>$fin)
  {
    $valor=$ini-$fin;
    for ($i=0; $i < $valor; $i++)
    {
      $veri=extraer("SELECT idamd as id FROM tahorro_motivo_deta where idam='$id' order by idamd desc limit 1");
      $row =mysqli_fetch_array($veri);
      $iden=$row['id'];
      enviar("DELETE FROM tahorro_motivo_deta WHERE idamd='$iden'");
    }
    $resul=$valor;
  }
  return $resul;
}

//realizamos la funcion para determinar que dias no se incluiran
function despejarDias($id,$mes,$dia)
{
  $resul=false;
  $veri=extraer("SELECT domingo as dom,lunes as lu, martes as me, miercoles as mi, jueves as ju, viernes as vi, sabado as sa  FROM tahorro_motivo_deta where idam='$id' and mes='$mes'");
  $row =mysqli_fetch_array($veri);
  if($dia=='0')
  {
    if($row[0]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='1')
  {
    if($row[1]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='2')
  {
    if($row[2]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='3')
  {
    if($row[3]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='4')
  {
    if($row[4]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='5')
  {
    if($row[5]=='1')
    {
      $resul=true;
    }
  }
  elseif ($dia=='6')
  {
    if($row[6]=='1')
    {
      $resul=true;
    }
  }
return $resul;
}

//funcion para verificar y agregar el mes de no estar registrado
function agregarMes($id,$fecha)
{
  //para hacer recores especificos
  $fe=preg_split("~/~",$fecha);
  $mes=$fe[1];
  $veri=extraer("SELECT count(*) as dato FROM tahorro_motivo_deta where idam='$id' and mes='$mes'");
  $row =mysqli_fetch_array($veri);
  if($row['dato']=="0")
  {
    enviar("INSERT INTO tahorro_motivo_deta (idam,mes) VALUES ('$id','$mes')");
  }
}
//funcion para invertir el formato de la fecha
function convert($dato)
{
  $fe=preg_split("~/~",$dato);
  $fecha="$fe[2]/$fe[1]/$fe[0]";
  return  $fecha;
}
//function eliminacion de Meses
function verificador($id)
{
  $re=extraer("SELECT count(*) as dato from tahorro_motivo_deta where idam='$id'");
  $row=mysqli_fetch_array($re);
  $resul=$row['dato'];
  return $resul;
}
 ?>
