<?php include('head.php');
if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '2' && $_COOKIE['tuser'] != '3') {
  echo "<script>location.href='index.php'</script>";
  //echo "<script>alert(".$_COOKIE['tuser'].")</script>";

}
$diasDeAtrazo = isset($_GET['dias_atrazo']) && !empty($_GET['dias_atrazo']) ? $_GET['dias_atrazo'] : '';
$atrazoInicio = 0; // en dias
$atrazoFin = 180;

if ($diasDeAtrazo) {
  $a = explode('-', $diasDeAtrazo);
  if (count($a) === 2) {
    $value1 = intval($a[0]);
    $value2 = intval($a[1]);

    $atrazoInicio = $value1;
    if ($value2 > 0) {
      $atrazoFin = $value2;
    }
  }
}

?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">


    </div>
    <h5 style="color:white">Reportes <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" id="crack">
    <div id="juve">
      <div class="tabs-container">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#tab-1"><strong>REPORTES DEUDORES</strong></a></li>
          <!--<li><button  class="btn btn-default"  title='Descargar Lista'  onclick="imprimir_ahorro('reportes');">
                        <span class="glyphicon glyphicon-print"></span> Imprimir
                      </button></li>-->
        </ul>
        <div class="tab-content">
          <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
              <div>
                <form action="reporteMoras.php" method="get">
                  <label>Días de atrazo</label>
                  <select name="dias_atrazo">
                    <option value="" <?php echo $diasDeAtrazo == '' ? 'selected' : '' ?>>Todos</option>
                    <option value="1-5" <?php echo $diasDeAtrazo == '1-5' ? 'selected' : '' ?>>1 - 5</option>
                    <option value="6-15" <?php echo $diasDeAtrazo == '6-15' ? 'selected' : '' ?>>6 - 15</option>
                    <option value="16-30" <?php echo $diasDeAtrazo == '16-30' ? 'selected' : '' ?>>16 - 30</option>
                    <option value="31-180" <?php echo $diasDeAtrazo == '31-180' ? 'selected' : '' ?>>31 - 180</option>
                  </select>
                  <button type="submit">Buscar</button>
                </form>
              </div>
              <div class="col-xs-12" id="reportes">
                <div class=" center-version" id="reportes2">
                  <!-- style="overflow-y:scroll;height:350px">-->
                  <?php
                  $consul = "";
                  if ($_COOKIE['tuser'] == '1') {
                    $consul = extraer("SELECT @id:=tp.idP as id,dni,concat(ap,' ',am,' ',nom)as dato,direc as dir,cel,tp.montoAprovado as mont, taza,@monto:=(round((montoAprovado*((taza+100)/100)),2))as suma,round((@monto-(SELECT sum(if(montoPagado is null,0,montoPagado))  FROM tpresta_detalle where idP=@id)),2)as saldo,concat(tu.apU,' ',tu.amU,' ',nomU) as usuario,tp.fechaDesembolso as desem from tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG inner join tusuario tu on tc.idU=tu.idU  where tp.estado='4' order by desem asc");
                  } else {
                    $idOficina = $_COOKIE['tofi'];
                    $consul = extraer("SELECT @id:=tp.idP as id,dni,concat(ap,' ',am,' ',nom)as dato,direc as dir,cel,tp.montoAprovado as mont, taza,@monto:=(round((montoAprovado*((taza+100)/100)),2))as suma,round((@monto-(SELECT sum(if(montoPagado is null,0,montoPagado))  FROM tpresta_detalle where idP=@id)),2)as saldo,concat(tu.apU,' ',tu.amU,' ',nomU) as usuario,tp.fechaDesembolso as desem from tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG inner join tusuario tu on tc.idU=tu.idU  where tp.estado='4' and tc.idO='$idOficina'  order by desem asc");
                  }

                  ?>
                  <table class="table table-striped  dataTables-example" style="font-size:10px">
                    <thead>
                      <tr>
                        <th style="text-align:center">DNI</th>
                        <th style="text-align:center">CLIENTE</th>
                        <th style="text-align:center">DIRECCION</th>
                        <th style="text-align:center">CELULAR</th>
                        <th style="text-align:center">PRESTAMO DE</th>
                        <th style="text-align:center">TAZA</th>
                        <th style="text-align:center">PENDIENTE</th>
                        <th style="text-align:center">FECHA DESEMBOLSO</th>
                        <th style="text-align:center">DIAS ATRASO</th>
                        <th style="text-align:center">MORA</th>
                        <th style="text-align:center">ASESOR</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      ///fucionalidades
                      $Ttotal = 0;
                      $Tmora = 0;
                      $TTotal2 = 0;
                      $Tcantidad = 0;

                      function verificador($id, $mora, $cuota, $fecha)
                      {
                        $det = extraer("SELECT @id:='$id',@fe:='$fecha',(select count(*) from tpresta_detalle where idP=@id and fechaProg>=@fe and (estado='2' or estado is null))as pendiente,(select count(*) from tpresta_detalle where idP=@id and fechaProg<@fe and (estado='2' or estado is null)) as vencidas,@fechi:=(select fechaProg from tpresta_detalle where idP=@id and (estado='2' or estado is null) limit 1) as fechaProg,(select SUM(cuota) - SUM(if(montoPagado!='',montoPagado,0)) from tpresta_detalle where idP=@id and fechaProg<=@fe) as debe, DATEDIFF(@fe,@fechi) as retra,(select ((saldo+cuota)-(if(montoPagado!='',montoPagado,0))) from tpresta_detalle where idP=@id  and (estado='2' or estado is null) limit 1) as saldo,(select cuota from tpresta_detalle where idP=@id and fechaProg<=@fe limit 1)as cuota, (select sum(pagoMora) from tpresta_detalle where idP=@id and tfechaMora is null and estado='1' ) as demora,(select sum(pagoMora) from tpresta_detalle where idP=@id and tfechaMora is not null and (estado is null or estado='2')) as nodemora FROM tpresta_detalle limit 1");
                        $deta = mysqli_fetch_array($det);

                        $pendiente = "";
                        //cuotas pendientes

                        $pendiente = $deta['pendiente'];

                        $juvenal['0'] = $pendiente;
                        //cuotas vencidas
                        $juvenal['1'] = $deta['vencidas'];

                        //dias de atraso
                        $retra = $deta['retra'];
                        $fechaU = $deta['fechaProg'];
                        //obtener los dias de atraso
                        $atraso = verificar($retra, $fechaU);


                        //atraso de Credito
                        $juvenal['2'] = $atraso;
                        //total pendiente
                        $juvenal['3'] = $deta['saldo'];
                        //cuota
                        $juvenal['4'] = $deta['cuota'];
                        //pendiente hasta ahora
                        $juvenal['5'] = $deta['debe'];
                        //Mora
                        $juvenal['6'] = ($atraso * $mora) + $deta['demora'];
                        if ($deta['nodemora'] != "") {
                          $juvenal['6'] = ($atraso * $mora) - $deta['nodemora'];
                        }
                        return $juvenal;
                      }
                      function verificar($retra, $fecha)
                      {
                        $com = "1 days";
                        $total = 0;
                        $año = "";
                        for ($i = 0; $i < $retra; $i++) {
                          $fecha = date("d-m-Y", strtotime($fecha . "+" . $com));

                          $dia = date("w", strtotime($fecha));

                          if ($dia == '0') {
                          } else {
                            if (feriados($fecha) == '1') {
                              //  $año=feriados3($fecha);
                            } else {
                              $total++;
                              //$año=feriados3($fecha);
                            }
                          }
                        }
                        //  $total=$año;
                        return $total;
                      }
                      function feriados($fec)
                      {
                        $fec2 = preg_split("~-~", $fec);
                        $fechi = "$fec2[0]/$fec2[1]";
                        $año = $fec2[2];
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
                      while ($row = mysqli_fetch_array($consul)) {
                        $codigo = $row['id'];

                        //estado  y Cuotas
                        $eC = extraer("SELECT if(estado=4,'ACTIVO','INACTIVO') as estado,plazo,idP,mora,n_cuota from tprestamo where idP='$codigo' ");
                        $r1 = mysqli_fetch_array($eC);
                        $estado = $r1['INACTIVO'];
                        $id = $r1['idP'];
                        $mora = $r1['mora'];
                        $ncuota = $r1['n_cuota'];
                        $plazo = "";
                        $prueba = "";
                        //empezamos la managing_apps
                        //cuotas pendientes
                        $pendiC = "";
                        //cuotas vencidas
                        $venci = "";
                        //atraso de Credito
                        $atra = "";
                        //total pendiente
                        $pendiT = "";
                        //cuota
                        $cuo = "";
                        //pendiente hasta ahora
                        $pendiH = "";
                        //Mora
                        $mor = "";
                        //*******************lapsus
                        $fecha = date("Y-m-d");
                        //$fecha=date("2019-05-13");

                        if ($r1['estado'] != "") {
                          $estado = $r1['estado'];
                          if ($estado == "ACTIVO") {
                            $plazo = $r1['plazo'];
                            $fecha = date("Y-m-d");
                            //$fecha=date("2019-05-12");
                            $prueba = verificador($id, $mora, $ncuota, $fecha);
                            //cuotas pendientes
                            $pendiC = $prueba['0'];
                            //cuotas vencidas
                            $venci = $prueba['1'];
                            //atraso de Credito
                            $atra = $prueba['2'];
                            //total pendiente
                            $pendiT = $prueba['3'];
                            //cuota
                            $cuo = $prueba['4'];
                            //pendiente hasta ahora
                            $pendiH = $prueba['5'];
                            //Mora
                            $mor = $prueba['6'];
                          }
                        }

                        $fechis = preg_split("~-~", $row['desem']);
                        $fe = "$fechis[2]/$fechis[1]/$fechis[0]";

                        if ($row['saldo'] == "0.00" && $mor == "0") {
                          $id = $row['id'];
                          enviar("UPDATE tprestamo set estado='5' where idP='$id'");
                        } else if ($atra < $atrazoInicio || $atra > $atrazoFin) {
                          continue;
                        } else if ($atra > 180) {
                          continue;
                        } else {
                      ?>
                          <tr>
                            <td><?php echo $row['dni']; ?></td>
                            <td><?php echo $row['dato']; ?></td>
                            <td><?php echo $row['dir']; ?></td>
                            <td><?php echo $row['cel']; ?></td>
                            <td><?php echo 'S/. ' . $row['mont']; ?></td>
                            <td><?php echo $row['taza'] . ' %'; ?></td>
                            <td><?php echo 'S/. ' . $row['saldo']; ?></td>
                            <td><?php echo $fe ?></td>
                            <td><?php echo $atra ?></td>
                            <td><?php echo 'S/. ' . $mor ?></td>
                            <td><?php echo $row['usuario']; ?></td>
                          </tr>

                      <?php
                        }
                        $Tmora += $mor;
                        $Ttotal += $row['saldo'];
                        $Tcantidad++;
                      }
                      $TTotal2 = $Ttotal + $Tmora;
                      ?>
                    </tbody>
                  </table>
                </div>
                <div class="col-sm-3 col-xs-3">
                  <label for="">MONTO POR COBRAR:</label>
                  <input type="text" class="form-control" style="color:blue;text-align:right;font-weight:bold" name="" value="<?php echo $Ttotal; ?>">
                </div>
                <div class="col-sm-3 col-xs-3">
                  <label for="">MORA POR COBRAR:</label>
                  <input type="text" class="form-control" style="color:blue;text-align:right;font-weight:bold" name="" value="<?php echo $Tmora; ?>">
                </div>
                <div class="col-sm-3 col-xs-3">
                  <label for="">TOTAL POR COBRAR</label>
                  <input type="text" class="form-control" style="color:blue;text-align:right;font-weight:bold" name="" value="<?php echo $TTotal2; ?>">
                </div>
                <div class="col-sm-3 col-xs-3">
                  <label for="">CANTIDAD DE CREDITOS</label>
                  <input type="text" class="form-control" style="color:blue;text-align:right;font-weight:bold" name="" value="<?php echo $Tcantidad; ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="extra/min.js"></script>
<script>
  function imprimir_ahorro(nombreDiv) {
    //  VentanaCentrada('./pdf/documentos/ver_ahorro.php?idahorro='+idahorro,'Ahorro','','1024','768','true');
    var contenido = document.getElementById(nombreDiv).innerHTML;
    var contenidoOriginal = document.body.innerHTML;

    document.body.innerHTML = contenido;

    window.print();

    document.body.innerHTML = contenidoOriginal;
  }
</script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    $('.dataTables-example').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        /*{ extend: 'copy'},
        {extend: 'csv'},*/
        {
          extend: 'excel',
          title: 'Deudores '
        },
        /*{extend: 'pdf', title: 'ExampleFile'},*/
        {
          extend: 'print',
          customize: function(win) {
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', 'inherit');
          }
        }
      ]
    });
  });
</script>

<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>