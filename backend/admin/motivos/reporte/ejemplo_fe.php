<?php
$fecha="2019/09/05";
$plazo='20';
$com="1 days";
$pago='1';
for ($i=1; $i <= $plazo; $i++)
{
  $fecha=date("Y/m/d",strtotime($fecha."+".$com));
  $dia=date("w", strtotime($fecha));
  if($pago=='1')
  {
    if($dia=='0')
    {
      $i--;
    }
    else
    {
      if(feriados($fecha)=='1')
      {
        $i--;
      }
      else
      {
        if($plazo==$i)
        {
          echo $fecha."<br>";
        }
        else
        {
          echo $fecha."<br>";
        }
      }
    }
  }
  else
  {
    if($plazo==$i)
    {
      echo $fecha."<br>";
    }
    else
    {
      echo $fecha."<br>";
    }
  }
}


function feriados($fec)
{
/*$resul='0';
$fec1 = preg_split("~-~", $fec);
$fec="$fec1[0]/$fec1[1]";
$info=extraer("SELECT COUNT(*) AS con from tfecha where fecha='$fec'");
while($row =mysqli_fetch_array($info))
{
$dato=$row['con'];
}
if(!empty($dato))
{
$resul='1';
}
return $resul;*/
$fec2=preg_split("~/~",$fec);
$fechi="$fec2[2]/$fec2[1]";
$año=$fec2[0];
//comprende lo saños de 1970 a 2037
$sema=date("Y/m/d", easter_date($año));

$santa=date("d/m",strtotime($sema."-"."3 days"));
$santa2=date("d/m",strtotime($sema."-"."2 days"));
$resul=0;
if($santa==$fechi){$resul=1;}
else if($santa2==$fechi){$resul=1;}
else if('01/01'==$fechi){$resul=1;}
else if('01/05'==$fechi){$resul=1;}
else if('29/06'==$fechi){$resul=1;}
else if('28/07'==$fechi){$resul=1;}
else if('29/07'==$fechi){$resul=1;}
else if('30/08'==$fechi){$resul=1;}
else if('08/10'==$fechi){$resul=1;}
else if('01/11'==$fechi){$resul=1;}
else if('08/12'==$fechi){$resul=1;}
else if('25/12'==$fechi){$resul=1;}
return $resul;
}
//fucion de agregado
function generado($ide,$fec,$nu,$cu,$sal)
{
$cadena=str_replace("/","-",$fec);
$fec=date("Y-m-d",strtotime($cadena));
//enviar("INSERT INTO tpresta_detalle (idP,fechaProg, ncuota, cuota, saldo) VALUES ('$ide','$fec','$nu','$cu','$sal')");
}
 ?>
