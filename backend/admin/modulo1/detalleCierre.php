<?php
  include('../conection/bdcredito.php');
  /*$idO=$_COOKIE['co_ido'];
  $idU=$_COOKIE['co_id'];*/
  extract($_POST);
  function ordenFecha($dato)
  {
    $f=preg_split("~/~",$dato);
    $fecha=$f[2]."-".$f[1]."-".$f[0];
    return $fecha;
  }
/*
  $fe="30/12/2019";
  $id="4";*/
    $fecha=ordenFecha($fe);
    $idU=$id;

    //consulta para obtener el ingreso y egreso
    /*$consul=extraer("select @id:=idCA,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='3' and estadodt='2') as cobro,(SELECT sum(if(monto>0,monto,0)) FROM tcaja_usu_detal where idCA=@id and tipo='1' and habilitacion='4' and estadodt='2') as designacion,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='2' and estadodt='2') as desembolso,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='4' and estadodt='2') as gastoadmin,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='5' and estadodt='2') as recibo,
  (SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='7' and estadodt='2') as ahorro,
  (SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='8' and estadodt='2') as retiro,montofin as define from tcaja_usuario tu inner join tcaja_oficina tof on tof.idCO=tu.idCO where idU='$idU' and ini='$fecha'");*/

  //==========================================================================================
  //mi consulta en cadena
  $variacion="select @id:=idCA,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='3' and estadodt='2') as cobro,(SELECT sum(if(monto>0,monto,0)) FROM tcaja_usu_detal where idCA=@id and tipo='1' and habilitacion='4' and estadodt='2') as designacion,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='2' and estadodt='2') as desembolso,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='4' and estadodt='2') asgastoadmin,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='5' and estadodt='2') as recibo,(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='8' and estadodt='2') as retiro,";
  //==================
  //creamos la consulta para recolectar las operaciones de ingreso y egreso
  $operac=extraer("SELECT idam as id,motivo,tipoM FROM tahorro_motivo where estado='1' order by tipoM asc");
  $eg=0;
  $ing=0;
  $tipo="i";
  $motiIngre;
  $motiEgre;

  while ($cx=mysqli_fetch_array($operac))
  {
    if($cx['tipoM']=='2')
    {
      $eg++;
      $tipo="e".$eg;
      $motiEgre[$eg]=$tipo.",".$cx['motivo'];
    }
    else
    {
      $ing++;
      $tipo="i".$ing;
      $motiIngre[$ing]=$tipo.",".$cx['motivo'];
    }
    $ida=$cx['id'];
    $variacion.="(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo=".$ida." and estadodt='2') as ".$tipo.",";
  }

  //==================
  //(SELECT sum(if(total>0,total,0)) FROM tcaja_usu_detal where idCA=@id and tipo='7' and estadodt='2') as ahorro
    $variacion.="montofin as define from tcaja_usuario  tu inner join tcaja_oficina tof on tof.idCO=tu.idCO where idU='$idU' and ini='$fecha' order by idCA desc limit 1";

    $consul=extraer($variacion);
    $r=mysqli_fetch_array($consul);
    $total1=0;
    if(!empty($r['cobro']))
    {
      $total1+=$r['cobro'];
    }
    if(!empty($r['designacion']))
    {
      $total1+=$r['designacion'];
    }/*
    if(!empty($r['recibo']))
    {
      $total1+=$r['recibo'];
    }
    $ahorro=0;
    if(!empty($r['ahorro']))
    {
      $ahorro+=$r['ahorro'];
    }*/

    $total2=0;/*
    if(!empty($r['gastoadmin']))
    {
      $total2+=$r['gastoadmin'];
    }*/
    if(!empty($r['desembolso']))
    {
      $total2+=$r['desembolso'];
    }

    $retiro=0;
    if(!empty($r['retiro']))
    {
      $retiro+=$r['retiro'];
    }
