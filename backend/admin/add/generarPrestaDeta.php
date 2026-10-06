<?php
include('../conection/bdcredito.php');
extract($_POST);

if (isset($id)) {

  //obteniendo si y se agrego anterior mente
  $resul = extraer("SELECT COUNT(*) as dato FROM tpresta_detalle WHERE idP='$id'");
  $rq = mysqli_fetch_array($resul);
  if ($rq['dato'] == "0") {
    $idO = $_COOKIE['tofi'];
    $data = extraer("SELECT ini FROM tcaja_oficina where idO='$idO' order by idCO desc limit 1 ");
    $rw = mysqli_fetch_array($data);
    $ini = $rw['ini'];
    //verificamos el numero de credito
    $fr = extraer("SELECT @id:=idCG,(SELECT (count(*)+1) as dato FROM tprestamo where idCG=@id and (estado='4' or estado='5')) as dato FROM tprestamo where idP='$id'");
    $cre = mysqli_fetch_array($fr);
    $credito = $cre['dato'];
    //agregando la fecha de DESEMBOLSO
    enviar("UPDATE tprestamo set fechaDesembolso='$ini',estado='4',n_credito='$credito',tofic='$idO' where idP='$id'");

    //obtener valores de Prestamo
    $info = extraer("SELECT montoAprovado,taza,diasPasados,plazo,mora,pago,fechaTermino,cuota, started_at FROM tprestamo where idP='$id'");
    $row = mysqli_fetch_array($info);
    //$monto=parse6
    $monto = $row['montoAprovado'];
    $taza = $row['taza'];
    $dia = $row['diasPasados'];
    $plazo = $row['plazo'];
    $mora = $row['mora'];
    $pago = $row['pago'];

    $cuotanF = $row['cuota'];
    $fechaFinal = $row['fechaTermino'];
    //actualizar en el historial de caja ususario
    //optenemos el identificador
    $idUsuario = $_COOKIE['user1'];
    if ($_COOKIE['tuser'] == '2') {
      $oficina = $_COOKIE['tofi'];
      $dea = extraer("SELECT @id:=idCO,(select idCA from tcaja_usuario where idCO=@id and montofin is null order by idCA desc limit 1 ) as idCA FROM tcaja_oficina where idO='$oficina' order by idCO desc limit 1");
    } else {
      $dea = extraer("select idCA from tcaja_usuario where idU='$idUsuario' and montofin is null order by idCA desc limit 1");
    }

    $req = mysqli_fetch_array($dea);
    $idenJuve = $req['idCA'];
    //enviamos a la caja detalle
    $currentDate = date('Y-m-d H:i:s');
    enviar("INSERT INTO tcaja_usu_detal (idCA,tipo,total,cliente,conejo,created_at) VALUES ('$idenJuve','2','$monto','$idc','$id','$currentDate')");


    $monto = ($monto + ($monto * ($taza / 100))) * 10;
    //monto
    //para obtener dos decimales
    $monto = bcdiv((ceil($monto) / 10), '1', '2');
    echo $monto . "<br>";
    //cuota
    //$cuota=(ceil(($monto/$plazo)*10))/10;
    $cuota = $cuotanF;
    $valor = 0;
    $desc = $monto;
    //obtenemos los dias diasPasados
    switch ($pago) {
      case '1': // diario
        $com = "1 days";
        break;
      case '2': // semanal
        $com = "1 week";
        break;
      case '3': // pago unico
        $com = "2 week";
        break;
      case '4': // mensual
        $com = "1 month";
        break;
      case '5': // quincenal
        $com = "15 days";
        break;
    }
    date_default_timezone_set('america/lima');
    $fecha = $row['started_at'] != null ? date('Y-m-d', strtotime($row['started_at'] . '-' . $com)) : date("Y-m-d");
    $ultimaFechaDePago = $fecha;
    //$fecha= date("2021-02-10");
    for ($i = 1; $i <= $plazo; $i++) {

      $fecha = date("Y/m/d", strtotime($fecha . "+" . $com));
      $dia = date("w", strtotime($fecha));

      // verificamos que sea el ultimo buble
      // y actualizamosla fecha en que el credito finaliza
      if ($i == $plazo) {
        $ultimaFechaDePago = $fecha;
        enviar("UPDATE tprestamo set fechaTermino='$ultimaFechaDePago' where idP='$id'");
      }

      if ($pago == '1') {
        if ($dia == '0') {
          $i--;
        } else {
          if (feriados($fecha) == '1') {
            $i--;
          } else {
            if ($plazo == $i) {
              $re = $monto - $valor;
              $desc = 0;
              generado($id, $fecha, $i, $re, $desc);
            } else {
              $valor = $valor + $cuota;
              $desc = $desc - $cuota;
              generado($id, $fecha, $i, $cuota, $desc);
            }
          }
        }
      } else if ($pago == '3') {
        $re = $cuotanF;
        $desc = 0;
        $fecha = $fechaFinal;
        generado($id, $fecha, $i, $re, $desc);
      } else {
        if ($plazo == $i) {
          $re = $monto - $valor;
          $desc = 0;
          generado($id, $fecha, $i, $re, $desc);
        } else {
          $desc -= $cuota;
          $valor += $cuota;
          generado($id, $fecha, $i, $cuota, $desc);
        }
      }
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
  $fec2 = preg_split("~/~", $fec);
  $fechi = "$fec2[2]/$fec2[1]";
  $año = $fec2[0];
  //comprende lo saños de 1970 a 2037
  $sema = date("Y/m/d", easter_date($año));

  $santa = date("d/m", strtotime($sema . "-" . "3 days"));
  $santa2 = date("d/m", strtotime($sema . "-" . "2 days"));
  $resul = 0;
  if ($santa == $fechi) {
    $resul = 1;
  } else if ($santa2 == $fechi) {
    $resul = 1;
  } else if ('01/01' == $fechi) {
    $resul = 1;
  } else if ('01/05' == $fechi) {
    $resul = 1;
  } else if ('29/06' == $fechi) {
    $resul = 1;
  } else if ('28/07' == $fechi) {
    $resul = 1;
  } else if ('29/07' == $fechi) {
    $resul = 1;
  } else if ('30/08' == $fechi) {
    $resul = 1;
  } else if ('08/10' == $fechi) {
    $resul = 1;
  } else if ('01/11' == $fechi) {
    $resul = 1;
  } else if ('08/12' == $fechi) {
    $resul = 1;
  } else if ('25/12' == $fechi) {
    $resul = 1;
  }
  return $resul;
}
//fucion de agregado
function generado($ide, $fec, $nu, $cu, $sal)
{
  $cadena = str_replace("/", "-", $fec);
  $fec = date("Y-m-d", strtotime($cadena));
  enviar("INSERT INTO tpresta_detalle (idP,fechaProg, ncuota, cuota, saldo) VALUES ('$ide','$fec','$nu','$cu','$sal')");
}
