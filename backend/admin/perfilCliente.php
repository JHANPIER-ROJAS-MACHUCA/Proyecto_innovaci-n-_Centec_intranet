<?php include('head.php'); ?>
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<link href="../css/plugins/switchery/switchery.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
          <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
         </div>
         <h5 style="color:white">Perfil del Cliente<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
      </div>
      <div class="panel-body">
        <div class="tabs-container">
          <link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
          <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
          <?php //empezamos ?>

          <?php
          if(!isset($_REQUEST['idCG']))
          {
             echo "<script>location.href='clientes.php'</script>";
          }
             $idCliente = $_REQUEST['idCG'];
             $da=extraer("SELECT idCG,nom, ap, am,correo,telefono,direc,sexo,imgC,cel FROM tclie_general WHERE idCG='$idCliente'");
             $info=mysqli_fetch_array($da);

           ?>
           <div class="form-group">
             <div class="col-sm-4">
               <label ><?php echo "CLIENTE : ".$info['nom'].' '.$info['ap'].' '.$info['am']; ?></label>
             </div>
           </div>
           <input type="hidden" id="idClie" value="<?php echo $idCliente; ?>">

          <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#tab-1">Datos</a></li>
              <li class=""><a data-toggle="tab" href="#tab-2">Direcciones</a></li>
              <li class=""><a data-toggle="tab" href="#tab-3">Negocios</a></li>
              <li class=""><a data-toggle="tab" href="#tab-4">Prestamos</a></li>
          </ul>
          <div class="tab-content">
              <div id="tab-1" class="tab-pane active">
                  <div class="panel-body">
                    <fieldset class="form-horizontal">
                      <div class="form-group">
                        <div class="col-md-2">
                          <div class="profile-image">
                            <a class="fun"  data-toggle="modal" data-target="#fotoC<?php echo $info['idCG']; ?>">
                              <?php
                                if($info['imgC']== null)
                                {
                                  if($info['sexo']=="F")
                                  {
                                    ?>
                                        <img alt="image" class="img-circle" src="img/profile2.jpg">
                                    <?php
                                  }
                                  else if($info['sexo']=="M")
                                  {
                                    ?>
                                        <img alt="image" class="img-circle" src="img/profile.jpg">
                                    <?php
                                  }else{
                                     ?>
                                        <img alt="image" class="img-circle" src="img/profile3.jpg">
                                    <?php
                                  }
                                }
                                else
                                {
                                  ?>
                                      <img alt="image" class="img-circle" src="img2/clie/<?php echo $info['imgC']; ?>">
                                  <?php
                                }
                               ?>
                            </a>
                          </div>
                          <?php include("modal/fotoC.php");?>
                        </div>
                        <div class="col-md-10">
                          <?php //Nombre ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >Nombre(s)</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['nom']; ?>
                            </div>
                          </div>
                          <?php //apellido ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >Apellidos</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['ap'].' '.$info['am']; ?>
                            </div>
                          </div>
                          <?php //email ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >E-mail</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['correo']; ?>
                            </div>
                          </div>
                          <?php //telefono ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >Telefono / Cel</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['telefono']." / ".$info['cel']; ?>
                            </div>
                          </div>
                          <?php //celular ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >E-mail</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['correo']; ?>
                            </div>
                          </div>
                          <?php //direccion ?>
                          <div class="row form-group ">
                            <div class="col-md-2">
                              <label >Dirección</label>
                            </div>
                            <div class="col-md-8">
                              <?php echo ": ".$info['direc']; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  </div>
              </div>
              <div id="tab-2" class="tab-pane">
                  <div class="panel-body">
                    <fieldset class="form-horizontal">
                        <div class="form-group">
                        <?php //departamento ?>
                         <div class="col-sm-3">
                          <label >DEPARTAMENTO</label>
                          <select class="form-control m-b" name="txtdepa" id="txtdepa" required>
                            <option value="">--Seleccione--</option>
                          </select>
                        </div>
                        <?php //provincia ?>
                         <div class="col-sm-3">
                            <label >PROVINCIA</label>
                            <select class="form-control m-b" name="txtprovi" id="txtprovi" required>
                              <option value="" disabled selected >- -Seleccione--</option>
                            </select>
                          </div>
                        <?php //distrito ?>
                          <div class="col-sm-3">
                            <label >DISTRITO</label>
                            <select class="form-control m-b" name="txtdis" id="txtdis" required>
                              <option value="">--Seleccione--</option>
                            </select>
                          </div>
                       </div>
                        <div class="form-group">
                          <?php //anexo ?>
                            <div class="col-sm-3">
                              <label>LUGAR</label>
                              <input type="text" id="lugar" maxlength="45" class="form-control" name="" placeholder="Lugar donde se encuentra" value="">
                            </div>
                          <?php //direccion  ?>
                           <div class="col-sm-3">
                            <label>DIRECCIÓN</label>
                            <input type="text" id="direccion" class="form-control" name="" placeholder="Direccion" value="">
                          </div>
                          <?php //referencia ?>
                           <div class="col-sm-3">
                            <label>REFERENCIA</label>
                            <input type="text" id="referencia" class="form-control" name="" placeholder="Frente a, al costado de." value="">
                          </div>
                        </div>
                        <?php //BOTONES ?>
                        <div class="form-group">
                          <div class="col-sm-6">
                            <input type="hidden" name="" id="idEdiDirec" class="form-control" value="">
                            <a class="btn btn-sm btn-info" onclick="enviar()" title="Guardar"><i class="fa fa-save"> </i>  Guardar</a>

                            <a class="btn btn-sm btn-danger" onclick="limpiar()" title="Cancelar y/o limpiar">Cancelar</a>
                          </div>
                        </div>
                        <div class="form-group">
                          <div id="listarDirec">

                          </div>
                        </div>
                    </fieldset>
                  </div>
              </div>
              <div id="tab-3" class="tab-pane">
                  <div class="panel-body">
                    <fieldset class="form-horizontal">
                      <div class="form-group">
                        <?php //direccion  ?>
                         <div class="col-md-3">
                          <label>DIRECCIÓN</label>
                          <input type="text" id="direccion2" class="form-control" name="" placeholder="Direccion" value="">
                        </div>

                        <?php //TIPO ?>
                         <div class="col-md-3">
                          <label>TIPO</label>
                          <select class="form-control" id="txttipo1" title="Tipo de cleinte">
                            <option value="" disabled selected>- - Seleccione - -</option>
                            <option value="AMBULANTE">AMBULANTE</option>
                            <option value="PUESTO MERCADO">PUESTO MERCADO</option>
                            <option value="ESTABLECIMIENTO">ESTABLECIMIENTO</option>
                          </select>
                        </div>
                        <?php //tipo de negocio ?>
                        <div class="col-md-3">
                          <label>TIPO DE NEGOCIO</label>
                          <select class="form-control" id="txttipoNego" name="">
                            <option value="" disabled selected>--Seleccione--</option>
                            <option value="COMERCIO">COMERCIO</option>
                            <option value="SERVICIO">SERVICIO</option>
                            <option value="PRODUCCION">PRODUCCION</option>
                          </select>
                        </div>
                        <?php //EL NEGOCIO ES ?>
                        <div class="col-md-3">
                          <label>TIPO DE LOCAL</label>
                          <select class="form-control" id="txttipoLocal" name="">
                            <option value="" disabled selected>--Seleccione--</option>
                            <option value="PROPIO">PROPIO</option>
                            <option value="ALQUILADO">ALQUILADO</option>
                          </select>
                        </div>
                        <?php //TIEMPO DE ACTIVIDAD ?>
                        <div class="col-md-3">
                          <label>TIEMPO DE ACTIVIDAD</label>
                          <input type="text" class="form-control" id="txttiempo" placeholder="El tiempo en el rubro." name="" value="">
                        </div>
                        <?php //botones ?>
                        <div class="col-md-3">
                          <div style="padding-top:24px">
                              <a class="btn btn-sm btn-info" onclick="enviar1()"><i class="fa fa-save" style=""> </i> Guardar</a>
                              <a class="btn btn-sm btn-danger" onclick="linNego()"> Limpiar </a>
                          </div>

                        </div>
                      </div>
                      <div class="form-group">
                        <div id="listarNegoClie">

                        </div>
                      </div>
                    </fieldset>
                  </div>
              </div>
              <div id="tab-4" class="tab-pane">
                  <div class="panel-body">
                    <fieldset class="form-horizontal">
                      <div class="form-group">
                        <div class="progress progress-striped active m-b-sm" id="barra" style="display:none">
                            <div style="width:0%;" class="progress-bar" id="canti"><strong id="conteo">0</strong>%</div>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-a1">Detalle</a></li>

                        </ul>
                        <div id="resulPre">

                        </div>

                      </div>
                    </fieldset>
                  </div>
              </div>
          </div>

        </div>
      </div>
</div>
<script src="extra/swetalert.js"></script>
<script src="functiones/direccion.js"></script>
<script src="extra/min.js"></script>


<?php include('footer.php'); ?>
<?php // swicth?>
<script src="../js/plugins/switchery/switchery.js"></script>
 <!-- iCheck -->
 <script src="../js/plugins/iCheck/icheck.min.js"></script>
 <!-- Image cropper -->
 <script src="../js/plugins/cropper/cropper.min.js"></script>
 <!-- Tags Input -->
 <script src="../js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>

<script src="extra/buscador.js">
</script>
<script src="fecha/moment.js"></script>
<script src="fecha/bootstrap-material-datetimepicker.js"></script>
<!--modificar imagen cliente-->
<script src="../js/fileinput.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/fileinput.min.css">
 <script type="text/javascript">
  function actualizar2(){location.reload();}
//Función para actualizar cada 4 segundos(4000 milisegundos)
</script>
