
<div class="panel-heading">
    <div class="btn-group pull-right">

      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
  </div>
<h5 style="color:white">Operaciones <small> Desarrollador Juvenal Perez Ramos </small></h5>
</div>
<div class="panel-body">
  <?php //estilos
  include('conection/bdcredito.php');
  date_default_timezone_set('america/lima');
  $f=date("d/m/Y");

  ?>
  <link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
  <link href="fecha/fecha.css" rel="stylesheet" />
  <link href="../css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
  <?php //monto designado por la bobeda ?>
        <div class="form-group row">
          <div class="col-md-3">
            <label class="control-label">ABRIR CON:</label>
            <input type="hidden" name="sal" id="sal" value="<?php //echo $sal; ?>">
            <input type="text" class="form-control" placeholder="Monto Designado" id="sald" name="sald" style="text-align:right;background:white;font-weight:bold" readonly >
          </div>
          <?php //fecha ?>
          <div class="col-md-4">
            <label class="control-label">FECHA :</label>
            <div class="input-group margin">
              <span class="input-group-btn">
                <a class="btn btn-info" onclick="return false"><i class="fa fa-calendar"></i> </a>
              </span>
              <input type="text" class="form-control datepicker" placeholder="dd/mm/aaaa" name="fecha" id="fecha" style="text-align:center" value="<?php echo $f; ?>">
              <span class="input-group-btn">
                <a class="btn btn-info btn-flat" onclick="verInicio()" style="height:34px;font-weight:bold"><i class="glyphicon glyphicon-search"></i></a>
                <a class="btn btn-primary" title="Iniciar Día" onclick="abrirDia()"><i class="fa fa-save"></i> Iniciar Dia</a>
              </span>
            </div>
          </div>

          <div class="col-md-3">
            <input type="hidden" class="form-control" id="fechi" value="">
          </div>
        </div>

        <div class="tabs-container">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">INICIAR</a></li>
            <li><a data-toggle="tab" href="#tab-2" onclick="cajaCerrado()" >CERRAR</a></li>
          </ul>
          <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <div class="panel-body">

                  <label>Se abrio la caja con la fecha: <strong id="fechaAbierta"></strong></label>

                  <div id="iniciarB">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <label class="form-control">SE ABRIO LA CAJA CON: S/.  <strong id="txtabrioCon"> </strong></label>
                        <input type="hidden" class="form-control"  id="dinerOPE" value="<?php// echo $abrio; ?>">
                        <label  class="form-control">SALDO DISPONIBLE: S/. <strong id="saldOPE"></strong></label>
                        <input type="hidden" name="" id="saldOPE2" value="">
                      </div>
                    </div>
                    <?php //designaciones ?>
                    <div class="form-group row">
                      <input type="hidden" name="idCO" id="idCO" >
                      <input type="hidden" name="txtsaldoOperativo" id="txtsaldoOperativo" >
                      <div id="iniOperacionesF">

                      </div>
                    </div>
                    <?php //****** ?>
                    <div class="form-group row" id="iOPEHISTO" style="height: 250px;overflow: auto">
                      <?php //historial ?>
                      <div class="col-md-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                              <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
                                <th>
                                  USUARIO
                                </th>
                                <th>
                                  MONTO
                                </th>
                                <th>
                                  TIPO
                                </th>
                                <th>
                                  ESTADO
                                </th>
                                <th>
                                  OPCIONES
                                </th>
                              </tr>
                            </thead>
                            <tbody id="contenidoRegistroDesignacion">

                            </tbody>
                        </table>
                      </div>

                    </div>
                  </div>
                </div>
            </div>
            <div id="tab-2" class="tab-pane">
              <div class="panel-body">
                <div id="cerraronCaja">


                </div>
              </div>
            </div>
            <div id="tab-3" class="tab-pane">
              <div class="panel-body">
                <div id="detalleAdmin">
                  <?php //******detalle ?>
                <!--  <div class="col-md-9 col-sm-9">
                    <?php //datos importantes
                    $idO=$_COOKIE['co_ido'];
                    $idUsuario=$_COOKIE['co_id'];
                    $fechaCU="2019-10-31";
                    //variables
                    $ingresos=0;
                    $venta=0;
                    $cadel=0;
                    $ingreso=0;
                    $ccredito=0;
                    $designacion=0;

                    //egresoso
                    $egresos=0;
                    $compra=0;
                    $adelanto=0;
                    $gastos=0;

                    $ca=extraer("SELECT idCU as id FROM tcaja_usuario where fecha='$fechaCU' and idU<>'$idUsuario' and idO='$idO'");
                    while ($r=mysqli_fetch_array($ca))
                    {
                      $id=$r['id'];
                      $c=extraer("SELECT SUM(CASE WHEN tipo = '1' THEN monto ELSE 0 END) as compra,SUM(CASE WHEN tipo = '2' THEN monto ELSE 0 END) as venta,SUM(CASE WHEN tipo = '5' THEN monto ELSE 0 END) as adelanto,SUM(CASE WHEN tipo = '6' THEN monto ELSE 0 END) as cadel,SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) as gastos,SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) as ingresos,SUM(CASE WHEN tipo = '9' THEN monto ELSE 0 END) as ccredito,SUM(CASE WHEN tipo = '10' THEN monto ELSE 0 END) as designacion FROM tcaja_usuario_deta where idCU='$id'");
                      $r2=mysqli_fetch_array($c);
                      //ingresos
                      $venta+=$r2['venta'];$cadel+=$r2['cadel'];$ingreso+=$r2['ingresos'];$ccredito+=$r2['ccredito'];$designacion+=$r2['designacion'];
                      //egresos
                      $compra+=$r2['compra'];$adelanto+=$r2['adelanto'];$gastos+=$r2['gastos'];

                    }
                    $ingresos=$venta+$cadel+$ingreso+$ccredito+$designacion;
                    $egresos=$compra+$adelanto+$gastos;

                    ?>
                    <?php //usuarios ?>
                    <div class="col-md-12 col-sm-12">
                      <label for="">USUARIOS</label>
                    </div>
                    <?php //ingresoss ?>
                    <div class="col-md-6">
                       <div class="panel panel-info" >
                          <div class="panel-heading">
                                <h3 class="panel-title" align="center"><b>INGRESOS</b></h3>
                          </div>
                          <?php //ingresos ?>
                          <div class="panel-body">
                                <div class=" table-responsive" >
                                  <table class="table table-responsive" >
                                      <thead>
                                      <tr>
                                          <th>Tipo</th>
                                          <th align="center">Monto</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      <?php //designacion ?>
                                      <?php if($designacion>0){?>
                                      <tr>
                                        <td><?php echo "DESIGNACION DEL DIA"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$designacion; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //cobro ?>
                                      <?php if($venta>0){?>
                                      <tr>
                                        <td><?php echo "VENTAS "; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$venta; ?></td>
                                      </tr>
                                      <?php } ?>

                                      <?php //cobro de adelanto ?>
                                      <?php if($cadel>0){?>
                                      <tr>
                                        <td><?php echo "COBRO DE ADELANTO"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$cadel; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //cobro de credito ?>
                                      <?php if($ccredito>0){?>
                                      <tr>
                                        <td><?php echo "COBRO DE CREDITOS"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$ccredito; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //recibo de ingresos ?>
                                      <?php if($ingreso>0){?>
                                      <tr>
                                        <td><?php echo "OTROS INGRESOS"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$ingreso; ?></td>
                                      </tr>
                                      <?php } ?>
                                    </tbody>
                                  </table>
                                </div>
                          </div>
                          <div class="panel-footer">
                               <div class="form-group row">
                                    <div  align="right">
                                      <label class="col-md-6 control-label">TOTAL INGRESOS S/.:</label>
                                    </div>
                                    <div class="col-md-6">
                                      <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo $ingresos; ?>">
                                    </div>
                              </div>
                          </div>
                        </div>
                    </div>
                    <?php //egresos ?>
                    <div class="col-md-6">
                       <div class="panel panel-info">
                          <div class="panel-heading">
                               <h3 class="panel-title" align="center"><b>EGRESOS</b></h3>
                          </div>
                          <div class="panel-body">
                                <div class=" table-responsive" >
                                  <table class="table table-responsive" >
                                      <thead>
                                      <tr>
                                          <th>Tipo</th>
                                          <th align="center">Monto</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                        <?php //compras ?>
                                        <?php if($compra>0){?>
                                        <tr>
                                          <td><?php echo "COMPRAS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$compra; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php //adelanto ?>
                                        <?php if($adelanto>0){?>
                                        <tr>
                                          <td><?php echo "ADELANTOS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$adelanto; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php //gastos ?>
                                        <?php if($gastos>0){?>
                                        <tr>
                                          <td><?php echo "OTROS GASTOS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$gastos; ?></td>
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                  </table>
                                </div>
                          </div>
                          <div class="panel-footer">
                            <div class="form-group row">
                                 <div  align="right">
                                   <label class="col-md-6 control-label">TOTAL EGRESO S/.:</label>
                                 </div>
                                 <div class="col-md-6">
                                   <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtegreso" value="<?php echo $egresos; ?>">
                                 </div>
                           </div>
                          </div>
                      </div>
                    </div>

                  </div>

                    <?php //administrador ?>
                  <div class="col-md-9 col-sm-9">
                    <div class="col-md-12 col-sm-12">
                      <label for="">ADMINISTRADOR</label>
                    </div>
                    <?php
                    //INGRESOS
                    $ingresos2=0;
                    $venta2=0;
                    $cadel2=0;
                    $ingreso2=0;
                    $ccredito2=0;
                    $designacion2=0;

                    //egresoso
                    $egresos2=0;
                    $compra2=0;
                    $adelanto2=0;
                    $gastos2=0;
                    $ca=extraer("SELECT idCU as id FROM tcaja_usuario where fecha='$fechaCU' and idU='$idUsuario' and idO='$ido'");
                    while ($r=mysqli_fetch_array($ca))
                    {
                      $id=$r['id'];
                      $c=extraer("SELECT SUM(CASE WHEN tipo = '1' THEN monto ELSE 0 END) as compra,SUM(CASE WHEN tipo = '2' THEN monto ELSE 0 END) as venta,SUM(CASE WHEN tipo = '5' THEN monto ELSE 0 END) as adelanto,SUM(CASE WHEN tipo = '6' THEN monto ELSE 0 END) as cadel,SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) as gastos,SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) as ingresos,SUM(CASE WHEN tipo = '9' THEN monto ELSE 0 END) as ccredito,SUM(CASE WHEN tipo = '10' THEN monto ELSE 0 END) as designacion FROM tcaja_usuario_deta where idCU='$id'");
                      $r2=mysqli_fetch_array($c);
                      //ingresos
                      $venta2+=$r2['venta'];$cadel2+=$r2['cadel'];$ingreso2+=$r2['ingresos'];$ccredito2+=$r2['ccredito'];$designacion2+=$r2['designacion'];
                      //egresos
                      $compra2+=$r2['compra'];$adelanto2+=$r2['adelanto'];$gastos2+=$r2['gastos'];

                    }
                    $ingresos2=$venta2+$cadel2+$ingreso2+$ccredito2+$designacion2;
                    $egresos2=$compra2+$adelanto2+$gastos2;

                    $entrada=$ingresos+$ingresos2;
                    $salidas=$egresos+$egresos2;
                    $disponible=$entrada-$salidas;
                    $disponible=number_format($disponible, 2, ".", ".");
                    //billetaje
                    $a=extraer("SELECT total as monto FROM tbilletaje where estado='1' and idU='3' and idO='1' order by idBille desc limit 1");
                    $d=mysqli_fetch_array($a);
                    $billetaje=$d['monto'];
                    $billetaje==number_format($billetaje, 2, ".", ".");

                    $diferencia=$disponible-$billetaje;
                    $diferencia=number_format($diferencia, 2, ".", ".");
                     ?>
                    <?php //ingresoss ?>
                    <div class="col-md-6">
                       <div class="panel panel-info" style="padding:none">
                          <div class="panel-heading">
                                <h3 class="panel-title" align="center"><b>INGRESOS</b></h3>
                          </div>
                          <?php //ingresos ?>
                          <div class="panel-body">
                                <div class=" table-responsive" >
                                  <table class="table table-responsive" >
                                      <thead>
                                      <tr>
                                          <th>Tipo</th>
                                          <th align="center">Monto</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      <?php //designacion ?>
                                      <?php if($designacion2>0){?>
                                      <tr>
                                        <td><?php echo "DESIGNACION DEL DIA"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$designacion2; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //cobro ?>
                                      <?php if($venta2>0){?>
                                      <tr>
                                        <td><?php echo "VENTAS "; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$venta2; ?></td>
                                      </tr>
                                      <?php } ?>

                                      <?php //cobro de adelanto ?>
                                      <?php if($cadel2>0){?>
                                      <tr>
                                        <td><?php echo "COBRO DE ADELANTO"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$cadel2; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //cobro de credito ?>
                                      <?php if($ccredito2>0){?>
                                      <tr>
                                        <td><?php echo "COBRO DE CREDITOS"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$ccredito2; ?></td>
                                      </tr>
                                      <?php } ?>
                                      <?php //recibo de ingresos ?>
                                      <?php if($ingreso2>0){?>
                                      <tr>
                                        <td><?php echo "OTROS INGRESOS"; ?></td>
                                        <td width="84px"> <?php echo "S/. ".$ingreso2; ?></td>
                                      </tr>
                                      <?php } ?>
                                    </tbody>
                                  </table>
                                </div>
                          </div>
                          <div class="panel-footer">
                               <div class="form-group row">
                                    <div  align="right">
                                      <label class="col-md-6 control-label">TOTAL INGRESOS S/.:</label>
                                    </div>
                                    <div class="col-md-6">
                                      <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo $ingresos2; ?>">
                                    </div>
                              </div>
                          </div>
                        </div>
                    </div>
                    <?php //egresos ?>
                    <div class="col-md-6">
                       <div class="panel panel-info">
                          <div class="panel-heading">
                               <h3 class="panel-title" align="center"><b>EGRESOS</b></h3>
                          </div>
                          <div class="panel-body">
                                <div class=" table-responsive" >
                                  <table class="table table-responsive" >
                                      <thead>
                                      <tr>
                                          <th>Tipo</th>
                                          <th align="center">Monto</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                        <?php //compras ?>
                                        <?php if($compra2>0){?>
                                        <tr>
                                          <td><?php echo "COMPRAS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$compra2; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php //adelanto ?>
                                        <?php if($adelanto2>0){?>
                                        <tr>
                                          <td><?php echo "ADELANTOS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$adelanto2; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php //gastos ?>
                                        <?php if($gastos2>0){?>
                                        <tr>
                                          <td><?php echo "OTROS GASTOS"; ?></td>
                                          <td width="84px"> <?php echo "S/. ".$gastos2; ?></td>
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                  </table>
                                </div>
                          </div>
                          <div class="panel-footer">
                            <div class="form-group row">
                                 <div  align="right">
                                   <label class="col-md-6 control-label">TOTAL EGRESO S/.:</label>
                                 </div>
                                 <div class="col-md-6">
                                   <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtegreso" value="<?php echo $egresos2; ?>">
                                 </div>
                           </div>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-9 col-sm-9">
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="col-md-12 col-sm-12">
                          <label class="control-label">MONTO DISPONIBLE (S/.):</label>
                          <input  type="text" style="text-align:center;color:blue;font-weight:bold;background:white" class="form-control input-sm" id="txtmontoDis" value="<?php echo $disponible; ?>">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-12 col-sm-12" >
                          <label class="control-label">MONTO BILLETAJE (S/.):</label>
                          <input style="text-align:center;color:blue;font-weight:bold;background:white" type="text" class="form-control input-sm" id="txtbilletaje" value="<?php echo $billetaje; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                      <label class="control-label">DIFERENCIA (S/.):</label>
                      <input style="text-align:center;color:red;font-weight:bold;background:white" type="text" onkeypress="verificacion()" class="form-control input-sm" id="txtdiferencia" value="<?php echo $diferencia; ?>">
                    </div>
                    </div>
                    <div align="right" class="panel-footer">
                    <?php //if($fin==0 && $r['define']=="") {?>
                       <a class="btn btn-info" id="btnCerrarDia" onclick="cerrarCaja()"><i class="fa fa-yelp"></i> Cerrar Dia</a>
                      <?php /*}else if($fin!=0){ ?>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">GENERAR RECIBO</button>
                      <?php }*/ ?>
                    </div>
                    <?php //******* ?>
                  </div>-->
                </div>
              </div>
            </div>
          </div>
       </div>
  </div>


<script src="extra/min.js"></script>

<?php //para la fecha  ?>
<script src="fecha/moment.js"></script>
<script src="fecha/bootstrap-material-datetimepicker.js"></script>
<script src="contenido/caja/admin/codigo.js"></script>
<script>
 $('.datepicker').bootstrapMaterialDatePicker({
     weekStart: 0,
     time: false,
     format: 'DD/MM/YYYY'
 });
</script>
