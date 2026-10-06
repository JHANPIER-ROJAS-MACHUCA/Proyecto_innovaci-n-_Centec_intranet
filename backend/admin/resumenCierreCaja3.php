
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
      <h5 style="color:white">DETALLE DE CIERRE DE CAJA <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
      </div>
      <div class="panel-body">
        <?php //monto designado por la bobeda ?>
              <div class="form-group row">
                <div class="col-md-4">
                  <label>USUARIO</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)"><i class="fa fa-user-circle-o"></i> </a>
                    </span>
                    <select class="form-control" id="lstusuarios" name="">
                      <option value="-1" selected disabled>- - Seleccionar - -</option>
                      <?php
                         $tipodeUsuario=$_COOKIE['tuser'];
                         $consulta="";
                         if($tipodeUsuario==1 || $tipodeUsuario==7)
                         {
                           $consulta=extraer("SELECT idU as id,concat(apU,' ',amU,' ',nomU) as dato,tipoU FROM tusuario order by apU desc");
                         }
                         else
                         {
                           $idO=$_COOKIE['tofi'];
                           $consulta=extraer("SELECT idU as id,concat(apU,' ',amU,' ',nomU) as dato,tipoU FROM tusuario where idO='$idO' order by apU desc");
                         }
                         while ($r=mysqli_fetch_array($consulta))
                         {
                           ?>
                            <option value="<?php echo $r['id'] ?>"><?php echo $r['dato'] ?></option>
                           <?php
                         }
                       ?>
                    </select>
                  </div>
                </div>
                <?php //fecha ?>
                <div class="col-md-3">
                  <label class="control-label">FECHA :</label>
                  <div class="input-group margin">
                    <input type="text" class="form-control datepicker" placeholder="dd/mm/aaaa" name="fecha" id="fecha" style="text-align:center" value="<?php echo $f; ?>">
                    <span class="input-group-btn">
                      <a class="btn btn-info btn-flat" onclick="BuscarUsuario()" style="height:34px;font-weight:bold"><i class="glyphicon glyphicon-search"></i></a>
                    </span>
                  </div>
                </div>
              </div>

              <div class="tabs-container">
                <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">DETALLE</a></li>

                </ul>
                <div class="tab-content">
                  <div id="tab-1" class="tab-pane active">
                      <div class="panel-body">
                        <div id="detalleCaja">

                        </div>
                      </div>
                  </div>

                </div>
             </div>
        </div>

<script src="modulo1/codigo.js"></script>
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
