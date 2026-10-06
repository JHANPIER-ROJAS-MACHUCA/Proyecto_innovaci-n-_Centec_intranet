<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  $fechita=extraer("select fecha as fec,dias as dia,motivo as moti from tahorro_motivo where idam='$id'");
  $row=mysqli_fetch_array($fechita);
  ?>
  <div class="row form-group">
    <div class="col-sm-12">
      <div class="col-sm-6">
        <label for="">Motivo</label>
        <div class="ibox-content">
              <input type="text" disabled style="width:100%" class="form-control juve12" id="txt" name="txt" value="<?php echo $row["moti"]; ?>" />
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <div class="col-sm-5">
          <label for="">Fecha Limite</label>
          <div class="ibox-content">
            <input type="text" value="<?php if($row['fec']!=""){echo $row['fec'];} ?>" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="txtfechaFinalizar" id="txtfechaFinalizar">
            <input type="hidden" class="form-control" name="txtidentificador" id="txtidentificador" value="<?php echo $id; ?>">
          </div>
      </div>
      <div class="col-sm-5">
        <label for="">Dias</label>
        <div class="ibox-content">
              <input type="text" maxlength="3" value="<?php if($row['dia']!=""){echo $row['dia'];} ?>" class="form-control juve12" onkeypress="return numero(event)" onkeyup="anioLimit()" id="txtcantidadDias" name="txtcantidadDias" />
              <select style="display:none" class="form-control" name="lstclientes" id="lstclientes">
                <option value="" disabled selected>-- Seleccione--</option>
                <?php
                  $ususa=extraer("SELECT dni,concat(ap,' ',am,' ',nom) as dato,idCG FROM tclie_general order by ap asc");
               while($row =mysqli_fetch_array($ususa))
               {
                 ?>
                   <option value="<?php echo $row['idCG'] ?>"> <?php echo $row['dni']." | ". $row['dato']?></option>
                 <?php
               }
                 ?>
              </select>
        </div>
      </div>
      <div class="col-sm-2  " >
        <label for="">&nbsp;</label>
        <div class="ibox-content">
            <a class="btn btn-info" title="Generar Fechas" onclick="inicioMotivo()"><i class="fa fa-save"></i></a>
              <a href="#" style="display:none" class="btn btn-default" title="Descargar Prestamo" onclick="imprimir_prestamo();"><i class="glyphicon glyphicon-download"></i></a>
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="col-sm-5">
          <label for="">Fecha Inicio</label>
          <div class="ibox-content">
            <input type="text" value="" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="txtfechaIniciar" id="txtfechaIniciar">
          </div>
      </div>
      <div class="col-sm-5">
          <label for="">Meses</label>
          <div class="ibox-content">
            <select class="form-control" id="lstmeses" name="lstmeses" onselect=""="">

            </select>
          </div>
      </div>
      <div class="col-sm-2">
        <label for="">&nbsp;</label>
        <div class="ibox-content">
            <a class="btn btn-info" title="Ver Dias del Mes" onclick="cargarDias()"><i class="fa fa-eye"></i></a>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="col-sm-12">
        <label for="">Dias de la Semana</label>
        <div class="col-sm-12" id="diasP">

        </div>
      </div>
    </div>
  </div>
  <script>
         $('.datepicker').bootstrapMaterialDatePicker({
             weekStart: 0,
             time: false,
             format: 'DD/MM/YYYY'
             //format: 'dddd DD MMMM YYYY - HH:mm'
         });
         function anioLimit()
         {
           var dia=$('#txtcantidadDias').val();
          if(dia>101)
           {
             swal("El limite maximo es de 100","","info");
             $('#txtcantidadDias').val("100");
           }
         }
         $('.i-checks').iCheck({
             checkboxClass: 'icheckbox_square-green',
             radioClass: 'iradio_square-green'
         });

     </script>
