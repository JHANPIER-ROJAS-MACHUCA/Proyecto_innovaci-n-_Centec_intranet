<div class="modal fade le" id="agreUser">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
          <div class="col-lg-12">
            <div class="ibox float-e-margins">
              <div class="ibox-title j7">
                <h5 id="tituU">Nuevo Usuario<small>.</small></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="load(1)">&times;</button>
              </div>
              <div class="ibox-content">
                <form id="aUsuario" class="form-horizontal" enctype="multipart/form-data">
                  <div class="form-group">
                    <label class="col-sm-2 control-label">DNI</label>
                    <div class="col-sm-10">
                      <input type="hidden" name="idue" id="idue" class="form-control" placeholder="ide del usuario a editar">
                      <input type="text" class="form-control" accesskey="s" onkeydown="tecleo(event)" onkeyup="dni2('txtdni','txtap','txtam','txtnom')" placeholder="Ingrese número de DNI" onkeypress="return numero(event)" autocomplete="off" id="txtdni" name="txtdni" maxlength="8" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Apellido Paterno</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" required placeholder="Apellido Paterno" autocomplete="off" id="txtap" name="txtap">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Apellido Materno</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" required placeholder="Apellido Materno" autocomplete="off" id="txtam" name="txtam">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Nombres</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" required placeholder="Nombre" id="txtnom" autocomplete="off" name="txtnom">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label"># Celular</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Número celular" onkeypress="return numero(event)" autocomplete="off" required maxlength="9" id="txtcel" name="txtcel">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Correo</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" placeholder="centro@tecnologico.com" autocomplete="off" required id="txtema" name="txtema">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Dirección</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" rows="3" placeholder="Donde vive actualmente" autocomplete="off" style="text-transform:uppercase" required id="txtdirec" name="txtdirec"></textarea>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Tipo de Usuario</label>
                    <div class="col-sm-10">
                      <select class="form-control m-b" name="txttipo" id="txttipo" required>
                        <option value="" disabled selected>--Seleccione--</option>

                        <?php if (isset($_COOKIE['tuser'])) {
                          if ($_COOKIE['tuser'] == '7' || $_COOKIE['tuser'] == '1') { ?><option value="1">Gerente</option><?php }
                                                                                                                      }   ?>
                        <option value="2">Administrador</option>
                        <option value="3">Operador</option>
                        <option value="4">Asesor</option>
                        <?php if (isset($_COOKIE['tuser'])) {
                          if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '7') { ?>
                            <option value="5">Jefe de Operaciones</option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Oficina</label>
                    <div class="col-sm-10">
                      <select class="form-control m-b" name="txtofi1" id="txtofi1">
                        <option value="" disabled selected>--Seleccione--</option>
                        <?php
                        //mostramos las oficinas
                        include('../conection/bdcredito.php');
                        $uofi = extraer("select idO,distri, direccion FROM toficina tof inner join ta_dis td on tof.distrito =td.iddis order by distri asc");
                        while ($row1 = mysqli_fetch_array($uofi)) {
                        ?>
                          <option value="<?php echo $row1['idO']; ?>"> <?php echo $row1['distri'] . "     " . $row1['direccion'] ?></option>
                        <?php

                        }

                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-10">
                      <select class="form-control" name="txtestado" id="txtestado">
                        <option value="1">Habilitado</option>
                        <option value="2">Inabilitado</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">

                    <label class="col-sm-2 control-label">Fotografia del Usuario:</label>
                    <div class="col-sm-10 size-detail">
                      <div class="row">
                        <div class="col-sm-12">
                          <div Id="preview">
                          </div>
                          <img Id="img1" src="img/1.jpg" CssClass="imageslide" width="300px" height="300px" />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12 contentupload">
                          <input type="file" name="img" ID="img" style="color:blue">
                          <div class="js">
                            <span>Elegir una fotografía en formato JPG&hellip;</span></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-3 ">
                      <button data-dismiss="modal" aria-hidden="true" class="btn btn-white" type="reset">Cancel</button>
                    </div>
                    <div class="col-sm-4 ">
                      <input type="submit" id="agreU" onclick="setTimeout('load(1)',1000);" name="" value="Guardar y Limpiar" class="btn btn-primary">
                    </div>
                    <div class="col-sm-4">
                      <input type="submit" id="agreU2" onclick="setTimeout('guarsalir(1)',1000);" name="" value="Guardar y Salir" class="btn btn-info">

                    </div>
                  </div>
                </form>
                <script src="functiones/poder.js"></script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function tecleo(event) {
    var codigo = event.which || event.keyCode;
    //console.log("Presionada: " + codigo);
    if (codigo === 13) {
      verDatosDni();
    }
  }

  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#img1').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#img").change(function() {
    readURL(this);
  });
</script>