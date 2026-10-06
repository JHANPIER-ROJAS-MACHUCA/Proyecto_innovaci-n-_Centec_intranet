<?php include('../conection/bdcredito.php'); ?>
<?php
  $usus=extraer("(select idU, dniU, pass, apU, amU,
  nomU, celU, direcU, correoU, if(tipoU='1','GERENTE',(if(tipoU='2','ADMINISTRADOR',(IF(tipoU='3','OPERADOR',(IF(tipoU='4','ASESOR','JEFE DE OPERACIONES'))))))) as tipo,tipoU, @id:=idO, estadoU,img,(SELECT concat(distri,' ',direccion) as dato FROM toficina tof inner join ta_dis ta on tof.distrito=ta.iddis where idO=@id) as datos
  FROM tusuario  where tipoU!='7' and idO !='')
  union
  (select idU, dniU, pass, apU, amU,
  nomU, celU, direcU, correoU, if(tipoU='1','GERENTE',(if(tipoU='2','ADMINISTRADOR',(IF(tipoU='3','OPERADOR','ASESOR'))))) as tipo,tipoU, idO, estadoU,img,'Sin oficina' as datos
  FROM tusuario where tipoU!='7' and idO is null or idO='')");
  while($row =mysqli_fetch_array($usus))
    {
      ?>
          <div class="col-lg-3">
              <div class="contact-box center-version">
                  <a href="" style="height:380px">
                      <img alt="image" class="img-circle" src="img2/user/<?php echo $row['img']; ?>">
                      <h3 class="m-b-xs"><strong><?php echo $row['apU']." ".$row['amU']." ".$row['nomU'];  ?></strong></h3>
                      <div class="font-bold"><?php echo $row['tipo']; ?></div>

                      <address class="m-t-md">
                        <strong>Datos</strong><br>
                        <strong>Dni:</strong> <?php echo $row['dniU'] ?><br>
                        <strong>Direc.:</strong> <?php echo $row['direcU'] ?><br>
                        <strong>Cel: </strong>  <?php echo $row['celU']; ?><br>
                        <strong>Email: </strong> <?php echo $row['correoU']; ?><br>
                        <strong>Oficina: </strong> <?php echo $row['datos']; ?>
                      </address>
                  </a>
                  <div class="contact-box-footer" >
                      <div class="m-t-xs btn-group">
                          <a class="btn btn-sm btn-white" title="<?php echo $row['celU']; ?>" href="tel:<?php echo $row['celU']; ?>"><i class="fa fa-phone"></i> Call </a>
                          <a class="btn btn-sm btn-white" title="<?php echo $row['correoU'] ?>" href="mailto:<?php echo $row['correoU'] ?>"><i class="fa fa-envelope"></i> Email</a>

                          <a href="#" data-toggle="modal" title='Editar Usuario' data-target="#agreUser" class='btn btn-sm btn-default'
                            data-dni='<?php echo  $row['dniU'];?>'
                            data-ap="<?php echo $row['apU'];?>"
                            data-am="<?php echo $row['amU'];?>"
                            data-nom="<?php echo $row['nomU'];?>"
                            data-direc="<?php echo $row['direcU']; ?>"
                            data-ema="<?php echo $row['correoU'];?>"
                            data-cel="<?php echo $row['celU'];?>"
                            data-tipo="<?php echo $row['tipoU']; ?>"
                            data-ofi="<?php echo $row['@id']; ?>"
                            data-img="<?php echo $row['img']; ?>"
                            data-id="<?php echo $row['idU'];?>"
                            data-estado="<?php echo $row['estadoU'];?>">
                          <i class="glyphicon glyphicon-edit" style="color:rgb(13, 72, 107)"></i>
                        </a>
                  		<a href="" class='btn btn-sm btn-default' title='Borrar Usuario' onclick="eliminar('<?php echo $row['idU']; ?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
                      </div>
                  </div>
              </div>
          </div>
          <?php
          }
          ?>
