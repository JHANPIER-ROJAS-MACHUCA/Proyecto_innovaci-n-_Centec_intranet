<?php
include('../conection/bdcredito.php');
/*
  <form method="post">
    <input type="text" name="id" value="41">
    <button type="submit" name="button">holaa</button>
  </form>
  */
extract($_POST);
if(isset($id))
{
  $info=extraer("select cuota,idCuota,mora,idMora from tcaja_usu_detal where idCAD='$id'");
  $d1=mysqli_fetch_array($info);
  $idC=$d1['idCuota'];
  $idM=$d1['idMora'];
  $montoC=$d1['cuota'];
  $montoM=$d1['mora'];

  if($idC!="")
  {
      $pago = preg_split("~,~", $idC);
      $cant=count($pago)-2;
      $efectivo=$montoC;
     echo $efectivo."<br>";
      for ($i=$cant; $i >= 0 ; $i--)
      {
        $data=extraer("select montoPagado,fechaPago,estado,idU from tpresta_detalle where idPD='$pago[$i]'");
        $r=mysqli_fetch_array($data);
        $descuento=$r['montoPagado'];
        $fec=$r['fechaPago'];
        $iduser=$r['idU'];

        echo $pago[$i]." - - ";
        if($efectivo>=$descuento)
        {
          $efectivo=$efectivo-$descuento;
          $mnd=NULL;
          $fec=NULL;
          $esta=NULL;
        }
        else if($efectivo<$descuento && $efectivo>0)
        {
          echo "$efectivo < $descuento";
          $mnd=$descuento-$efectivo;
          $efectivo=0;
          $esta=2; 
        }
        $iden=$pago[$i];
        echo $mnd." - - ".$fec." - - ".$esta."<br>";
       if($mnd == NULL)
        {
          enviar("UPDATE tpresta_detalle SET montoPagado= NULL, fechaPago = NULL, estado= NULL, idU = NULL WHERE idPD= $iden");
        }
        else
        {
          enviar("UPDATE tpresta_detalle SET montoPagado= '$mnd', estado= '$esta', idU = '$iduser' WHERE idPD= $iden");
        }
      }
  }
  //cambiar el extorno
  enviar("UPDATE tcaja_usu_detal SET estadodt='1' WHERE idCAD='$id'");
  enviar("UPDATE textorno set estado='1' WHERE idex='$id'");
}
 ?>
