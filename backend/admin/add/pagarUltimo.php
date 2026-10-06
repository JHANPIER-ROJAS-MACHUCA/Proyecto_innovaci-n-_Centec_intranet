<?php
include('../conection/bdcredito.php');
extract($_POST);
//========================================
//========================================
/*$id='552';
  $fechaJuve='14/03/2020';
  $montoN='57.40';*/
//extraemos el valor de mora Diario
$creditId = $id;
$data = extraer("SELECT mora,idCG as iden from tprestamo where idP='$id'");
$roe = mysqli_fetch_array($data);
$mora = $roe['mora'];
$cliente = $roe['iden'];
date_default_timezone_set('america/lima');
//cambiar la fecha al de caja abrierto
$fecha = date("Y-m-d H:i:s");
//id usuario
$idU = $_COOKIE['user1'];

$campos = "";
$valores = "";
//verificar mora(
$cmora = evaluar($cmora);
//verificar monto neto
$montoN = evaluar($montoN);
//verificar monto cuota
$mcuota = evaluar($mcuota);
$juveP = $total;
//identificamos el total
$fin = $montoN;
$contadorMora = $venci;

//id de cuotas
$idCuotas = "";
$idMora = "";
if ($cuo == "0") {
  $fin = $montoN;
} else if ($cuo == "1") {
  $fin = $mcuota;
}
$juve7 = $fin;
//feriados ultimo
function feriados2($fec)
{
  $resul = '0';
  $fec1 = preg_split("~-~", $fec);
  $fec = "$fec1[2]/$fec1[1]";
  $info = extraer("SELECT COUNT(*) AS con from tfecha where fecha='$fec'");
  while ($row = mysqli_fetch_array($info)) {
    $dato = $row['con'];
  }
  if (!empty($dato)) {
    $resul = '1';
  }
  return $resul;
}
//pagar Mora
$juve71 = 0;
if ($mor == "1") {
  $nif = $tomora;
  $juve71 = $nif;
  if ($nif > 0) {
    $data = extraer("select idPD,fechaProg,pagoMora,tfechaMora FROM tpresta_detalle where idP='$id' and tfechaMora is null ");
    while ($r = mysqli_fetch_array($data)) {
      $idPag = $r['idPD'];

      $pag = $r['pagoMora'];
      if ($nif > 0) {
        if ($pag != null) {
          pagaMoraMascota($idPag, $fecha);
          $idMora .= "" . $idPag . ",";
          $nif -= $pag;
        }
      }
    }
  }
}

////*******
/*cambiar ubicaion de fehca*/
function ordenFecha($dato)
{
  $f = preg_split("~/~", $dato);
  $fecha = $f[2] . "-" . $f[1] . "-" . $f[0];
  return $fecha;
}
$fechaJuve = ordenFecha($fechaJuve);
//===========================================================================
//si la fecha es mayor a la ultima cuota empezamos desde el principio
$cade = extraer("SELECT @id:=idP as id,@fe:=max(fechaProg) as de, min(fechaProg) as de1,(select cuota from tpresta_detalle where idP=@id order by idPD desc limit 1) as cuota FROM tpresta_detalle where idP='$id'");
$sac = mysqli_fetch_array($cade);
if ($sac['de'] <= $fechaJuve) {
  $fechaJuve = $sac['de1'];
}
/*else if($sac['cuota']<$montoN)
{
  $fechaJuve=$sac['de1'];
}*/

function floattostr($val)
{
  preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
  return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
}
//===========================================================================
//obtenemos el monto real del neto
$inicilizadora = 0;
while (1 >= $inicilizadora) {
  if ($inicilizadora > 0) {
    $fechaJuve = $sac['de1'];
  }
  $data = extraer("select idPD,cuota,montoPagado,fechaProg FROM tpresta_detalle where idP='$id' and fechaProg>='$fechaJuve' and (estado='2' or estado is null or estado='')");
  while ($r = mysqli_fetch_array($data)) {
    $tipo = "2";
    $idPa = $r['idPD'];

    $cuota = $r['cuota'];
    $adelanto = 0;
    $fechita = $r['fechaProg'];
    if ($r['montoPagado'] != "") {
      $adelanto = $r['montoPagado'];
    }
    $adelanto = ($r['cuota']) - $adelanto;
    if ($fin > $adelanto) {
      //obtenemos la diferencia del monto pagado si es que lo hubiera
      $fin -= $adelanto;
      $tipo = "1";
      pago2($idPa, $cuota, $fecha, $tipo, $idU);
      $idCuotas .= "" . $idPa . ",";
    } else if ($fin > 0) {
      $adel = 0;
      if ($r['montoPagado'] != "") {
        $adel = $r['montoPagado'];
      }
      $fin += $adel;
      if (floattostr($cuota) == floattostr($fin)) {
        $tipo = '1';
      } else {

        //$tipo=determinarUltimo($id,$idPa);
      }
      pago2($idPa, $fin, $fecha, $tipo, $idU);
      $idCuotas .= "" . $idPa . ",";
      $fin -= $fin;
    }
  }
  $inicilizadora++;
}