/*
    $disponible=($total1+$ahorro)-($total2+$retiro);
    $disponible=number_format($disponible, 2, '.', '');
    $fin=$bille-$disponible;*/

    ?>
    <div class="col-md-12 col-sm-12">
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
                        <?php if($r['designacion']>0){?>
                        <tr>
                          <td><?php echo "DESIGNACION DEL DIA"; ?></td>
                          <td width="84px"> <?php echo "S/. ".$r['designacion']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php //cobro ?>
                        <?php if($r['cobro']>0){?>
                        <tr>
                          <td><?php echo "COBROS DE PRESTAMO"; ?></td>
                          <td width="84px"> <?php echo "S/. ".$r['cobro']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php //recibo de ingresos ?>
                        <?php /*if($r['recibo']>0){?>
                        <tr>
                          <td><?php echo "OTROS INGRESOS"; ?></td>
                          <td width="84px"> <?php echo "S/. ".$r['recibo']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php //ahorro ?>
                        <?php if($ahorro>0){?>
                        <tr>
                          <td><?php echo "AHORROS"; ?></td>
                          <td width="84px"> <?php echo "S/. ".$ahorro; ?></td>
                        </tr>
                        <?php }*/
                        if($ing>0)
                        {
                          for ($i=1; $i <= count($motiIngre); $i++)
                          {
                            $ide=preg_split("~,~",$motiIngre[$i]);
                            if($r[$ide[0]]>0)
                            {
                              $total1+=$r[$ide[0]];
                            ?>
                            <tr>
                              <td><?php echo $ide[1]; ?></td>
                              <td width="84px"> <?php echo "S/. ".$r[$ide[0]]; ?></td>
                            </tr>
                            <?php
                            }
                          }
                        }
                      ?>
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
                        <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo ($total1+$ahorro); ?>">
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
                          <?php //designacion ?>
                          <?php /*if($r['gastoadmin']>0){?>
                          <tr>
                            <td><?php echo "GASTOS ADMINISTRATIVOS"; ?></td>
                            <td width="84px"> <?php echo "S/. ".$r['gastoadmin']; ?></td>
                          </tr>
                          <?php } */?>
                          <?php //cobro ?>
                          <?php if($r['desembolso']>0){?>
                          <tr>
                            <td><?php echo "DESEMBOLSO"; ?></td>
                            <td width="84px"> <?php echo "S/. ".$r['desembolso']; ?></td>
                          </tr>
                          <?php } ?>
                          <?php //retiro ?>
                          <?php if($retiro>0){?>
                          <tr>
                            <td><?php echo "RETIRO"; ?></td>
                            <td width="84px"> <?php echo "S/. ".$retiro; ?></td>
                          </tr>
                          <?php }
                         //egresos
                         if($eg>0)
                         {
                           for ($i=1; $i <= count($motiEgre); $i++)
                           {
                           //  echo $motiIngre[$i]."<br>";
                             $ide=preg_split("~,~",$motiEgre[$i]);
                             //echo $ide[1]." => ".$r[$ide[0]]."<br>";
                             if($r[$ide[0]]>0)
                             {
                               $total2+=$r[$ide[0]];
                             ?>
                             <tr>
                               <td><?php echo $ide[1]; ?></td>
                               <td width="84px"> <?php echo "S/. ".$r[$ide[0]]; ?></td>
                             </tr>
                             <?php
                             }
                           }
                         }

                            //===============================================
                            ?>
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
                     <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtegreso" value="<?php echo ($total2+$retiro); ?>">
                   </div>
             </div>
            </div>
        </div>
      </div>

  </div>
  <?php
  $disponible=($total1)/*+$ahorro)*/-($total2+$retiro);
  $disponible=number_format($disponible, 2, '.', '');
   ?>
  <div class="col-md-6">
    <div class="form-group">
      <div class="col-md-12 col-sm-12">
        <label class="control-label">MONTO DISPONIBLE (S/.):</label>
        <input disabled type="text" style="text-align:center;color:blue;font-weight:bold;background:white" class="form-control input-sm" id="txtmontoDis" value="<?php echo $disponible; ?>">
      </div>
    </div>

  </div>

  <div align="right" class="panel-footer">

        <button type="button" onclick="cerrarCaja()" class="btn btn-info"><i class="fa fa-print"></i> Imprimir</button>

  </div>
