<?php
  include("../conection/bdcredito.php");
  $codigo=$_REQUEST['codigo'];
  //$codigo='242';

  $status = getStatus($codigo);

  //estado  y Cuotas
  $eC=extraer("SELECT if(estado=4,'ACTIVO','INACTIVO') as estado,plazo,idP,mora,n_cuota,fechaDesembolso from tprestamo where idP='$codigo' ");
  $r1=mysqli_fetch_array($eC);

  $fechaDesembolso = $r1['fechaDesembolso'];

  $estado=$r1['INACTIVO'];
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
  $fecha=date("Y-m-d");
  //$fecha=date("2019-12-10");
  //==============================================
  function verificador($id,$mora,$cuota,$fecha)
    {
      //$det=extraer("SELECT @id:='$id',@fe:='$fecha',(select count(*) from tpresta_detalle where idP=@id and fechaProg>=@fe and (estado='2' or estado is null))as pendiente,(select count(*) from tpresta_detalle where idP=@id and fechaProg<@fe and (estado='2' or estado is null)) as vencidas,@fechi:=(select fechaProg from tpresta_detalle where idP=@id and (estado='2' or estado is null) limit 1) as fechaProg,(select SUM(cuota) - SUM(if(montoPagado!='',montoPagado,0)) from tpresta_detalle where idP=@id and fechaProg<=@fe) as debe, DATEDIFF(@fe,@fechi) as retra,(select ((saldo+cuota)-(if(montoPagado!='',montoPagado,0))) from tpresta_detalle where idP=@id  and (estado='2' or estado is null) limit 1) as saldo,(select cuota from tpresta_detalle where idP=@id and fechaProg<=@fe limit 1)as cuota FROM tpresta_detalle limit 1");
      $det=extraer("SELECT @id:='$id',@fe:='$fecha',(select count(*) from tpresta_detalle where idP=@id and fechaProg>=@fe and (estado='2' or estado is null))as pendiente,(select count(*) from tpresta_detalle where idP=@id and fechaProg<@fe and (estado='2' or estado is null)) as vencidas,@fechi:=(select fechaProg from tpresta_detalle where idP=@id and (estado='2' or estado is null) limit 1) as fechaProg,(select SUM(cuota) - SUM(if(montoPagado!='',montoPagado,0)) from tpresta_detalle where idP=@id and fechaProg<=@fe) as debe, DATEDIFF(@fe,@fechi) as retra,(select sum(cuota)-sum(if(montoPagado!='',montoPagado,0)) as saldo from tpresta_detalle where idP=@id and (estado='2' or estado is null) limit 1) as saldo,(select cuota from tpresta_detalle where idP=@id and fechaProg<=@fe limit 1)as cuota FROM tpresta_detalle limit 1");
      $deta=mysqli_fetch_array($det);

      $pendiente="";
      //cuotas pendientes

      $pendiente=$deta['pendiente'];

      $juvenal['0']=$pendiente;
      //cuotas vencidas
      $juvenal['1']=$deta['vencidas'];

      //dias de atraso
      $retra=$deta['retra'];

      $fechaU=$deta['fechaProg'];
      //obtener los dias de atraso
      $atraso=verificar($retra,$fechaU);
      //atraso de Credito
      $juvenal['2']=$atraso;
      //total pendiente
      $juvenal['3']=$deta['saldo'];
      //cuota
      $juvenal['4']=$deta['cuota'];
      //pendiente hasta ahora
      $juvenal['5']=$deta['debe'];
      //Mora
      return $juvenal;
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
        $prueba=verificador($id,$mora,$ncuota,$fecha);
        //cuotas pendientes
        $pendiC=$prueba['0'];
        //cuotas vencidas
        $venci=$prueba['1'];
        //atraso de Credito
        $atra=$prueba['2'];
        //total pendiente
        $pendiT=$prueba['3'];
        //cuota
        $cuo=$prueba['4'];
        //pendiente hasta ahora
        $pendiH=$prueba['5'];
        //Mora
        $rm=extraer("select sum(pagoMora) as mor from tpresta_detalle where idP='$codigo' and fechaProg<'$fecha' and (estado='2' or estado is null or pagoMora is not null) and tfechamora is null limit 1");
        $rm1=mysqli_fetch_array($rm);
        $mor=$rm1['mor'];
        ///$prueba['6'];
        // darMora($codigo,$mor,$atra,$mora,$fecha,$macar);
    }
  }
  //===========================================================
  //verificar si es el ultimo idea
//====================================================
  $determina=extraer("SELECT if(pagoMora is null,0,pagoMora) as da FROM tpresta_detalle where idP='$codigo' and tfechaMora is null and pagoMora is not null");
  $cadena="";
  while ($r=mysqli_fetch_array($determina))
  {
    $cadena.=$r['da'].",";
  }
  //*************************
  echo json_encode(array(
    "status"=>$status['status'],
    "finCredit" => $status['finCredit'],
    "fechaDesembolso" => date('d-m-Y',strtotime($fechaDesembolso)),
    "totalAPagar" => $status['totalAPagar'],
    "estado"=>"$estado.$id",
    "plazo"=>"$plazo",
    "pendiC"=>"$pendiC",
    "venci"=>"$venci",
    "atra"=>"$atra",
    "pendiT"=>"$pendiT",
    "cuo"=>"$cuo",
    "pendiH"=>"$pendiH",
    "mora"=>"$mor",
    "cadena"=>"$cadena"
  ));


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
        {

        }
        else
        {
          $total++;
        }
      }
  }
  return $total;
}
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

  function feriados2($fec)
  {
    $resul='0';
    $fec1 = preg_split("~-~", $fec);
    $fec="$fec1[2]/$fec1[1]";
    $año=$fec1[0];
    $resul=verFeriado($fec,$año);

    return $resul;
  }
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

  function getStatus($creditId)
  {
    $status = '';
    $totalAPagar = 0;

    $det = extraer("SELECT * FROM tpresta_detalle where idP=$creditId");
    
    $finFecha='';
    $nextPayment = '';
    $amortizo = false;

    while ($credit=mysqli_fetch_array($det)) {
      // obtenemos la primera cuota que falta pagar
      
      // si aun falta pagar
      if (strtotime($credit['fechaProg']) <= strtotime(date('Y-m-d'))) {
        $totalAPagar += floatval($credit['cuota']) - floatval($credit['montoPagado']);
      }
      
      if (!$nextPayment && $credit['estado'] != '1') {
        $nextPayment = $credit['fechaProg'];
        $amortizo = floatval($credit['montoPagado']) > 0;
      }

      $finFecha = $credit['fechaProg']; 
    }

    $isPuntual = strtotime($nextPayment) >= strtotime(date('Y-m-d'));
    if ($isPuntual && $amortizo) {
      $status = "ADELANTADO";
    }else if($isPuntual){
      $status = "PUNTUAL";
    }else{

      if (strtotime($finFecha) < strtotime(date('Y-m-d'))) {
        $status = "VENCIDO";
      }else{
        $status = "RETRAZADO";
      }

    }


    return [
      'status' => $status,
      'finCredit' => date('d-m-Y  ', strtotime($finFecha)),
      'totalAPagar' => $totalAPagar
    ];
  }

  ?>
