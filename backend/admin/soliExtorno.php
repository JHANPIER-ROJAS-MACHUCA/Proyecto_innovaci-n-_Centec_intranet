<?php include('head.php');?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
      <div class="btn-group pull-right">
        <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>
  <h5 style="color:white">Extornaciones<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div class="tabs-container">
      <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#tab-1"><strong>INFORMACIÓN DE OPERACIONES</strong></a></li>
          <!--<li class=""><a data-toggle="tab" href="#tab-2">Direcciones</a></li>
          <li class=""><a data-toggle="tab" href="#tab-3">Negocios</a></li>
          <li class=""><a data-toggle="tab" href="#tab-4">Prestamos</a></li>-->
      </ul>
      <div class="tab-content">
          <div id="tab-1" class="tab-pane active">
              <div class="panel-body">
                <fieldset class="form-horizontal">
                  <div class="form-group">
                    <div class="col-md-4 col-sm-5">
                      <div class="input-group margin" style="padding-top:10px">
                        <span class="input-group-btn" disabled>
                          <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">COD. (*) </a>
                        </span>
                        <input autocomplete="off" class="form-control" style="text-align:center" placeholder="codigo" type="text" name="txtcodigo" id="txtcodigo" value="">
                      </div>
                      <div class="" style="padding-top:10px">
                        <span class="input-group-btn" disabled>
                          <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">MOTIVO (*) </a>
                        </span>
                        <textarea name="txtmotivo" placeholder="Ingrese el motivo por el cual quiere extornar el codigo solicitado" id="txtmotivo" class="form-control" rows="3" cols="40"></textarea>
                      </div>
                      <div class="" style="padding-top:10px;text-align:right">
                        <?php
                        if($limitadorfinal==0)
                        {
                        ?>
                        <button type="button" class="btn btn-info" name="btnsolicitar" onclick="solicitar()">Solicitar Extorno</button>
                        <?php
                          }
                         ?>
                      </div>
                    </div>
                    <div class="col-md-8 col-sm-7">
                      <h3 style="font-weight:bold">SOLICITUDES DE EXTORNO</h3>
                      <div id="solici" >

                      </div>
                    </div>
                  </div>

          </div>
    </div>
 </div>
</div>
<?php include('footer.php');?>
<script src="functionesBasic/soli.js">

</script>
