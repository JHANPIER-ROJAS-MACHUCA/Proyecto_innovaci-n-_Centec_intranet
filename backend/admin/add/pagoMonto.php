<?php
  include("../conection/bdcredito.php");
  ?>
  <?php /*<form  method="post">
    <input type="text" placeholder="id" name="id" value="">
    <input type="text" name="monto" value="">
    <input type="text" name="fecha" placeholder="00-00-0000" value="">
    <input type="submit" name="" value="Enviar">
  </form>
  //<?php*/
  extract($_POST);
  if(isset($id) && isset($monto) && isset($fecha))
  {
    //limpiamos el id del string
    $id=str_replace("juve1","",$id);
    //obtenemos la mora

    $m=extraer("SELECT mora from tprestamo where idP='$id'");
    $roe=mysqli_fetch_array($m);
    $mora=$roe['mora'];
    //fecha
    $fe=preg_split("~/~", $fecha);
    $fec=$fe['2'].'-'.$fe['1'].'-'.$fe['0'];
    //obtenemos las cuotas a pagar por monto
      $montoF=$monto;
    ///id usuario
    $idU=$_COOKIE['user1'];
    $dt=extraer("SELECT idPD,fechaProg,cuota,montoPagado,estado,pagoMora,fechaPago FROM tpresta_detalle WHERE idP='$id' and estado='2' or estado is null");
    //echo $montoF."<br>";

    while ($row=mysqli_fetch_array($dt))
    {
      $tipo="2";
      $ide=$row['idPD'];
      if($fec>$row['fechaProg'])
      {
        $adelanto=0;
        //obtenemos cuanto a pagado si es que pago
        if($row['montoPagado']!=null)
        {
          $adelanto=$row['montoPagado'];
        }
        //obtenemos cuanto debe de pagar en esa cuota
        $adelanto=($row['cuota'])-$adelanto;
        //si el monto es mayor a la deuda
        if($montoF>=$adelanto)
        {
          $montoF-=$adelanto;
          if($montoF>=$mora)
          {
            $montoF-=$mora;
            $tipo="1";
            $cuo=$row['cuota'];
            pago1($ide,$cuo,$mora,$fec,$tipo,$idU);
          }
        }
        else
        {
          if($montoF>0)
          {
            pago2($ide,$montoF,$fec,$tipo,$idU);
            $montoF-=$montoF;
          }
        }
      }
      else
      {
        $adelanto=0;
        //obtenemos cuanto a pagado si es que pago
        if($row['montoPagado']!=null)
        {
          $adelanto=$row['montoPagado'];
        }
        //obtenemos cuanto debe de pagar en esa cuota
        $adelanto=($row['cuota'])-$adelanto;
        //si el monto es mayor a la deuda
        if($montoF>=$adelanto)
        {
          $montoF-=$adelanto;
          $tipo="1";
          $cuo=$row['cuota'];
          pago2($ide,$cuo,$fec,$tipo,$idU);
        }
        else
        {
          if($montoF>0)
          {
            pago2($ide,$montoF,$fec,$tipo,$idU);
            $montoF-=$montoF;
          }
        }
      }
    }
  }
  //pagar antes de la fecha
  function pago1($id,$monto,$mora,$fec,$estado,$idu)
  {
    enviar("UPDATE tpresta_detalle SET montoPagado='$monto' ,pagoMora='$mora',fechaPago='$fec',estado='$estado',idU='$idu'  where idPD='$id'");
  }
  //pagar despues de fecha sin MORA
  function pago2($id,$monto,$fec,$estado,$idu)
  {
    enviar("UPDATE tpresta_detalle SET montoPagado='$monto',fechaPago='$fec' ,estado='$estado' ,idU='$idu' where idPD='$id'");
  }
 ?>
