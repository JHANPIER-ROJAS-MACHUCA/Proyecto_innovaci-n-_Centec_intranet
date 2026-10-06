<?php include('head.php'); ?>
<style>
.juve12{
  text-transform: uppercase;
}
</style>
<link href="../css/plugins/switchery/switchery.css" rel="stylesheet">
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<link href="../css/plugins/switchery/switchery.css" rel="stylesheet">

<link href="../css/plugins/iCheck/custom.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
          <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
         </div>

         <h5 style="color:white">Perfil del Cliente<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
      </div>
      <div class="panel-body">
        <div class="tabs-container">

          <?php //empezamos ?>

          <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#tab-1">Datos</a></li>
          </ul>
          <div class="tab-content">
              <div id="tab-1" class="tab-pane active">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <label for="">Motivo</label>
                      <div class="ibox-content">
                        <input type="text" class="form-control juve12" id="txtmotivo" name="txtmotivo" />
                        <input type="hidden" class="form-control juve12" id="txtidmotivo" name="txtidmotivo" />
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <label for="">Cuota</label>
                      <div class="ibox-content">
                            <input type="text" class="form-control" id="txtmontoMotivo" onkeypress="return numi(event)" name="txtmontoMotivo" />
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <label for="">Tiene Fechas</label>
                      <div class="ibox-content">
                            <input type="checkbox" class="js-switch" id="chkfechas" name="chkfechas" />
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <label for="">&nbsp;</label>
                      <div class="ibox-content">
                        <a type="button" name="button" class="btn btn-info" onclick="guardarMotivo()"><i class="fa fa-save"></i> Agregar Motivo</a>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <fieldset class="form-horizontal">
                    <div class="col-sm-6">
                      <div class="form-group" style="overflow-y:scroll;height:350px">
                        <div id="listarMotivos">

                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group" style="">
                        <div id="configurarMotivo">

                        </div>
                      </div>
                    </div>
                    </fieldset>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
</div>
<?php include("footer.php"); ?>
   <script src="../js/plugins/switchery/switchery.js"></script>
   <script type="text/javascript" src="motivos/motivos.js"></script>

   <script src="fecha/moment.js"></script>
   <script src="fecha/bootstrap-material-datetimepicker.js"></script>

   <!--che-->
     <script src="../js/plugins/iCheck/icheck.min.js"></script>

     <!--<script src="../js/jquery-3.1.1.min.js"></script>-->
   <!--imprimir-->
   <script src="js/VentanaCentrada.js">

   </script>
<script>
   $(document).ready(function(){

        var elem = document.querySelector('.js-switch');
           var switchery = new Switchery(elem, { color: '#1AB394' });
        });

   </script>
