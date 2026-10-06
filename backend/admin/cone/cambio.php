<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  /*$codi='566';
  $txtporcenta='6';
  $txtplazom='4';
  $lstpago='2';*/
  //$txtfechaDesembolso='2021-01-11';
    if(isset($codi))
    {
      //vaciamos las tablas veriicando
      $c=extraer("SELECT if(sum(montoPagado) is null,0,'X') as dato from tpresta_detalle where idP='$codi'");
      $ra=mysqli_fetch_array($c);
      if($ra['dato']=="0")
      {
        $c=extraer("SELECT idPD as id from tpresta_detalle where idP='$codi'");
        while ($ca=mysqli_fetch_array($c))
        {
          $id=$ca['id'];
          enviar("DELETE FROM tpresta_detalle where idPD='$id'");
          echo "eliminado".$id."<br>";
        }
        //actualziamos el prestamos
        switch ($lstpago) {
          case 1:
            $diaPasa="1";
            break;
          case 2:
            $diaPasa="7";
            break;
          case 3:
            $diaPasa="1";
            break;
          case 4:
            $diaPasa="30";
            break;
        }
          $txtfechaPago = date("Y-m-d",strtotime($txtfechaPago));
        //echo $lstpago."<br>";
          //echo $txtporcenta." ".$lstpago." ".$txtplazom." ".$diaPasa." ". $codi." ".$txtcuota." ".$txtfechaPago." ".$txtfechaDesembolso." ".$txtmonto ."<br>";
          enviar("UPDATE tprestamo set  taza='$txtporcenta',pago='$lstpago',plazo='$txtplazom',n_cuota='$txtplazom',diasPasados='$diaPasa',cuota='$txtcuota',fechaTermino='$txtfechaPago',fechaDesembolso='$txtfechaDesembolso',montoAprovado='$txtmonto' where idP='$codi'");
          //echo "UPDATE tprestamo set  taza='$txtporcenta',pago='$lstpago',plazo='$txtplazom',n_cuota='$txtplazom',diasPasados='$diaPasa',cuota='$txtcuota',fechaTermino='$txtfechaPago',fechaDesembolso='$txtfechaDesembolso',montoAprovado='$txtmonto' where idP='$codi'";
          //obtener valores de Prestamo
          $info=extraer("SELECT montoAprovado,taza,diasPasados,plazo,mora,pago,cuota,fechaDesembolso,fechaTermino FROM tprestamo where idP='$codi'");
          $row=mysqli_fetch_array($info);

          //enviamos a la caja detalle
          $taza=$row['taza'];
          $dia=$row['diasPasados'];
          $plazo=$row['plazo'];
          $mora=$row['mora'];
          $pago=$row['pago'];

          $monto=$row['montoAprovado'];
          $monto=($monto+($monto*($taza/100)))*10;
          $monto= bcdiv((ceil($monto)/10),'1','2');

          $cuotan=$row['cuota'];
          $fechaD=$row['fechaDesembolso'];
          $fechafin=$row['fechaTermino'];

          $cuota=$cuotan;
          $valor=0;
          $desc=$monto;
          //obtenemos los dias diasPasados
        //  echo $pago."<br>";
          switch ($pago) {
           case '1':
                 $com="1 days";
                 break;
           case '2':
                 $com="1 week";
                 break;
           case '3':
                 $com="1 days";
                 break;
           case '4':
                 $com="1 month";
             break;
         }
          date_default_timezone_set('america/lima');
          $fecha=$fechaD;

          //$fecha= date("2019-11-25");
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
                    $re=$monto-$valor;
                    $desc=0;
                    generado($codi,$fecha,$i,$re,$desc);
                  }
                  else
                  {
                    $valor=$valor+$cuota;
                    $desc=$desc-$cuota;
                    generado($codi,$fecha,$i,$cuota,$desc);
                  }
                }
              }
            }
            else if($pago==3)
            {
              $re=$cuotan;
              $desc=0;
              generado($codi,$txtfechaPago,$i,$re,$desc);
            }
            else
            {
              if($plazo==$i)
              {
                $re=$monto-$valor;
                $desc=0;
                generado($codi,$fecha,$i,$re,$desc);
              }
              else
              {
                $desc-=$cuota;
                $valor+=$cuota;
                generado($codi,$fecha,$i,$cuota,$desc);
              }
            }
          }
      }
  }
  function feriados($fec)
  {
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
    enviar("INSERT INTO tpresta_detalle (idP,fechaProg, ncuota, cuota, saldo) VALUES ('$ide','$fec','$nu','$cu','$sal')");
  }
 ?>