/*function determinarUltimo($id,$idP)
{
  $resul=2;
  $c=extraer("SELECT idPD as id FROM tpresta_detalle where idP='$id' order by idPD desc limit 1 ");
  $r=mysqli_fetch_array($c);
  if($r['id']==$idP)
  {
    $resul=1;
  }
  return $resul;
}*/
//****

//registrar la operacion
function registraOperacion($id, $idcu, $idmor, $monto, $mor, $total, $cliente, $created_at, $creditId)
{
  $datoC = extraer("SELECT idCA as id FROM tcaja_usuario where idU='$id' order by idCA desc limit 1");
  $r = mysqli_fetch_array($datoC);
  if ($_COOKIE['tuser'] == '2') {
    //vemos la caja abierta
    $ido = $_COOKIE['tofi'];
    $fs = extraer("SELECT idCO FROM tcaja_oficina where idO='$ido' order by idCO desc limit 1");
    $re = mysqli_fetch_array($fs);
    $ofi = $re['idCO'];

    $datoC = extraer("SELECT idCA as id FROM tcaja_usuario where idCO='$ofi' order by idCA asc limit 1");
    $r = mysqli_fetch_array($datoC);
  }
  $idCA = $r['id'];

  $resumen = getResumen($creditId);
  $cuotasFaltantes = $resumen['cuotasFaltantes'];
  $saldo = $resumen['deudaTotal'];
  $nextPayment = $resumen['nextPayment'];

  enviar("INSERT INTO tcaja_usu_detal (idCA,tipo,cuota,idCuota,mora,idMora,total,cliente,saldo,cuotas_pendientes,next_payment,created_at) VALUES ('$idCA','3','$monto','$idcu','$mor','$idmor','$total','$cliente', '$saldo', '$cuotasFaltantes', '$nextPayment', '$created_at')");
  $ra = extraer("SELECT idCAD as iden FROM tcaja_usu_detal where idCA='$idCA' and tipo='3' order by idCAD desc limit 1");
  $r = mysqli_fetch_array($ra);
  $dato = $r['iden'];
  return $dato;
}

function getResumen($creditId)
{
  $stm = extraer("SELECT * FROM tpresta_detalle WHERE idP='$creditId'");

  $cuotasFaltantes = 0;
  $deudaTotal = 0;
  $sumCuota = 0;
  $sumPago = 0;
  $nextPayment = '';
  while ($installment = mysqli_fetch_array($stm)) {
    $sumCuota += $installment['cuota'];
    $sumPago += $installment['montoPagado'];
    $deudaTotal += ($installment['cuota']) - ($installment['montoPagado']);

    if ($installment['estado'] != '1') {
      $cuotasFaltantes += 1;

      if (!$nextPayment) {
        $nextPayment = $installment['fechaProg'];
      }
    }
  }

  return [
    'cuotasFaltantes' => $cuotasFaltantes,
    'deudaTotal' => $deudaTotal,
    'nextPayment' => $nextPayment
  ];
}

//pagar mora si es que habilito el check mora
function pagaMoraMascota($id, $fe)
{
  enviar("UPDATE tpresta_detalle SET tfechaMora='$fe' WHERE idPD='$id'");
}

//saber si esta en el rango
function rangoF($fecha_inicio, $fecha_fin, $fecha)
{
  $fecha_inicio = strtotime($fecha_inicio);
  $fecha_fin = strtotime($fecha_fin);
  $fecha = strtotime($fecha);
  if (($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
    return true;
  } else {
    return false;
  }
}

function feriados($fec)
{
  $resul = '0';
  $info = extraer("SELECT COUNT(*) AS con from tfecha where fecha='$fec'");
  while ($row = mysqli_fetch_array($info)) {
    $dato = $row['con'];
  }
  if (!empty($dato)) {
    $resul = '1';
  }
  return $resul;
}
///evaluador
function evaluar($mon)
{
  if ($mon == "") {
    $mon = 0;
  }
  return $mon;
}
//pagar su monto
function pago2($id, $monto, $fecha, $estado, $usuario)
{
  enviar("UPDATE tpresta_detalle set montoPagado='$monto',fechaPago='$fecha',estado='$estado',idU='$usuario' where idPD='$id'");
}
//============================================================================
$oper = registraOperacion($idU, $idCuotas, $idMora, $juve7, $juve71, $juveP, $cliente, $fecha, $creditId);
echo "<input id='opera1' type='hidden' value='$oper'></input>";
$infoUser = extraer("select concat(apU,' ',amU,' ',nomU) as dato,direccion,telefono,correo FROM tusuario tu inner join toficina tof on tu.idO=tof.idO where idU='$idU'");
$rq1 = mysqli_fetch_array($infoUser);
$nameUsu = $rq1['dato'];
$direcc = $rq1['direccion'];
$telef = $rq1['telefono'];
$corre = $rq1['correo'];
echo "<input id='usu' type='hidden' value='$nameUsu'></input>";
echo "<input id='dire' type='hidden' value='$direcc'></input>";
echo "<input id='tel' type='hidden' value='$telef'></input>";
echo "<input id='corre' type='hidden' value='$corre'></input>";

 //============================================================================
