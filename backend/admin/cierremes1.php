<?php
  include('head.php');
  date_default_timezone_set('america/lima');
  $f=date("d/m/Y");
  $dat=preg_split("~/~", $f);
 ?>
<?php //el primero es para la feha el seunda para los iconos ?>
 <link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
 <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
   <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
          <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
        </div>
      <h5 style="color:white">DETALLE DE CIERRE DE MES <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
      </div>
      <div class="panel-body">
        <?php //monto designado por la bobeda ?>
              <div class="form-group row">
                <div class="col-md-4">
                  <label>OFICINA</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)"><i class="fa fa-user-circle-o"></i> </a>
                    </span>
                    <select class="form-control" id="lstofi" name="">
                      <?php
                      $re=extraer("SELECT idO as id, direccion as dato FROM toficina");
                      while ($r=mysqli_fetch_array($re))
                      {
                        ?>
                        <option value="<?php echo $r['id'] ?>"><?php echo $r['dato'] ?></option>
                        <?php
                      }
                       ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <label>AÑO</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)"><i class="fa fa-user-circle-o"></i> </a>
                    </span>
                    <select class="form-control" id="lstanio" name="">
                      <option value="2022" selected >2022</option>
                      <option value="2021">2021</option>
                      <option value="2020">2020</option>
                      <option value="2019">2019</option>
                    </select>
                  </div>
                </div>
                <?php //fecha ?>
                <div class="col-md-2">
                  <label class="control-label">MES :</label>
                  <div class="input-group margin">
                    <span class="input-group-btn">
                      <a class="btn btn-info btn-flat" onclick="BuscarUsuario()" style="height:34px;font-weight:bold"><i class="fa fa-calendar"></i></a>
                    </span>
                    <select class="form-control" id="lstmes" name="">
                      <option value="1" selected >ENERO</option>
                      <option value="2">FEBRERO</option>
                      <option value="3">MARZO</option>
                      <option value="4">ABRIL</option>
                      <option value="5">MAYO</option>
                      <option value="6">JUNIO</option>
                      <option value="7">JULIO</option>
                      <option value="8">AGOSTO</option>
                      <option value="9">SEPTIEMBRE</option>
                      <option value="10">OCTUBRE</option>
                      <option value="11">NOVIEMBRE</option>
                      <option value="12">DICIEMBRE</option>
                    </select>

                  </div>
                </div>
                <?php ////usuarios ?>
                <div class="col-md-4">
                  <label class="control-label">USUARIOS :</label>
                  <div class="input-group margin">
                    <select class="form-control" id="lstusuarios" name="">

                    </select>
                    <span class="input-group-btn">
                      <a class="btn btn-info btn-flat" onclick="BuscarUsuario1()" style="height:34px;font-weight:bold"><i class="fa fa-search"></i></a>
                    </span>
                  </div>
                </div>
              </div>

              <div class="tabs-container" id="detalleCaja">

             </div>
        </div>

<script src="cone/cierremes1/codigo.js"></script>
<script src="extra/min.js"></script>

<?php include('footer.php'); ?>
<?php //para la fecha  ?>
<script src="fecha/moment.js"></script>
<script src="fecha/bootstrap-material-datetimepicker.js"></script>
<script>
       $('.datepicker').bootstrapMaterialDatePicker({
           weekStart: 0,
           time: false,
           format: 'DD/MM/YYYY'
       });
</script>
 <script type="text/javascript" src="js/VentanaCentrada.js"></script>
