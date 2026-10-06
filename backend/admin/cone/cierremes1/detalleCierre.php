<?php
  include('../../conection/bdcredito.php');
  extract($_POST);
    switch ($mesi)
    {
      case 1:
        $mes="January";
        break;
      case 2:
        $mes="February";
        break;
      case 3:
        $mes="March";
        break;
      case 4:
        $mes="April";
        break;
      case 5:
        $mes="May";
        break;
      case 6:
        $mes="June";
        break;
      case 7:
        $mes="July";
        break;
      case 8:
        $mes="August";
        break;
      case 9:
        $mes="September";
        break;
      case 10:
        $mes="October";
        break;
      case 11:
        $mes="November";
        break;
      case 12:
        $mes="December";
        break;
    }

  //====================
  function camari($dato,$anio)
  {
    $f=preg_split("~-~",$dato);
    $resul=$anio."-".$f[1]."-".$f[2];
    if($f[2]."-".$f[1]=="28-02")
    {
      $resul=$anio."-02-29";
    }
    return $resul;
  }
  //====================
  $ini = strtotime('first day of '.$mes, time());
  $ini=date('Y-m-d', $ini);
  $fin = strtotime('last day of '.$mes, time());
  $fin=date('Y-m-d', $fin);
  //====================
  $ini=camari($ini,$ani);
  $fin=camari($fin,$ani);
  //====================
  //SELECTOR DE OFICINA
  $idO=$_COOKIE['tofi'];
  if($_COOKIE['tofi']=="")
  {
    $idO=$ofi;
  }
  $idU=$_COOKIE['user1'];
  if($usua!=0)
  {
    $idU=$usua;
  }

  ///=reconfigure
  $vf=extraer("SELECT tipoU as tipo from tusuario where idU='$idU'");
  $df=mysqli_fetch_array($vf);
  if($df['tipo']==3)
  {
    $cadena=" and tp.tlocal='1' and ";
  }
  else
  {
    $cadena=" and tp.tlocal='2' and tc.idU='$idU' and ";
  }
  //=====================
  //cambio de tipo fecha
  function fra($v)
  {
    $f=preg_split("~-~",$v);
    $resul=$f[2]."/".$f[1]."/".$f[0];
    return $resul;
  }
  //===================
  //recuperacion de mes
  function fre($v)
  {
    $f=preg_split("~-~",$v);
    $resul=$f[1];
    return $resul;
  }
  //===================
?>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#tab-1" id="">REGISTRO</a></li>
  <li ><a data-toggle="tab" href="#tab-2" id="">DETALLE</a></li>
