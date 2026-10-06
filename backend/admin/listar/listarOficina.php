<?php include('../conection/bdcredito.php');
$usus=extraer("(select tof.idO,depa,provi,prov, distri,distrito, direccion, telefono, concat('img2/user/',img) as img1, concat(apU,' ', amU,' ', nomU) as rpo,responsable, correo FROM toficina tof inner join ta_prov tp on tof.prov=tp.idprov inner join ta_dis ta on tof.distrito=ta.iddis inner join tusuario tu on tof.responsable=tu.idU  order by provi,distri asc )
  union
  (select tof.idO,depa,provi,prov, distri,distrito, direccion, telefono, ('img2/sede/a1.png') as img1, ('SIN RESPONSABLE') as rpo,responsable, correo FROM toficina tof inner join ta_prov tp on tof.prov=tp.idprov inner join ta_dis ta on tof.distrito=ta.iddis where responsable is null or responsable='' order by provi,distri asc)");
?>
<div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1">OFICINAS</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <div class="panel-body">

      <?php
        while($row =mysqli_fetch_array($usus))
        {
        ?>
        <div class="col-lg-3" >
            <div class="contact-box center-version" >
                <a href="" style="height:380px">
                    <img alt="image" class="img-circle" src="<?php echo $row['img1']; ?>">
                    <h3 class="m-b-xs"><?php echo $row['rpo'];  ?></h3>
                    <div class="font-bold"><strong>RESPONSABLE</strong></div>

                    <address class="m-t-md">
                      <strong>Datos:</strong><br>
                      <strong>Prov.:</strong> <?php echo $row['provi'] ?><br>
                      <strong>Distr.:</strong> <?php echo $row['distri'] ?><br>
                      <strong>Direc.:</strong> <?php echo $row['direccion'] ?><br>
                      <strong>Tel.: </strong>  <?php echo $row['telefono']; ?><br>
                      <strong>Email: </strong> <?php echo $row['correo']; ?><br>

                    </address>
                </a>
                <div class="contact-box-footer" >
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-sm btn-white" title="<?php echo $row['telefono']; ?>" href="tel:<?php echo $row['telefono']; ?>"><i class="fa fa-phone"></i> Call </a>
                        <a class="btn btn-sm btn-white" title="<?php echo $row['correo'] ?>" href="mailto:<?php echo $row['correo'] ?>"><i class="fa fa-envelope"></i> Email</a>

                        <a data-toggle="tab" href="#tab-2" onclick="je(this.id)" id="<?php echo "juve".$row['idO']; ?>"  class='btn btn-sm btn-default juve7' title='Agregar Responsable'
                        data-pro='<?php echo $row['provi'] ;?>'
                        data-ds="<?php echo $row['distri']; ?>"
                        data-dr="<?php echo $row['direccion']; ?>"
                        data-usu="<?php echo $row['rpo']; ?>"
                        data-ido="<?php echo $row['idO']; ?>">
                        <i class="fa fa-handshake-o" style="color:green"></i></a>

                        <a href="#" data-toggle="modal" title='Editar Oficina' data-target="#agreOfi" class='btn btn-sm btn-default'
                          data-prov='<?php echo  $row['prov'];?>'
                          data-depa="<?php echo $row['depa']; ?>"
                          data-dis="<?php echo $row['distrito'];?>"
                          data-direc="<?php echo $row['direccion'];?>"
                          data-tele="<?php echo $row['telefono'];?>"
                          data-ema="<?php echo $row['correo'];?>"
                          data-id="<?php echo $row['idO'];?>">
                        <i class="glyphicon glyphicon-edit" style="color:rgb(13, 72, 107)"></i>
                        </a>
                		    <a href="" class='btn btn-sm btn-default' title='Eliminar Oficina' onclick="eliminar('<?php echo $row['idO'];?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
      </div>
    </div>
    <div id="tab-2" class="tab-pane">
        <div class="panel-body">

            <fieldset class="form-horizontal">
                <div class="form-group"><label class="col-sm-2 control-label">PROV:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" disabled id="oprovi">

                    </div>
                </div>
                <div class="form-group"><label class="col-sm-2 control-label">DIST:</label>
                    <div class="col-sm-10"><input type="text" class="form-control" disabled id="odis"></div>
                </div>
                <div class="form-group"><label class="col-sm-2 control-label">DIREC.:</label>
                    <div class="col-sm-10"><input type="text" disabled class="form-control" id="odire"></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label">Responsable:</label>
                  <div class="col-sm-10"><input type="text" disabled class="form-control" name="resp12" id="resp12"></div>
                </div>
                <div class="form-group">
                  <form method="post" action="add/agrerespo.php">
                  <label class="col-sm-2 control-label">Responsable:</label>
                    <div class="col-sm-5">

                          <input type="hidden" id="idddo" name="idddo" value="">
                          <select class="select2_demo_3 form-control" id="idresponsa" name="idresponsa">
                              <option value="" disabled selected>-- Seleccione--</option>
                              <?php
                                $ususa=extraer("select idU, dniU,apU, amU,nomU from tusuario where tipoU='2'");
                                  while($row =mysqli_fetch_array($ususa))
                                {
                                  ?>
                                  <option value="<?php echo $row['idU'] ?>"> <?php echo $row['dniU']."  ". $row['apU']." ".$row['amU']." ".$row['nomU']?></option>
                                  <?php
                                  }
                                  ?>

                          </select>

                    </div>
                    <div class="col-sm-5">
                      <input type="submit"  id="btnguardar" class="btn btn-info btn-flat " name="" value="Cambiar Resonsable y Volver a Oficinas">
                    </div>
                    </form>
                </div>

                <div class="form-group">
                  <div class="col-sm-5">
                    <a data-toggle="tab" href="#tab-1" class="btn btn-sm btn-default" >Cancelar</a>
                  </div>
                </div>
            </fieldset>
<!--<script src="../functiones/oficina.js">

</script>-->
<script type="text/javascript">
$(document).ready(function(){
        $("#idresponsa").select2({
              minimumResultsForSearch: 5,
              placeholder: "- - Seleccione Responsable - -",
              allowClear: false,
              width: '100%',
          });
  });
</script>
        </div>
    </div>
  </div>

</div>
