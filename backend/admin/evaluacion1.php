<?php
  include('head.php');
 ?>
 <style media="screen">
    .drag0106{
     text-align: right;
   }
   .dr{
     text-align:center;
     background-color: #23c6c8;
     color:white;
     font-size: 13px;
     border:5px red;
    /* border-color: red;*/

   }
 </style>
 <?php //el primero es para la feha el seunda para los iconos ?>
  <link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
       <div class="btn-group pull-right">
       </div>

     <h5 style="color:white">Evaluación  de Credidiario<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
   </div>
   <div class="panel-body">
     <div class="tabs-container">
       <ul class="nav nav-tabs">
           <li class="active"><a data-toggle="tab" href="#tab-1"><strong>HOJA DE EVALUACION DE CREDIDIARIO</strong></a></li>
       </ul>
       <div class="tab-content">
           <div id="tab-1" class="tab-pane active">
               <div class="panel-body">
                 <fieldset class="form-horizontal">
                   <div class="form-group">
                         <div class="row">
                           <div class="col-lg-12">
                             <div class="ibox float-e-margins">
                                <div class="ibox-title" style="background:<?php echo $jua1['color']?>">
                                    <h5 style="color:white;font-weight:bold">VIVIENDA?? <small> </small></h5>

                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                      <?php //1 ?>
                                      <div class="col-md-6">

                                            <label><strong>TIPO:</strong></label>
                                            <select class="form-control" onchange="eval1()" name="tipoVivienda" id="tipoVivienda" style="text-align:center">
                                              <option value="0.3">Vivenda Propia</option>
                                              <option value="0.3">Vivenda Familiar</option>
                                              <option value="0.4">Vivenda Alquilada</option>
                                            </select>

                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                           <?php //PRIMERA EVALUACION ?>
                           <div class="col-lg-12">
                             <div class="ibox float-e-margins">
                                <div class="ibox-title" style="background:<?php echo $jua1['color']?> ">
                                    <h5 style="color:white;font-weight:bold">PRIMERA EVALUACIÓN <small> </small></h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link" style="color:white">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                      <?php //1 ?>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <?php //caso 1 ?>
                                          <div class="col-md-12">
                                            <label><strong>I. ACTIVO A CORTO PLAZO</strong></label>
                                            <br>
                                            <label class="control-label">FECHA :</label>
                                            <div class="input-group margin">
                                              <input type="text" class="form-control datepicker" onclick="fechaEva1()" placeholder="dd/mm/aaaa" name="fecha1" id="fecha1" style="text-align:center" value="<?php echo $f; ?>">
                                              <span class="input-group-btn">
                                                <a class="btn btn-info btn-flat" disabled style="height:34px;font-weight:bold"><i class="fa fa-calendar"></i></a>
                                              </span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table width="100%">
                                              <tr>
                                                <td>
                                                  <label>DISPONIBLE:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" name="txtdiponible1" id="txtdiponible1" type="text" title="Disponible en el momento" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Disponible en el momento">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>CUENTAS POR COBRAR:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" name="txtporcobrar1" id="txtporcobrar1" type="text" title="Cuentas por Cobrar." class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Pendiente">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>INVENTARIO:</label>
                                                </td>
                                                <td>
                                                  <input name="txttotinven1" id="txttotinven1" title="Total del Inventario" type="text" class="form-control drag0106"  value="" placeholder="Monto del inventario" disabled>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>TOTAL ACT. CORTO PLAZO:</label>
                                                </td>
                                                <td>
                                                  <input name="txttotalCorto1" id="txttotalCorto1" title="Total Activo a Corto Plazo" type="text" class="form-control drag0106"  value="" placeholder="0.00" disabled>
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                      <?php //1.2 ?>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label>INVENTARIO</label>
                                            <table width="100%">
                                              <tr>
                                                <td align="center">
                                                  <label>DESCRIPCION:</label>
                                                </td>
                                                <td align="center">
                                                  <label>MONTO s/.</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve1a" id="txtinve1a" type="text" class="form-control"  value="" placeholder="INV. N°1">
                                                </td>
                                                <td>
                                                  <input name="txtinve1a1" onkeyup="eval1()" id="txtinve1a1" type="text" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="INV. N°1 MONTO">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve2a" id="txtinve2a" type="text" class="form-control"  value="" placeholder="INV. N°2">
                                                </td>
                                                <td>
                                                  <input name="txtinve2a2" onkeyup="eval1()" id="txtinve2a2" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°2 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve3a" id="txtinve3a" type="text" class="form-control"  value="" placeholder="INV. N°3">
                                                </td>
                                                <td>
                                                  <input name="txtinve3a3" onkeyup="eval1()" id="txtinve3a3" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°3 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve4a" id="txtinve4a" type="text" class="form-control"  value="" placeholder="INV. N°4">
                                                </td>
                                                <td>
                                                  <input name="txtinve4a4" onkeyup="eval1()" id="txtinve4a4" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°4 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                <label>Total Inventario</label>
                                                </td>
                                                <td>
                                                  <input name="txttotalInventarioa1" id="txttotalInventarioa1" title="Total del Inventario" type="text" class="form-control drag0106" disabled  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                      <?php //2 ?>
                                      <hr>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>II. VENTAS</strong></label>
                                            <br>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label>VENTAS DIARIAS:</label>
                                            <input name="txtventaDiaria1" id="txtventaDiaria1" type="text" title="Ventas Diarias" disabled placeholder="Ventas diaria estimada" class="form-control drag0106"  value="">
                                            <br>
                                            <label>VENTAS DECLARADAS</label>
                                            <table>
                                              <tr>
                                                <td>
                                                  <label>BUENO:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" name="txtbueno1" id="txtbueno1" type="text" title="Dia Bueno" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="Pendiente">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>MALO:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" name="txtmalo1" id="txtmalo1" type="text" title="Dia Malo" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Monto del inventario">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>ESTIMACIÓN:</label>
                                                </td>
                                                <td>
                                                  <input  name="txtestimacion1" id="txtestimacion1" disabled title="Estimación" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                      <?php //3 ?>
                                    <div class="row">

                                      <div class="col-md-8">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>III. COSTO MERC/PROD</strong></label>
                                            <br>

                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table>
                                              <tr>
                                                <td>
                                                    <label>1ra Evaluación</label>
                                                </td>
                                                <td>
                                                    <label>Precio de Compra</label>
                                                </td>
                                                <td>
                                                    <label>Precio de Venta</label>
                                                </td>
                                                <td>
                                                    <label>C.R.</label>
                                                </td>
                                                <td>
                                                    <label>C.R. Promedio</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a1" id="txtcosto1a1" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio1" id="txtcosto1Precio1" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta1" id="txtcosto1Venta1" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr1" id="txtcosto1Cr1" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td rowspan="7">
                                                  <input name="txtcosto1Prom1" id="txtcosto1Prom1" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a2" id="txtcosto1a2" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio2" id="txtcosto1Precio2" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta2" id="txtcosto1Venta2" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr2" id="txtcosto1Cr2" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a3" id="txtcosto1a3" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio3" id="txtcosto1Precio3" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta3" id="txtcosto1Venta3" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr3" id="txtcosto1Cr3" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a4" id="txtcosto1a4" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio4" id="txtcosto1Precio4" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta4" id="txtcosto1Venta4" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr4" id="txtcosto1Cr4" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <?php //comienza en injerto ?>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a5" id="txtcosto1a5" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio5" id="txtcosto1Precio5" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta5" id="txtcosto1Venta5" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr5" id="txtcosto1Cr5" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a6" id="txtcosto1a6" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio6" id="txtcosto1Precio6" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta6" id="txtcosto1Venta6" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr6" id="txtcosto1Cr6" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a7" id="txtcosto1a7" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio7" id="txtcosto1Precio7" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval1()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta7" id="txtcosto1Venta7" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr7" id="txtcosto1Cr7" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>

                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                    <?php //4 otros INGRESOS ?>
                                    <div class="row">
                                      <div class="col-md-8">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>IV. OTROS INGRESOS</strong></label>
                                            <br>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table width="100%">
                                              <tr>
                                                <td align="center">
                                                    <label>MONTO</label>
                                                </td>
                                                <td align="center">
                                                    <label>DESCRIPCIÓN</label>
                                                </td>
                                                <td align="center">
                                                  <label>TOTAL</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td width="15%">
                                                    <input onkeyup="eval1()" maxlength="10" name="txtingreso1" id="txtingreso1" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td width="70%">
                                                  <input class="form-control" type="text" name="txtingresoD1" id="txtingresoD1" value="" placeholder="Describa el ingreso.">
                                                </td>
                                                <td rowspan="3" width="15%">
                                                  <input class="form-control" type="text" name="txttotalOtroIngreso" id="txttotalOtroIngreso" value="" disabled style="background:white;text-align:right" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td >
                                                    <input onkeyup="eval1()" maxlength="10" name="txtingreso2" id="txtingreso2" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td >
                                                  <input class="form-control" type="text" name="txtingresoD2" id="txtingresoD2" value="" placeholder="Describa el ingreso.">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td >
                                                    <input onkeyup="eval1()" maxlength="10" name="txtingreso3" id="txtingreso3" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td >
                                                  <input class="form-control" type="text" name="txtingresoD3" id="txtingresoD3" value="" placeholder="Describa el ingreso.">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                           </div>
                           <hr>
                           <?php //SEGUNDA EVALUACION ?>
                           <div class="col-lg-12">
                             <div class="ibox float-e-margins">
                                <div class="ibox-title" style="background:<?php echo $jua1['color']?> ">
                                    <h5 style="color:white;font-weight:bold">SEGUNDA EVALUACIÓN <small> </small></h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link" style="color:white">
                                            <i class="fa fa-chevron-down"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none">
                                    <div class="row">
                                      <?php //1 ?>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <?php //caso 1 ?>
                                          <div class="col-md-12">
                                            <label><strong>I. ACTIVO A CORTO PLAZO</strong></label>
                                            <br>
                                            <label class="control-label">FECHA :</label>
                                            <div class="input-group margin">
                                              <input type="text" class="form-control datepicker" onclick="fechaEva1()" placeholder="dd/mm/aaaa" name="fecha2" id="fecha2" style="text-align:center" value="<?php echo $f; ?>">
                                              <span class="input-group-btn">
                                                <a class="btn btn-info btn-flat" disabled style="height:34px;font-weight:bold"><i class="fa fa-calendar"></i></a>
                                              </span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table width="100%">
                                              <tr>
                                                <td>
                                                  <label>DISPONIBLE:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" name="txtdiponible2" id="txtdiponible2" type="text" title="Disponible en el momento" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Disponible en el momento">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>CUENTAS POR COBRAR:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" name="txtporcobrar2" id="txtporcobrar2" type="text" title="Cuentas por Cobrar." class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Pendiente">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>INVENTARIO:</label>
                                                </td>
                                                <td>
                                                  <input name="txttotinven2" id="txttotinven2" title="Total del Inventario" type="text" class="form-control drag0106"  value="" placeholder="Monto del inventario" disabled>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>TOTAL ACT. CORTO PLAZO:</label>
                                                </td>
                                                <td>
                                                  <input name="txttotalCorto2" id="txttotalCorto2" title="Total Activo a Corto Plazo" type="text" class="form-control drag0106"  value="" placeholder="0.00" disabled>
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                      <?php //1.2 ?>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label>INVENTARIO</label>
                                            <table width="100%">
                                              <tr>
                                                <td align="center">
                                                  <label>DESCRIPCION:</label>
                                                </td>
                                                <td align="center">
                                                  <label>MONTO s/.</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve1a2" id="txtinve1a2" type="text" class="form-control"  value="" placeholder="INV. N°1">
                                                </td>
                                                <td>
                                                  <input name="txtinve1a12" onkeyup="eval2()" id="txtinve1a12" type="text" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="INV. N°1 MONTO">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve2a21" id="txtinve2a21" type="text" class="form-control"  value="" placeholder="INV. N°2">
                                                </td>
                                                <td>
                                                  <input name="txtinve2a22" onkeyup="eval2()" id="txtinve2a22" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°2 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve3a2" id="txtinve3a2" type="text" class="form-control"  value="" placeholder="INV. N°3">
                                                </td>
                                                <td>
                                                  <input name="txtinve3a32" onkeyup="eval2()" id="txtinve3a32" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°3 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtinve4a2" id="txtinve4a2" type="text" class="form-control"  value="" placeholder="INV. N°4">
                                                </td>
                                                <td>
                                                  <input name="txtinve4a42" onkeyup="eval2()" id="txtinve4a42" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°4 MONTO" onkeypress="return numi(event)">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                <label>Total Inventario</label>
                                                </td>
                                                <td>
                                                  <input name="txttotalInventarioa2" id="txttotalInventarioa2" title="Total del Inventario" type="text" class="form-control drag0106" disabled  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>

                                        </div>
                                      </div>
                                      <?php //2 ?>
                                      <hr>
                                      <div class="col-md-4">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>II. VENTAS</strong></label>
                                            <br>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label>VENTAS DIARIAS:</label>
                                            <input name="txtventaDiaria2" id="txtventaDiaria2" type="text" title="Ventas Diarias" disabled placeholder="Ventas diaria estimada" class="form-control drag0106"  value="">
                                            <br>
                                            <label>VENTAS DECLARADAS</label>
                                            <table>
                                              <tr>
                                                <td>
                                                  <label>BUENO:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" name="txtbueno2" id="txtbueno2" type="text" title="Dia Bueno" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="Pendiente">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>MALO:</label>
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" name="txtmalo2" id="txtmalo2" type="text" title="Dia Malo" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Monto del inventario">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <label>ESTIMACIÓN:</label>
                                                </td>
                                                <td>
                                                  <input  name="txtestimacion2" id="txtestimacion2" disabled title="Estimación" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                      <?php //3 ?>
                                    <div class="row">
                                      <div class="col-md-8">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>III. COSTO MERC/PROD</strong></label>
                                            <br>

                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table>
                                              <tr>
                                                <td>
                                                    <label>2da Evaluación</label>
                                                </td>
                                                <td>
                                                    <label>Precio de Compra</label>
                                                </td>
                                                <td>
                                                    <label>Precio de Venta</label>
                                                </td>
                                                <td>
                                                    <label>C.R.</label>
                                                </td>
                                                <td>
                                                    <label>C.R. Promedio</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a12" id="txtcosto1a12" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio12" id="txtcosto1Precio12" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta12" id="txtcosto1Venta12" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr12" id="txtcosto1Cr12" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td rowspan="7">
                                                  <input name="txtcosto1Prom12" id="txtcosto1Prom12" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a22" id="txtcosto1a22" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio22" id="txtcosto1Precio22" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta22" id="txtcosto1Venta22" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr22" id="txtcosto1Cr22" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a32" id="txtcosto1a32" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio32" id="txtcosto1Precio32" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta32" id="txtcosto1Venta32" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr32" id="txtcosto1Cr32" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a42" id="txtcosto1a42" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio42" id="txtcosto1Precio42" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta42" id="txtcosto1Venta42" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr42" id="txtcosto1Cr42" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <?php //comienza en injerto ?>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a52" id="txtcosto1a52" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio52" id="txtcosto1Precio52" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta52" id="txtcosto1Venta52" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr52" id="txtcosto1Cr52" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a62" id="txtcosto1a62" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio62" id="txtcosto1Precio62" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta62" id="txtcosto1Venta62" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr62" id="txtcosto1Cr62" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td>
                                                  <input name="txtcosto1a72" id="txtcosto1a72" type="text" class="form-control"  value="" placeholder="">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio72" id="txtcosto1Precio72" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input onkeyup="eval2()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta72" id="txtcosto1Venta72" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td>
                                                  <input name="txtcosto1Cr72" id="txtcosto1Cr72" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                              </tr>
                                            </table>
                                          </div>

                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                    <?php //4 otros INGRESOS ?>
                                    <div class="row">

                                      <div class="col-md-8">
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <label><strong>IV. OTROS INGRESOS</strong></label>
                                            <br>
                                          </div>
                                        </div>
                                        <div class="row form-group">
                                          <div class="col-md-12">
                                            <table width="100%">
                                              <tr>
                                                <td align="center">
                                                    <label>MONTO</label>
                                                </td>
                                                <td align="center">
                                                    <label>DESCRIPCIÓN</label>
                                                </td>
                                                <td align="center">
                                                  <label>TOTAL</label>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td width="15%">
                                                    <input onkeyup="eval2()" maxlength="10" name="txtingreso12" id="txtingreso12" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td width="70%">
                                                  <input class="form-control" type="text" name="txtingresoD12" id="txtingresoD12" value="" placeholder="Describa el ingreso.">
                                                </td>
                                                <td rowspan="3" width="15%">
                                                  <input class="form-control" type="text" name="txttotalOtroIngreso2" id="txttotalOtroIngreso2" value="" disabled style="background:white;text-align:right" placeholder="0.00">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td >
                                                    <input onkeyup="eval2()" maxlength="10" name="txtingreso22" id="txtingreso22" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td >
                                                  <input class="form-control" type="text" name="txtingresoD22" id="txtingresoD22" value="" placeholder="Describa el ingreso.">
                                                </td>
                                              </tr>
                                              <tr>
                                                <td >
                                                    <input onkeyup="eval2()" maxlength="10" name="txtingreso32" id="txtingreso32" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                                </td>
                                                <td >
                                                  <input class="form-control" type="text" name="txtingresoD32" id="txtingresoD32" value="" placeholder="Describa el ingreso.">
                                                </td>
                                              </tr>

                                            </table>
                                          </div>

                                        </div>
                                      </div>
                                    </div>
                                </div>
                              </div>
                           </div>
                           <?php //TERCERA EVALUACION ?>
                           <div class="col-lg-12">
                             <div class="ibox float-e-margins">
                                <div class="ibox-title" style="background:<?php echo $jua1['color']?> ">
                                    <h5 style="color:white;font-weight:bold">TERCERA EVALUACIÓN <small> </small></h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link" style="color:white">
                                            <i class="fa fa-chevron-down"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none">
                                  <div class="row">
                                    <?php //1 ?>
                                    <div class="col-md-4">
                                      <div class="row form-group">
                                        <?php //caso 1 ?>
                                        <div class="col-md-12">
                                          <label><strong>I. ACTIVO A CORTO PLAZO</strong></label>
                                          <br>
                                          <label class="control-label">FECHA :</label>
                                          <div class="input-group margin">
                                            <input type="text" class="form-control datepicker" onclick="fechaEva1()" placeholder="dd/mm/aaaa" name="fecha3" id="fecha3" style="text-align:center" value="<?php echo $f; ?>">
                                            <span class="input-group-btn">
                                              <a class="btn btn-info btn-flat" disabled style="height:34px;font-weight:bold"><i class="fa fa-calendar"></i></a>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <table width="100%">
                                            <tr>
                                              <td>
                                                <label>DISPONIBLE:</label>
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" name="txtdiponible3" id="txtdiponible3" type="text" title="Disponible en el momento" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Disponible en el momento">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <label>CUENTAS POR COBRAR:</label>
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" name="txtporcobrar3" id="txtporcobrar3" type="text" title="Cuentas por Cobrar." class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Pendiente">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <label>INVENTARIO:</label>
                                              </td>
                                              <td>
                                                <input name="txttotinven3" id="txttotinven3" title="Total del Inventario" type="text" class="form-control drag0106"  value="" placeholder="Monto del inventario" disabled>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <label>TOTAL ACT. CORTO PLAZO:</label>
                                              </td>
                                              <td>
                                                <input name="txttotalCorto3" id="txttotalCorto3" title="Total Activo a Corto Plazo" type="text" class="form-control drag0106"  value="" placeholder="0.00" disabled>
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                    <?php //1.2 ?>
                                    <div class="col-md-4">
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <label>INVENTARIO</label>
                                          <table width="100%">
                                            <tr>
                                              <td align="center">
                                                <label>DESCRIPCION:</label>
                                              </td>
                                              <td align="center">
                                                <label>MONTO s/.</label>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtinve1a3" id="txtinve1a3" type="text" class="form-control"  value="" placeholder="INV. N°1">
                                              </td>
                                              <td>
                                                <input name="txtinve1a13" onkeyup="eval3()" id="txtinve1a13" type="text" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="INV. N°1 MONTO">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtinve2a3" id="txtinve2a3" type="text" class="form-control"  value="" placeholder="INV. N°2">
                                              </td>
                                              <td>
                                                <input name="txtinve2a23" onkeyup="eval3()" id="txtinve2a23" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°2 MONTO" onkeypress="return numi(event)">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtinve3a31" id="txtinve3a31" type="text" class="form-control"  value="" placeholder="INV. N°3">
                                              </td>
                                              <td>
                                                <input name="txtinve3a33" onkeyup="eval3()" id="txtinve3a33" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°3 MONTO" onkeypress="return numi(event)">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtinve4a3" id="txtinve4a3" type="text" class="form-control"  value="" placeholder="INV. N°4">
                                              </td>
                                              <td>
                                                <input name="txtinve4a43" onkeyup="eval3()" id="txtinve4a43" type="text" class="form-control drag0106" maxlength="10" value="" placeholder="INV. N°4 MONTO" onkeypress="return numi(event)">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                              <label>Total Inventario</label>
                                              </td>
                                              <td>
                                                <input name="txttotalInventarioa3" id="txttotalInventarioa3" title="Total del Inventario" type="text" class="form-control drag0106" disabled  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                    <?php //2 ?>
                                    <hr>
                                    <div class="col-md-4">
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <label><strong>II. VENTAS</strong></label>
                                          <br>
                                        </div>
                                      </div>
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <label>VENTAS DIARIAS:</label>
                                          <input name="txtventaDiaria3" id="txtventaDiaria3" type="text" title="Ventas Diarias" disabled placeholder="Ventas diaria estimada" class="form-control drag0106"  value="">
                                          <br>
                                          <label>VENTAS DECLARADAS</label>
                                          <table>
                                            <tr>
                                              <td>
                                                <label>BUENO:</label>
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" name="txtbueno3" id="txtbueno3" type="text" title="Dia Bueno" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)"  value="" placeholder="Pendiente">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <label>MALO:</label>
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" name="txtmalo3" id="txtmalo3" type="text" title="Dia Malo" class="form-control drag0106" maxlength="10" onkeypress="return numi(event)" value="" placeholder="Monto del inventario">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <label>ESTIMACIÓN:</label>
                                              </td>
                                              <td>
                                                <input  name="txtestimacion3" id="txtestimacion3" disabled title="Estimación" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <hr>
                                    <?php //3 ?>
                                  <div class="row">
                                    <div class="col-md-8">
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <label><strong>III. COSTO MERC/PROD</strong></label>
                                          <br>
                                        </div>
                                      </div>
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <table>
                                            <tr>
                                              <td>
                                                  <label>3ra Evaluación</label>
                                              </td>
                                              <td>
                                                  <label>Precio de Compra</label>
                                              </td>
                                              <td>
                                                  <label>Precio de Venta</label>
                                              </td>
                                              <td>
                                                  <label>C.R.</label>
                                              </td>
                                              <td>
                                                  <label>C.R. Promedio</label>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a13" id="txtcosto1a13" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio13" id="txtcosto1Precio13" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta13" id="txtcosto1Venta13" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr13" id="txtcosto1Cr13" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td rowspan="7">
                                                <input name="txtcosto1Prom13" id="txtcosto1Prom13" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a23" id="txtcosto1a23" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio23" id="txtcosto1Precio23" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta23" id="txtcosto1Venta23" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr23" id="txtcosto1Cr23" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a33" id="txtcosto1a33" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio33" id="txtcosto1Precio33" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta33" id="txtcosto1Venta33" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr33" id="txtcosto1Cr33" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a43" id="txtcosto1a43" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio43" id="txtcosto1Precio43" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta43" id="txtcosto1Venta43" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr43" id="txtcosto1Cr43" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <?php //comienza en injerto ?>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a53" id="txtcosto1a53" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio53" id="txtcosto1Precio53" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta53" id="txtcosto1Venta53" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr53" id="txtcosto1Cr53" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a63" id="txtcosto1a62" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio63" id="txtcosto1Precio63" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta63" id="txtcosto1Venta63" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr63" id="txtcosto1Cr63" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input name="txtcosto1a73" id="txtcosto1a73" type="text" class="form-control"  value="" placeholder="">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Precio73" id="txtcosto1Precio73" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input onkeyup="eval3()" maxlength="10" onkeypress="return numi(event)" name="txtcosto1Venta73" id="txtcosto1Venta73" type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td>
                                                <input name="txtcosto1Cr73" id="txtcosto1Cr73" disabled type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <hr>
                                  <?php //4 otros INGRESOS ?>
                                  <div class="row">
                                    <div class="col-md-8">
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <label><strong>IV. OTROS INGRESOS</strong></label>
                                          <br>
                                        </div>
                                      </div>
                                      <div class="row form-group">
                                        <div class="col-md-12">
                                          <table width="100%">
                                            <tr>
                                              <td align="center">
                                                  <label>MONTO</label>
                                              </td>
                                              <td align="center">
                                                  <label>DESCRIPCIÓN</label>
                                              </td>
                                              <td align="center">
                                                <label>TOTAL</label>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td width="15%">
                                                  <input onkeyup="eval3()" maxlength="10" name="txtingreso13" id="txtingreso13" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td width="70%">
                                                <input class="form-control" type="text" name="txtingresoD13" id="txtingresoD13" value="" placeholder="Describa el ingreso.">
                                              </td>
                                              <td rowspan="3" width="15%">
                                                <input class="form-control" type="text" name="txttotalOtroIngreso3" id="txttotalOtroIngreso3" value="" disabled style="background:white;text-align:right" placeholder="0.00">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td >
                                                  <input onkeyup="eval3()" maxlength="10" name="txtingreso23" id="txtingreso23" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td >
                                                <input class="form-control" type="text" name="txtingresoD23" id="txtingresoD23" value="" placeholder="Describa el ingreso.">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td >
                                                  <input onkeyup="eval3()" maxlength="10" name="txtingreso33" id="txtingreso33" onkeypress="return numi(event)"  type="text" class="form-control drag0106"  value="" placeholder="0.00">
                                              </td>
                                              <td >
                                                <input class="form-control" type="text" name="txtingresoD33" id="txtingresoD33" value="" placeholder="Describa el ingreso.">
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                           </div>
                           <hr style="border: 0 ; border-top: 4px double #23c6c8; width: 90%;">
                           <?php //RESULTADO ?>
                           <div class="col-lg-12">
                             <div class="ibox float-e-margins">
                                <div class="ibox-title" style="background:<?php echo $jua1['color']?> ">
                                    <h5 style="color:white;font-weight:bold">RESULTADOS <small> </small></h5>
                                </div>
                                <div class="ibox-content" >
                                    <div class="row">
                                        <?php //respuestas ?>
                                      <div class="col-md-12">
                                        <div class="row form-group">
                                          <div class="col-md-12" style="content-aling:center">
                                            <table align="center" width="100%" class="table table-striped table-bordered table-hover dataTables-example dataTable" >
                                              <tr>
                                                <td colspan="4" align="center" class="dr" style="background:<?php echo $jua1['color']?>">
                                                    <label><strong>RESUMEN ECONOMICO DE LA ACTIVIDAD</strong></label>
                                                </td>
                                              </tr>
                                              <tr >
                                                <td>
                                                    <label>&nbsp;</label>
                                                </td>
                                                <td class="dr" style="background:<?php echo $jua1['color']?>">
                                                    <label>1ra Evaluación</label>
                                                </td>
                                                <td class="dr" style="background:<?php echo $jua1['color']?>">
                                                    <label>2da Evaluación</label>
                                                </td>
                                                <td class="dr" style="background:<?php echo $jua1['color']?>">
                                                    <label>3ra Evaluación</label>
                                                </td>
                                              </tr>
                                              <?php //1 ?>
                                              <tr>
                                                <td>
                                                  <label>FECHA:</label>
                                                </td>
                                                <td>
                                                  <input name="txtf1" id="txtf1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtf2" id="txtf2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtf3" id="txtf3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //2 ?>
                                              <tr>
                                                <td>
                                                  <label>VENTA ESTIMADA</label>
                                                </td>
                                                <td>
                                                  <input name="txtv1" id="txtv1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtv2" id="txtv2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtv3" id="txtv3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //3 ?>
                                              <tr>
                                                <td>
                                                  <label>OTROS INGRESOS</label>
                                                </td>
                                                <td>
                                                  <input name="txto1" id="txto1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txto2" id="txto2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txto3" id="txto3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //4 ?>
                                              <tr>
                                                <td>
                                                  <label>TOTAL DE INGRESOS</label>
                                                </td>
                                                <td>
                                                  <input name="txtti1" id="txtti1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtti2" id="txtti2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtti3" id="txtti3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //5 ?>
                                              <tr>
                                                <td>
                                                  <label>COSTO MERC/PROD</label>
                                                </td>
                                                <td>
                                                  <input name="txtcmp1" id="txtcmp1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtcmp2" id="txtcmp2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtcmp3" id="txtcmp3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //6 ?>
                                              <tr>
                                                <td>
                                                  <label>UTILIDAD BRUTA</label>
                                                </td>
                                                <td>
                                                  <input name="txtub1" id="txtub1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtub2" id="txtub2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtub3" id="txtub3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //7 ?>
                                              <tr>
                                                <td>
                                                  <label>% COBERTURA - OTROS COSTOS</label>
                                                </td>
                                                <td>
                                                  <input name="txtcoc1" id="txtcoc1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtcoc2" id="txtcoc2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtcoc3" id="txtcoc3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                              <?php //8 ?>
                                              <tr>
                                                <td>
                                                  <label>EXCEDENTE DEL CLIENTE</label>
                                                </td>
                                                <td>
                                                  <input name="txtec1" id="txtec1" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtec2" id="txtec2" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                                <td>
                                                  <input name="txtec3" id="txtec3" type="text" class="form-control" style="background-color:white;color:blue;font-weight:bold;text-align:center" name="" value="" disabled >
                                                </td>
                                              </tr>
                                            </table>
                                          </div>
                                          <div class="col-md-12">
                                            <table align="center">
                                              <tr>
                                                <td>
                                                  <button type="button" class="btn btn-md btn-info" name="button" onclick="imprime()"><i class="fa fa-print"> </i> Imprimir</button>

                                                </td>
                                              </tr>
                                            </table>

                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
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
<?php
include('footer.php');
?>
<script src="fecha/moment.js"></script>
<script src="fecha/bootstrap-material-datetimepicker.js"></script>
<script src="evaluacion/eval1.js"></script>
<script>
       $('.datepicker').bootstrapMaterialDatePicker({
           weekStart: 0,
           time: false,
           format: 'DD/MM/YYYY'
       });
</script>
