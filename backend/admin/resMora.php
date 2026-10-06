<?php

use CrediSoporte\Domain\Models\Customer;

include('head.php');
?>

<link href="../css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>
    <h5 style="color:white">CONDONACIÓN DE MORA<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <?php //monto designado por la bobeda 
    ?>


    <div class="tabs-container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">DETALLE</a></li>

      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
          <div class="panel-body">
            <div class="form-group row">
              <div class="col-sm-6">
                <div class="col-md-12">
                  <label>CLIENTE</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info " style="font-weight:bold" onclick="return(false)"><i class="fa fa-user-circle-o"></i> </a>
                    </span>
                    <select class="chosen-select" tabindex="2" id="lstclientes" name="">
                      <option value="-1" selected disabled>- - Seleccionar - -</option>
                      <?php
                      $clients = Customer::join('tprestamo', 'tprestamo.idCG', 'tclie_general.idCG')
                        ->where('tprestamo.estado', 4)
                        ->groupBy('tclie_general.idCG')
                        ->get();
                      ?>

                      <?php foreach ($clients as $client) { ?>
                        <option value="<?php echo $client->idCG ?>"><?php echo "$client->ap $client->am $client->nom" ?></option>
                      <?php } ?>
                    </select>
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="cliente()"><i class="fa fa-search"></i> </a>
                    </span>
                  </div>
                </div>
                <?php //fecha 
                ?>
                <div class="col-md-12">
                  <label class="control-label">PRESTAMOS :</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)"><i class="fa fa-calendar"></i> </a>
                    </span>
                    <select class="form-control" id="lstprestamos" name="">
                      <option value="-1" selected disabled>- - Seleccione - -</option>
                    </select>
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="fechas()"><i class="fa fa-search"></i> </a>
                    </span>
                  </div>
                </div>
                <!-- <div class="col-sm-12">
                  <label class="control-label">MORA PENDIENTE :</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)">S/. </a>
                    </span>
                    <input type="text" class="form-control" style="text-align:right;color:blue;font-weight:bold" disabled name="" id="moraPendi" value="">
                    <input type="hidden" class="form-control" style="text-align:right" name="" id="txtmore" value="">
                  </div>

                </div> -->
                <!-- <div class="col-sm-12" >
                                <label class="control-label">CONDONAR MORA :</label>
                                <div class="input-group margin">
                                  <span class="input-group-btn" disabled>
                                    <a class="btn btn-info" style="font-weight:bold" onclick="return(false)">S/. </a>
                                  </span>
                                  <input type="text" class="form-control" style="text-align:right" onkeypress="return numi(event)" onkeyup="verificaR()" name="" id="condonar" value="">
                                  <span class="input-group-btn" disabled>
                                    <a class="btn btn-info" style="font-weight:bold" onclick="condonarMora()" title="Condonar Mora"><i class="fa fa-save"></i> Guardar</a>
                                  </span>
                                </div>
                            </div> -->
                <!-- <div class="col-sm-12">
                  <label class="control-label">CONDONAR MORA :</label>
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="return(false)">Cantidad</a>
                    </span>
                    <input type="text" class="form-control" style="text-align:right" name="" id="condonar" value="">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" style="font-weight:bold" onclick="condonarMora()" title="Condonar Mora"><i class="fa fa-save"></i> Guardar</a>
                    </span>
                  </div>
                </div> -->
              </div>
              <div class="col-sm-6">
                <div class="table-responsive" id="fechaMor">
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


  <!--<script src="extra/min.js"></script>-->
  <script src="more/codigo.js"></script>
  <?php include('footer.php'); ?>
  <script src="../js/plugins/chosen/chosen.jquery.js"></script>
  <script>
    $('.chosen-select').chosen({
      width: "100%"
    });
  </script>