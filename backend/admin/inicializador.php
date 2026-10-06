<?php
set_time_limit(60);
  include("conection/bdcredito.php");
  //=======FUNCIONES=========
  $idO=$_COOKIE['tofi'];

  //===========================================================================================================================
  function verificador($id,$mora,$fecha)
    {
      $det=extraer("SELECT @id:='$id',@fe:='$fecha',(select count(*) from tpresta_detalle where idP=@id and fechaProg<@fe and (estado='2' or estado is null)) as vencidas,@fechi:=(select fechaProg from tpresta_detalle where idP=@id and (estado='2' or estado is null) limit 1) as fechaProg,DATEDIFF(@fe,@fechi) as retra,(select sum(pagoMora) from tpresta_detalle where idP=@id and tfechaMora is null and estado='1' ) as demora,(select sum(pagoMora) from tpresta_detalle where idP=@id and tfechaMora is not null and (estado is null or estado='2')) as nodemora FROM tpresta_detalle limit 1");
      $deta=mysqli_fetch_array($det);
      $pendiente="";

      //cuotas vencidas
      $juvenal['0']=$deta['vencidas'];
      //dias de atraso
      $retra=$deta['retra'];

      $fechaU=$deta['fechaProg'];
      //obtener los dias de atraso
      $atraso=verificar($retra,$fechaU);

      //atraso de Credito
      $juvenal['1']=$atraso;
      $juvenal['2']=($atraso*$mora);//+$deta['demora'];
      $juvenal['3']=$mora;
      return $juvenal;
    }
  //===========================================================================================================================
  function darMora($codigo,$moraT,$can,$mora,$fecha,$macar)
  {
    //obtener las mora guardadas
    $dae=extraer("SELECT if(sum(pagoMora) is null,0,sum(pagoMora))as suma FROM tpresta_detalle where idP='$codigo' and tfechaMora is null");
    $dre=mysqli_fetch_array($dae);
    $moti=$dre['suma'];
    $total=$moraT;//-$moti;
    if($total>0)
    {
      //encontrar la canditad de cuotas
        $dea=extraer("select count(*) as dato FROM tpresta_detalle where idP='$codigo' and (estado='2' or estado is null or estado='' or (pagoMora is not null and tfechaMora is null)) and tfechaMora is null and fechaProg<'$fecha'");
        $sa=mysqli_fetch_array($dea);
        $cantidad=intval($sa['dato']);

        //encontrar las fechas y identificadores
        $data=extraer("select idPD,fechaProg as fec,ncuota,cuota,pagoMora,tfechaMora from tpresta_detalle where idP='$codigo' and fechaProg<'$fecha' and (estado='2' or estado is null or pagoMora is not null) ");//and pagoMora is null

        while ($r=mysqli_fetch_array($data))
        {
          $identi=$r['idPD'];
          $feca=$r['fec'];

         if(feriados2($feca)==1)
          {
          }
          else
          {
            if(empty($r['pagoMora']))
            {
              if($identi==$macar)
              {
                registrarMora($identi,$moraT);
                break;
              }
              if($cantidad==1)
              {
                if($macar==$identi)
                {
                registrarMora($identi,$moraT);
                }
                else if($r['pagoMora']=="")
                {
                  registrarMora($identi,$mora);
                }
                $cantidad--;
                $can--;
                $moraT=$moraT-$mora;

              }
              else if($cantidad>1)
              {
                if($identi==$macar)
                {
                  registrarMora($identi,$moraT);
                  break;
                }

                if($r['pagoMora']=="")
                {
                registrarMora($identi,$mora);
                }
                $cantidad--;
                $can--;
                $moraT=$moraT-$mora;
              }
              else
              {
              }
            }
          }
        }
    }
  }
  //===========================================================================================================================
  function registrarMora($id,$more)
  {
    // enviar("UPDATE tpresta_detalle SET pagoMora='$more' WHERE idPD='$id'");
  }
  //===========================================================================================================================
  function verificar($retra,$fecha)
  {
    $com="1 days";
    $total=0;
    for ($i=0; $i < $retra; $i++)
    {
        $fecha=date("d-m-Y",strtotime($fecha."+".$com));
        $dia=date("w", strtotime($fecha));
        if($dia=='0')
        {
        }
        else
        {
          if(feriados($fecha)=='1')
          {}
          else
          {
            $total++;
          }
        }
    }
    return $total;
  }
  //===========================================================================================================================
  //estraer feriados y dias sabados
    function feriados($fec)
    {
      $resul='0';
      $fec1 = preg_split("~-~", $fec);
      $fec="$fec1[0]/$fec1[1]";
      $año=$fec1[2];
      $resul=verFeriado($fec,$año);
      return $resul;
    }
    //===========================================================================================================================
    function feriados2($fec)
    {
      $resul='0';
      $fec1 = preg_split("~-~", $fec);
      $fec="$fec1[2]/$fec1[1]";
      $año=$fec1[0];
      $resul=verFeriado($fec,$año);
      return $resul;
    }
    //===========================================================================================================================
    function verFeriado($fechi,$año)
    {
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
  //--------------------------------------------------------------------------------------------------------------------------
  function juven7($id,$fe)
  {
    $resul=0;
    $c=extraer("SELECT sum(cuota) as cu, sum(montoPagado) as mon, sum(if(pagoMora is not null && tfechaMora is null,'1','0'))as mora FROM tpresta_detalle where idP='$id'	");
    $r=mysqli_fetch_array($c);
    $mo1=$r['cu'];
    $mo2=$r['mon'];
    $mo3=$r['mora'];
    if($mo1==$mo2 && $mo3=='0')
    {
      enviar("UPDATE tprestamo SET estado = '5', fechaTermino='$fe' WHERE idP ='$id' ");
      $resul=1;
    }
    return $resul;
  }
  //===========================================================================================================================
  $fecha=date("Y-m-d");
  $consulta=extraer("SELECT tp.idP as id FROM tprestamo tp inner join tpresta_detalle tpd on tp.idP=tpd.idP inner join tclie_general tc on tp.idCG=tc.idCG where tp.estado='4' and idO='$idO' group by tp.idp");

  while ($raton=mysqli_fetch_array($consulta))
  {
    /*pendejadas que hacemos para eliminar el credito pagado */
      $codigo=$raton['id'];
      $conejo=juven7($codigo,$fecha);
      if($conejo==0)
      {  
        //$codigo='242';
        $eC=extraer("SELECT if(estado=4,'ACTIVO','INACTIVO') as estado,plazo,idP,mora,n_cuota from tprestamo where idP='$codigo' ");
        $r1=mysqli_fetch_array($eC);
        //$estado=$r1['INACTIVO'];
        $id=$r1['idP'];
        $mora=$r1['mora'];
        $ncuota=$r1['n_cuota'];
        $plazo="";
        $prueba="";
        //empezamos la managing_apps
        //cuotas pendientes
        $pendiC="";
        //cuotas vencidas
        $venci="";
        //atraso de Credito
        $atra="";
        //total pendiente
        $pendiT="";
        //cuota
        $cuo="";
        //pendiente hasta ahora
        $pendiH="";
        //Mora
        $mor="";
        //*******************lapsus

    //==============================================
  //====================================================
      $d1=extraer("SELECT idPD as id, estado FROM tpresta_detalle where idP='$codigo' order by idPD desc limit 1");
      $d2=mysqli_fetch_array($d1);
      $macar=$d2['id'];
      if($d2['estado']==2)
      {
        // enviar("UPDATE tpresta_detalle SET pagoMora=null WHERE idPD='$macar'");
      }
      //====================================================

      //====================================================
        if($r1['estado']!="")
        {
          $estado=$r1['estado'];

          if($estado=="ACTIVO")
          {
              $plazo=$r1['plazo'];
              //$fecha=date("Y-m-d");
              //$fecha=date("2019-05-12");
              $prueba=verificador($id,$mora,$fecha);

              //cuotas vencidas
              $venci=$prueba['0'];
              //atraso de Credito
              $atra=$prueba['1'];
                //pendiente hasta ahora
              $pendiH=$prueba['2'];
              //Mora
              $mor=$prueba['3'];

              darMora($codigo,$pendiH,$atra,$mora,$fecha,$macar);
          }
        }
      }
    }

  //*************************

  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <title></title>
    </head>
    <body onload="javascript:self.close();">

    </body>
  </html>
