<form id="agrePresta" class="form-horizontal" enctype="multipart/form-data">
  <div class="tab-content">
    <div id="tab-a1" class="tab-pane active">
      <div class="panel-body">
        <fieldset class="form-horizontal">
          <div class="form-group">
            <div class="col-md-2">
              <?php
              include("../conection/bdcredito.php");
              extract($_POST);
              if (isset($id)) {

                $ex = extraer("SELECT (count(*)+1) as dato FROM tprestamo where idCG='$id' and (estado='4' or estado='5')");
                $re = mysqli_fetch_array($ex);

              ?>
                <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a3" onclick="ver()"><i class="fa fa-plus-circle"> </i> Nuevo Prestamo</a>
              <?php
                /*}*/
              }
              ?>
            </div>
          </div>
          <div class="form-group">
            <div id="HistoPrestamo">

            </div>
          </div>
        </fieldset>
      </div>
    </div>
    <!--<div id="tab-a2" class="tab-pane">
          <div class="panel-body">
            detalle de prestamo
          </div>
      </div>-->

    <?php //info de prestamo monto 
    ?>
    <div id="tab-a3" class="tab-pane">
      <div class="panel-body">
        <fieldset class="form-horizontal">
          <div class="form-group">
            <div class="col-sm-6" style="text-align:left">
              <?php
              //$dato=extraer("SELECT if(count(*) >0,(if(estado='5','APRO','DESA')),'APRO') as resul FROM tprestamo where idCG='$id' order by idP desc limit 1");
              //$re=mysqli_fetch_array($dato);
              ?>
              <label>Credito N°: <strong id="contarPrestamo"><?php echo $re['dato']; ?></strong></label>
            </div>
            <div class="col-sm-6" style="text-align:right">
              <a class="btn btn-sm btn-info" id="btn1" data-toggle="tab" href="#tab-a4" onclick="avanze()">Siguiente Vinculación >></a>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-sm-2 ">
              <label>Tipo de Prestamo</label>
              <select class="form-control" onchange="detal()" name="txttipoPresta" id="txttipoPresta">
                <option value="1">Transporte</option>
                <option value="2">Comercio</option>
                <option value="3">Prendatario</option>
                <option value="4">Servicio</option>
              </select>
            </div>
            <?php //monto 
            ?>
            <div class="col-sm-2 ">
              <label>Monto Propuesto</label>
              <div class="input-group margin">
                <span class="input-group-btn" disabled>
                  <a class="btn btn-info" style="font-weight:bold">S/. </a>
                </span>
                <input autocomplete="off" onkeypress="return numi(event)" onkeyup="monti()" title="Monto a Designar" maxlength="9" class="form-control" style="text-align:right" placeholder="0.00" type="text" name="txtmonto" id="txtmonto" value="">
              </div>
            </div>
            <?php //tasa 
            ?>
            <div class="col-sm-2 ">
              <label>Tasa de Interes %</label>
              <div class="input-group margin">
                <select class="form-control" name="txtinteres" onchange="monti()" id="txtinteres">
                  <?php
                  for ($i = 3; $i < 51; $i++) {
                  ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                  <?php
                  // if ($_COOKIE['tuser'] == "2" || $_COOKIE['tuser'] == "1" || $_COOKIE['tuser'] == "7") {
                  ?>
                  <!-- <option value="15">15</option> -->
                  <?php //} 
                  ?>
                </select>
                <!--<input autocomplete="off" onkeypress="return numero(event)" onkeyup="monti()"  title="Taza de interes"   maxlength="3" class="form-control" style="text-align:right" placeholder="0.00" type="text"  value="8">-->
                <span class="input-group-btn" disabled>
                  <a class="btn btn-info" style="font-weight:bold">%</a>
                </span>
              </div>
            </div>
            <?php //pago 
            ?>
            <div class="col-sm-2 ">
              <label>Pagos</label>
              <select class="form-control" id="txtpago" name="txtpago" onchange="monti()">
                <option value="" disabled selected>- -Seleccione- -</option>
                <option value="1">Diario</option>
                <option value="2">Semanal</option>
                <option value="3">Pago Unico</option>
                <option value="5">Quincenal</option>
                <option value="4">Mensual</option>
              </select>
            </div>
            <?php //plazo 
            ?>
            <div class="col-sm-2 ">
              <label>Plazo</label>
              <input type="text" class="form-control" onkeyup="monti()" autocomplete="off" onkeypress="return numero(event)" name="txtplazo" id="txtplazo" value="">
            </div>
            <?php //cuota 
            ?>
            <div class="col-sm-2 ">
              <label>Cuota</label>
              <div class="input-group margin">
                <span class="input-group-btn" disabled>
                  <a class="btn btn-info" style="font-weight:bold">S/. </a>
                </span>
                <input type="text" class="form-control" style="background:white" disabled id="txtcuota" name="txtcuota" value="">
                <input type="hidden" class="form-control" style="background:white" id="txtcuotaf1" name="txtcuotaf1" value="">
              </div>
            </div>
            <?php //utima cuota 
            ?>
            <div class="col-sm-2 ">
              <label>Ultima Cuota</label>
              <div class="input-group margin">
                <span class="input-group-btn" disabled>
                  <a class="btn btn-info" style="font-weight:bold">S/. </a>
                </span>
                <input type="text" class="form-control" style="background:white" disabled id="txtcuotaF" name="txtcuotaF" value="">
              </div>
            </div>
            <?php //mora x di 
            ?>
            <div class="col-sm-2 ">
              <label>Mora por día</label>
              <div class="input-group margin">
                <span class="input-group-btn" disabled>
                  <a class="btn btn-info" style="font-weight:bold">S/. </a>
                </span>
                <input type="text" onkeypress="return numi(event)" class="form-control" style="background:white" id="txtmora" name="txtmora" value="" maxlength="5" placeholder="0.00">
              </div>
            </div>
            <div class="col-sm-2 ">
              <label>Funcionario responsable</label>
              <select name="user_id" id="user_id" class="form-control">
                <option value="">Seleccione una opción</option>
                <?php
                $stmtUser = extraer("SELECT * FROM tusuario WHERE estadoU='1' AND (tipoU=3 || tipoU=4)");
                while ($row = mysqli_fetch_array($stmtUser)) {
                ?>
                  <option value="<?php echo $row['idU'] ?>"><?php echo $row['apU'] . ' ' . $row['amU'] . ' ' . $row['nomU'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-sm-2">
              <label id="labelFechaPago">Fecha de inicio de pago</label>
              <input type="date" class="form-control" name="started_at" id="started_at">
            </div>
            <?php //fecha de ultimo pago 
            ?>
            <div class="col-sm-2" id="fechiPa" style="display:none">
              <label>Fecha de Pago</label>
              <input type="text" value="" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="AfechaP" id="AfechaP">
            </div>
          </div>



        </fieldset>
      </div>
    </div>
    <?php //datos conyugue 
    ?>
    <div id="tab-a4" class="tab-pane">
      <div class="panel-body">
        <div class="form-group">
          <div class="col-sm-12" style="text-align:right">
            <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a3" onclick="avanze()">
              << Anterior Prestamo</a>
                <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a5" onclick="avanze()">Siguiente Aval >></a>
          </div>
        </div>
        <fieldset class="form-horizontal">
          <strong>DATOS CONYUGUE</strong>
          <div class="form-group">
            <div class="col-sm-3">
              <label>DNI(*)</label>
              <input type="text" class="form-control" accesskey="s" placeholder="Ingrese número de DNI" onkeypress="return numero(event)" onkeyup="consultaConyuge('txtdni','txtap','txtam','txtnom', 'sexo', 'Afecha')" autocomplete="off" id="txtdni" name="txtdni" maxlength="8">
            </div>
            <div class="col-sm-3">
              <label>Apellido Paterno</label>
              <input type="text" class="form-control" placeholder="Apellido Paterno" autocomplete="off" id="txtap" name="txtap">
            </div>
            <div class="col-sm-3">
              <label>Apellido Materno</label>
              <input type="text" class="form-control" placeholder="Apellido Materno" autocomplete="off" id="txtam" name="txtam">
            </div>
            <div class="col-sm-3">
              <label>Nombres</label>
              <input type="text" class="form-control" placeholder="Nombre" id="txtnom" autocomplete="off" name="txtnom">
            </div>
            <?php //sexo 
            ?>
            <div class="col-sm-3">
              <label>Sexo</label>
              <select class="form-control" name="sexo" id="sexo">
                <option value="" disabled selected>--Seleccione--</option>
                <option value="F">FEMENINO</option>
                <option value="M">MASCULINO</option>
              </select>
            </div>
            <?php //fecha nac 
            ?>
            <div class="col-sm-3">
              <label>Fecha de Nac.</label>
              <input type="text" value="" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="Afecha" id="Afecha">
            </div>
            <?php //imagen 
            ?>

          </div>
          <!-- <div class="form-group">
            <div class="col-sm-6">
              <strong>Croquis del Negocio</strong>
              <div class="col-sm-10 size-detail">
                <div class="row">
                  <div class="col-sm-12">
                    <div Id="preview">
                    </div>
                    <img Id="img1" src="img/1.jpg" style="color:" CssClass="imageslide" width="300px" height="300px" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 contentupload">
                    <input type="file" accept="image/*" onBlur='LimitAttach(this,1);' name="img" ID="img" style="color:blue">
                    <div class="js">
                      <span>Elegir una fotografía en formato JPG&hellip;</span></label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <strong>Croquis del Domicilio</strong>
              <div class="col-sm-10 size-detail">
                <div class="row">
                  <div class="col-sm-12">
                    <div Id="preview">
                    </div>
                    <img Id="img3" src="img/1.jpg"  CssClass="imageslide" width="300px" height="300px" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 contentupload">
                    <input type="file" accept="image/*" onBlur='LimitAttach(this,1);' name="img2" ID="img2" style="color:blue">
                    <div class="js">
                      <span>Elegir una fotografía en formato JPG&hellip;</span></label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
        </fieldset>


      </div>
    </div>
    <?php //datos aval 
    ?>
    <div id="tab-a5" class="tab-pane">
      <div class="panel-body">
        <div class="form-group">
          <div class="col-sm-12" style="text-align:right">
            <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a4" onclick="avanze()">
              << Anterior Conyugue</a>
                <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a7" onclick="avanze()">Siguiente Documentos >></a>
          </div>
        </div>
        <fieldset class="form-horizontal">
          <?php //datos del aval 
          ?>
          <strong>DATOS AVAL</strong>
          <div class="form-group">
            <div class="col-sm-3">
              <label>DNI(*)</label>
              <input type="text" class="form-control" placeholder="Ingrese número de DNI" onkeypress="return numero(event)" autocomplete="off" onkeyup="consultaAval('txtdni1','txtap1','txtam1','txtnom1', 'txtdirec', 'txtcel', 'txtocu')" id="txtdni1" name="txtdni1" maxlength="8">
            </div>
            <div class="col-sm-3">
              <label>Apellido Paterno</label>
              <input type="text" class="form-control" placeholder="Apellido Paterno" autocomplete="off" id="txtap1" name="txtap1">
            </div>
            <div class="col-sm-3">
              <label>Apellido Materno</label>
              <input type="text" class="form-control" placeholder="Apellido Materno" autocomplete="off" id="txtam1" name="txtam1">
            </div>
            <div class="col-sm-3">
              <label>Nombres</label>
              <input type="text" class="form-control" placeholder="Nombre" id="txtnom1" autocomplete="off" name="txtnom1">
            </div>
            <div class="col-sm-3">
              <label>Dirección</label>
              <input type="text" class="form-control" name="txtdirec" id="txtdirec" placeholder="jr. desconocido n° 00" value="">
            </div>
            <div class="col-sm-3">
              <label>Celular</label>
              <input type="text" class="form-control" name="txtcel" id="txtcel" onkeypress="return numero(event)" maxlength="11" value="">
            </div>
            <div class="col-sm-3">
              <label>Ocupación</label>
              <input type="text" class="form-control" maxlength="45" name="txtocu" id="txtocu" placeholder="A que se dedica" value="">
            </div>
            <div class="col-sm-3">
              <label>Dirección del Centro de Tabrabajo</label>
              <input type="text" class="form-control" name="txtdirecTra" id="txtdirecTra" maxlength="45" value="">
            </div>
          </div>
        </fieldset>
        <!-- <div class="form-group">
          <div class="col-sm-6">
            <strong>Croquis del Aval</strong>
            <div class="col-sm-10 size-detail">
              <div class="row">
                <div class="col-sm-12">
                  <div Id="preview">
                  </div>
                  <img Id="img5" src="img/1.jpg" CssClass="imageslide" width="300px" height="300px" />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 contentupload">
                  <input type="file" name="img4" ID="img4" style="color:blue">
                  <div class="js">
                    <span>Elegir una fotografía en formato JPG&hellip;</span></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> -->
        <div class="form-group">
          <div class="col-sm-12" style="text-align:right">

          </div>
        </div>
      </div>
    </div>
    <?php //descripcion de prend 
    ?>
    <div id="tab-a6" class="tab-pane">
      <div class="panel-body">
        <fieldset class="form-horizontal">
          <div class="form-group">
            <div class="col-sm-6">
              <strong>DESCRIPCION DE GARANTIAS</strong>
              <textarea name="txtdescrip" id="txtdescrip" class="form-control"></textarea>
            </div>
            <div class="col-sm-6">
              <strong>OBSERVACIONES</strong>
              <textarea name="txtobser" id="txtobser" class="form-control"></textarea>
            </div>
          </div>
        </fieldset>
        <div class="form-group">
          <div class="col-sm-12" style="text-align:right">
            <a class="btn btn-sm btn-info" id="btn4" data-toggle="tab" href="#tab-a5" onclick="avanze()">
              << Anterior</a>
                <a class="btn btn-sm btn-info" data-toggle="tab" href="#tab-a7" onclick="avanze()">Siguiente Aval >></a>
          </div>
        </div>
      </div>
    </div>
    <?php //documentos  
    ?>
    <div id="tab-a7" class="tab-pane">
      <div class="panel-body">
        <fieldset class="form-horizontal">
          <div class="form-group">
            <div class="col-sm-3" title="Adjuntar el recibo de luz, el contrato de alquiler y las fotografias correspondientes e convertirlas en pdf">
              <label for="txtdocus">Documentos</label>
              <div class="fallback">
                <input type="file" id="txtdocus" name="txtdocus" value="">
              </div>
            </div>

            <div class="col-sm-9">

              <div class="col-sm-4">
                <label for="">Recibo de Luz</label>
                <div class="ibox-content">
                  <input type="checkbox" name="txtluz" id="txtluz" class="js-switch" />
                </div>
              </div>
              <div class="col-sm-4">
                <label for="">Contrato de alquiler</label>
                <div class="ibox-content">
                  <input type="checkbox" name="txtalqui" id="txtalqui" class="js-switch_2" />
                </div>
              </div>
              <div class="col-sm-4">
                <label for="">Fotografias</label>
                <div class="ibox-content">
                  <input type="checkbox" name="txtfoto" id="txtfoto" class="js-switch_3" />
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <label>Descripción</label>
              <textarea name="txtdecrip1" id="txtdecrip1" class="form-control" plaholder="Este campo es obligatorio en el caso de ser prensatario."></textarea>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="form-group">
        <div class="col-sm-12" style="text-align:right">
          <a class="btn btn-sm btn-info" id="btn5" data-toggle="tab" href="#tab-a5" onclick="avanze()">
            << Anterior</a>
              <input type="submit" id="btnInput" name="btnInput" value="Guardar" class="btn btn-sm btn-success">
              <!--<a class="btn btn-sm btn-info"  data-toggle="tab" href="#tab-a7" onclick="avanze()">Siguiente Aval</a>-->
              <a class="btn btn-sm btn-info " onclick="inicq1()" id="btnFine" name="btnfine" data-toggle="tab" href="#tab-a1">Volver a Historial</a>
        </div>
      </div>
    </div>

    <script>
    document.getElementById('txtpago').addEventListener('change', (e) => {
        if (e.target.value == '3') {
            document.getElementById('labelFechaPago').innerHTML = "Fecha de pago";
        }else{
            document.getElementById('labelFechaPago').innerHTML = "Fecha de inicio de pago";
        }
    })
</script>
    <script src="functiones/direcc1.js"></script>
</form>