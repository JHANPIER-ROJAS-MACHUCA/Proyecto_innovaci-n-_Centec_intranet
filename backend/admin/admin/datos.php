<?php
  include('../../../conection/db7.php');
  function recorte($codigo,$ca)
  {
    $num=0;
    for ($i=1; $i < $ca; $i++)
    {
      if($codigo[$i]>0)
      {
        break;
      }
      else
      {
        $num++;
      }
    }
    $resul=$num;
    return $resul;
  }

  extract($_POST);

  //$codigo1="o001";
  $ca1=strlen($codigo1);
  $num1=recorte($codigo1,$ca1);
  $cadena1=substr($codigo1,($num1+1));

  //$codigo2="C01";
  $ca2=strlen($codigo2);
  $num2=recorte($codigo2,$ca2);

  $cadena2=substr($codigo2,($num2+1));
  $ca2=$ca2-$num2-1;
  $codificador=10;
  $codificador=$codificador-$ca2;
  $codi=$codigo2[0];
  $codi=strtoupper($codi);
  for ($i=0; $i < $codificador; $i++)
  {
    $codi.="0";
  }
  $codi.=$cadena2;

  $c=extraer("SELECT @id:=idmov as id1, tipoM,monto, id, (select if(sum(monto) is null,0,sum(monto))  from tmovi_deta where idmov=@id) as pagado FROM tmovimiento where idO='$cadena1' and numero='$codi'");
  $d=mysqli_fetch_array($c);
  
  $tipo="";

  if($d['tipoM']=="1")
  {
    $tipo="COMPRA";
  }
  else if($d['tipoM']=="2")
  {
    $tipo="VENTA";
  }
  else if($d['tipoM']=="3")
  {
    $tipo="INGRESO";
  }
  else if($d['tipoM']=="4")
  {
    $tipo="TRASLADO";
  }
  //determinar la deuda
  $monto=$d['monto']-$d['pagado'];
  //cliente
  $cliente="varios";
  $direccion="S/n";
  $identi="";
  if($d['id']!="0")
  {
    $identi=$d['id'];
    $asd=extraer("SELECT datos,direccion from tproveedor where idprove='$identi'");
    $asd1=mysqli_fetch_array($asd);
    $cliente=$asd1['datos'];
    $direccion=$asd1['direccion'];
  }
  if($cliente!="")
  {
    $valor=$cliente."|".$tipo."|".$monto."|".$d['id1']."|".$direccion."|".$identi;
    echo $valor;
  }

  //$valor=$codi."|".$codigo2;

  //echo json_encode(array("clie"=>"$cliente","tipo"=>"$tipo"));
 ?>