</ul>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="tab-content">
  <div id="tab-1" class="tab-pane active">
      <div class="panel-body">
          <div class="col-xs-12">
              <div class=" center-version" ><!--style="overflow-y:scroll;overflow-x:hidden;height:700px;">-->
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover dataTables-example" >
                      <thead>
                        <tr>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Item</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>;">Cliente </th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Fecha Desembolso</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Tipo Credito</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Tasa %</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Periodo</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Colocación</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Cuotas</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Interes</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">C + I</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Amortización</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Saldo</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Retraso Días</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Moras</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">N° Credito</th>
                            <th style="background-color:<?php echo $jua1['color'] ?>">Tipo Pago</th>
                        </tr>
                      </thead>
                      <tbody>
                <?php
                  //===================
                  $totalclie=0;$tclie[0]="0";$nuevo=0;$re=0;$recurre[0]="0";$t1=0;$t2=0;$t3=0;$col=0;$inte=0;$mor=0;$diar=0;$sem=0;
                  //mes de seleccin
                  $csa="SELECT @ini:='$ini',@fin:='$fin',@ido:='$idO',
                  (select sum(tpd.cuota) from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tofic=@ido ".$cadena." fechaProg>=@ini and fechaProg<=@fin) as cuota,
                  (select if(sum(montoPagado) is null,0,sum(montoPagado))  from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tofic=@ido ".$cadena." fechaPago>=@ini and fechaPago<=@fin) as pagado,
                  (select round((if(sum(tpd.cuota) is null,0,sum(tpd.cuota)) -if(sum(montoPagado)is null,0,sum(montoPagado))),2) as deuda
                  from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tp.estado='4' ".$cadena." tofic=@ido and fechaProg>=@ini and fechaProg<=@fin) as deuda from tpresta_detalle  limit 1";
                  $z=extraer($csa);
                  /*extraer("SELECT @ini:='$ini',@fin:='$fin',@ido:='$idO',(select sum(tpd.cuota) from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP where tofic=@ido and fechaProg between cast(@ini as date) and cast(@fin as date)) as cuota,(select sum(montoPagado) from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP where tofic=@ido and fechaPago between cast(@ini as date) and cast(@fin as date)) as pagado,(select ROUND((sum(tpd.cuota) -sum(montoPagado)),2)as deuda from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP where tp.estado='4' and tofic=@ido) as deuda from tpresta_detalle limit 1");
                  */
                  $zs=mysqli_fetch_array($z);

                  //====montos ingresados distintos a crobro
                  $qw=extraer("SELECT motivo,sum(total) as suma FROM tcaja_usu_detal tcdu inner join tcaja_usuario tcu on tcdu.idCA=tcu.idCA inner join tcaja_oficina tco on tcu.idCO=tco.idCO inner join tahorro_motivo tm on tcdu.tipo=tm.idam  where tco.idO='$idO' and tcu.idU='$idU' and tco.ini>='$ini' and tco.ini<='$fin' and tcdu.estadodt='2' and tcdu.habilitacion is null group by tcdu.tipo order by motivo asc");
                  //echo "SELECT motivo,sum(total) as suma FROM tcaja_usu_detal tcdu inner join tcaja_usuario tcu on tcdu.idCA=tcu.idCA inner join tcaja_oficina tco on tcu.idCO=tco.idCO inner join tahorro_motivo tm on tcdu.tipo=tm.idam  where tco.idO='$idO' and tco.ini>='$ini' and tco.ini<='$fin' and tcdu.estadodt='2' and tcdu.habilitacion is null group by tcdu.tipo order by motivo asc";
                  //====
                  $cosul="SELECT tpd.idP as id, fechaDesembolso from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO'".$cadena." (tp.estado='4' or tp.estado='5') and tp.comentario is null and fechaProg>='$ini' and fechaProg<='$fin' group by tpd.idP union SELECT tp.idP as id,fechaDesembolso from tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO'".$cadena." (tp.estado='4' or tp.estado='5') and tp.comentario is null and fechaDesembolso>='$ini' and fechaDesembolso<='$fin'";
                  $c=extraer($cosul);
                  while ($r=mysqli_fetch_array($c))
                  {
                    $id=$r['id'];
                    $z=extraer("SELECT @id:=tp.idP as id,concat(ap,' ',am,' ',nom) as dato,tp.idCG as idc,tipoP,fechaDesembolso,pago,taza,plazo,montoAprovado,cuota,n_credito as credi,mora,@inte:=round((montoAprovado*(taza/100)),2) as interes,@ci:=(montoAprovado+@inte) as ci,@amr:=(SELECT if(sum(if(montoPagado is null,0,montoPagado)) is null,0,sum(if(montoPagado is null,0,montoPagado))) FROM tpresta_detalle where idP=@id and fechaPago between cast('$ini' as date) and cast('$fin' as date)) as amorti,ROUND((@ci-@amr),2) as saldo,@more:=round((SELECT if(sum(if(pagoMora is null,0,pagoMora)) is null,0,sum(if(pagoMora is null,0,pagoMora))) FROM tpresta_detalle where idP=@id and fechaPago>='$ini' and fechaPago<='$fin'),2) as moras,round((@more/mora)) as retra FROM tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG where idP='$id' and (tp.estado='4' or tp.estado='5')");
                    $x=mysqli_fetch_array($z);

                   $tcredi="";
                   switch ($x['tipoP'])
                   {
                     case 1:
                       $tcredi="Transporte";
                     break;
                     case 2:
                       $tcredi="Comercio";
                     break;
                     case 3:
                       $tcredi="Prendatario";
                     break;
                   }
                   $tpago="";
                   switch ($x['pago']) {
                     case 1:
                       $tpago="DIARIO";
                     break;
                     case 2:
                       $tpago="SEMANAL";
                     break;
                     case 3:
                       $tpago="PAGO UNICO";
                     break;
                     case 4:
                       $tpago="MENSUAL";
                     break;
                   }
                   //====clientes
                   if($mesi==fre($x['fechaDesembolso']))
                   {
                     $totalclie++;
                     $tclie[$totalclie]=$x['idc'];

                     if($x['credi']=='1')
                     {
                       $nuevo++;
                     }
                     else
                     {
                      $re++;
                      $recurre[$re]=$x['idc'];

                     }
                     //===
                     switch ($x['tipoP'])
                     {
                       case 1:
                         $t1++;
                       break;
                       case 2:
                         $t2++;
                       break;
                       case 3:
                         $t3++;
                       break;
                     }
                     //====
                     switch ($x['pago']) {
                       case 1:
                         $diar++;
                       break;
                       case 2:
                         $sem++;
                       break;
                       case 3:
                       break;
                       case 4:
                       break;
                     }
                     //===
                      $col+=$x['montoAprovado'];
                      $inte+=$x['interes'];
                      $mor+=$x['moras'];
                   }
                   ?>
                   <tr>
                     <td style="font-size:10px;"><?php echo $id; ?></td>
                     <td style="font-size:10px;"><?php echo $x['dato'] ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo fra($x['fechaDesembolso']); ?></td>
                     <td style="font-size:10px;"><?php echo $tcredi; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['taza']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['plazo']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['montoAprovado']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['cuota']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['interes']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['ci']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['amorti']; ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['saldo'] ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['retra'] ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['moras'] ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $x['credi'] ?></td>
                     <td style="font-size:10px;text-align:right"><?php echo $tpago ?></td>
                   </tr>
                   <?php
                  }
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Item</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Cliente</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Fecha Desembolso</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Tipo Credito</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Tasa</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Periodo</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Colocación</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Cuotas</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Interes</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">C + I</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Amortización</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Saldo</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Retraso Días</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Moras</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">N° Credito</th>
                    <th style="background-color:<?php echo $jua1['color'] ?>" align="center">Tipo Pago</th>
                  </tr>
                </tfoot>
                </table>

                </div>
          </div>
          </div>
      </div>
  </div>
  <div id="tab-2" class="tab-pane">
    <div class="panel-body">
      <div class="co-xs-12">
        <div class="col-xs-3">
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:200px;text-align:left" onclick="return(false)" >TOTAL CLIENTE</a>
            </span>
              <input type="text" class="form-control" value="<?php echo (count(array_unique($tclie))-1); ?>" style="width:100px;text-align:right">
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;200px;text-align:left" onclick="return(false)" >CLIENTES RECURRENTES</a>
            </span>
              <input type="text" class="form-control" value="<?php echo (count(array_unique($recurre))-1); ?>" style="width:100px;text-align:right">
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:200px;text-align:left" onclick="return(false)">CLIENTES NUEVOS</a>
            </span>
              <input type="text" class="form-control" value="<?php echo $nuevo; ?>" style="width:100px;text-align:right">
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:300px;text-align:left" onclick="return(false)">TIPOS DE CREDITO</a>
            </span>
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:200px;text-align:left" onclick="return(false)">COMERCIAL</a>
            </span>
              <input type="text" class="form-control" value="<?php echo $t1; ?>" style="width:100px;">
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:200px;text-align:left" onclick="return(false)">TRANSPORTE</a>
            </span>
              <input type="text" class="form-control" value="<?php echo $t2; ?>" style="width:100px;">
          </div>
          <br>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold;width:200px;text-align:left" onclick="return(false)">PRENDATARIO</a>
            </span>
              <input type="text" class="form-control" value="<?php echo $t3; ?>" style="width:100px;">
          </div>
        </div>
        <div class="col-xs-1">

        </div>
        <?php //fila 2 ?>
        <div class="col-xs-3">
          <table >
            <tr>
              <td>
                <label for="">Total Colocación</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td align="right">
                <?php echo number_format($col, 2, '.', ''); ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Total de Interes</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo number_format($inte, 2, '.', ''); ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Total Cobrar</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo $zs['cuota']; ?>
              </td>
            </tr>

            <tr>
              <td>
                <label for="">Total Cobrado</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo $zs['pagado']; ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Total Proyectado</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo $zs['deuda']; ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Total Mora</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo number_format($col, 2, '.', ''); ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Creditos Diarios</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo $diar; ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="">Creditos Semanal</label>
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <?php echo $sem; ?>
              </td>
            </tr>
          </table>
        </div>
        <div class="col-xs-3">
          <table>
            <?php
            while ($t=mysqli_fetch_array($qw))
            {
              ?>
                <tr>
                  <td>
                    <?php echo $t['motivo']; ?>
                  </td>
                  <td>
                    <?php echo $t['suma']; ?>
                  </td>
                </tr>
              <?php
            }
            ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
    $(document).ready(function(){
        $('.dataTables-example').DataTable({
            pageLength: 7,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFilea'},
                {extend: 'pdf', title: 'ExampleFile'},
                {extend: 'print',
                 customize: function (win){
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
