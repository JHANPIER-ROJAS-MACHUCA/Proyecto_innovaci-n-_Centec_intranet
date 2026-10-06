<div class="modal fade le" id="agrePrest">
    <div class="modal-dialog" >
      <div class="modal-content">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title j7">
                            <h5 id="tituP">Nuevo Prestamo<small>.</small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="load(1)">&times;</button>
                        </div>
                        <div class="ibox-content">
                            <form id="aPrestamo" class="form-horizontal" enctype="multipart/form-data" autocomplete="off">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="">Nº Credito</label>
                                    <div class="col-sm-10">
                                     <input type="number" min="1" step="1"  class="form-control" required placeholder="nº" id="txtncredito"  name="txtncredito">
                                    </div>
                                </div>
                                 <div class="form-group">
                                  <label class="">Monto aprobado</label>
                                    <div class="col-sm-10">
                                       <input type="text" class="form-control" id="txtmontoa" name="txtmontoa" placeholder="0.00"  pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
                                    </div>
                                </div>
                                 <div class="form-group">
                                  <label class="">Plazo</label>
                                    <div class="col-sm-10">
                                      <select class="form-control m-b" name="txtplazo" id="txtplazo" required>
                                        <option value="" disabled selected>--Seleccione--</option>
                                        <option value="20 Dias">20 Dias</option>
                                        <option value="30 Dias">30 Dias</option>
                                        <option value="4 Sem">4 Sem</option>
                                    </select>
                                    </div>
                                  </div>
                                <div class="form-group">
                                  <label class="">Nº Cuotas</label>
                                    <div class="col-sm-10">
                                     <input type="number" min="1" step="1"  class="form-control" required placeholder="nº" id="txtncuota"  name="txtncuota">
                                    </div>
                                </div>
                                <div class="form-group">
                                  <label class="">Fecha Termino</label>
                                    <div >
                                        <input id="txtfechat" name="txtfechat" type="date" class="form-control">
                                    </div>
                                </div>
                          </div>
                          <div class="col-lg-4">
                                <div class="form-group">
                                  <label class="">Fecha </label>
                                    <div>
                                        <input id="txtfechad" name="txtfechad" type="date" class="form-control">
                                    </div>
                                </div>
                                 <div class="form-group">
                                  <label class="">Monto desembolso</label>
                                    <div class="col-sm-10">
                                     <input type="text" class="form-control" id="txtmontod" name="txtmontod" placeholder="0.00" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
                                    </div>
                                </div>
                                 <div class="form-group">
                                  <label class="">Taza</label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="number" min="0" step="1"  class="form-control" required placeholder="%" id="txttaza"  name="txttaza">
                                          <span class="input-group-addon">%</span>
                                      </div>
                                    </div>
                                </div>
                                  <div class="form-group">
                                  <label class="">Dias Pasados</label>
                                    <div class="col-sm-10">
                                     <input type="number" min="1" step="1" class="form-control"  name="txtdp" id="txtdp">
                                    </div>
                                </div>
                                <div class="form-group">
                                  <label class="">Estado</label>
                                    <div class="col-sm-10">
                                      <select class="form-control m-b" name="txtestado" id="txtestado" required>
                                        <option value="" disabled selected>--Seleccione--</option>
                                        <option value="Propuesto">Propuesto</option>
                                        <option value="Aprobado">Aprobado</option>
                                        <option value="Desaprobado">Desaprobado</option>
                                        <option value="Cancelado">Cancelado</option>
                                        <option value="Anulado">Anulado</option>
                                    </select>
                                    </div>
                                  </div>

                      </div>
                      <div class="col-lg-4">
                          <div class="form-group">
                                  <label class="">Monto Propuesto</label>
                                    <div class="col-sm-10">
                                      <input type="hidden" name="idpe" id="idpe" class="form-control" placeholder="ide del prestamo a editar">
                                     <input type="hidden" name="txtidCG"  value="<?php echo $idCG; ?>">
                                      <input type="text" class="form-control" id="txtmontop" name="txtmontop" placeholder="0.00" required pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
                                    </div>
                               </div>
                                <div class="form-group">
                                  <label class="">Pagos</label>
                                    <div class="col-sm-10">
                                      <select class="form-control m-b" name="txtpago" id="txtpago" required>
                                        <option value="" disabled selected>--Seleccione--</option>
                                        <option value="Diario">Diario</option>
                                        <option value="Semanal">Semanal</option>
                                        <option value="Mensual">Mensual</option>
                                    </select>
                                    </div>
                                  </div>
                                    <div class="form-group">
                                        <label class="">Cuota</label>
                                     <div class="col-sm-10">
                                     <input type="text" class="form-control" id="txtcuota" name="txtcuota" placeholder="0.00" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
                                    </div>
                                </div>
                                  <div class="form-group">
                                  <label class="">Tipo Prestamo</label>
                                    <div class="col-sm-10">
                                      <select class="form-control m-b" name="txttip" id="txttip" >
                                        <option value="" disabled selected>--Seleccione--</option>
                                        <option value="Transporte">Transporte</option>
                                        <option value="Prendario">Prendario</option>
                                    </select>
                                    </div>
                                  </div>
                               </div>
                                  <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="submit" id="agreP2" onclick="setTimeout('guarsalir(1)',1000);" name="" value="Guardar y Salir" class="btn btn-info">
                                    </div>
                                    <div class="col-sm-3 ">
                                        <button data-dismiss="modal" aria-hidden="true" class="btn btn-white" type="reset">Cancel</button>
                                    </div>
                                    <div class="col-sm-4 ">
                                        <input type="submit" id="agreP" onclick="setTimeout('load(1)',1000);" name="" value="Guardar y Limpiar" class="btn btn-primary">
                                    </div>
                                </div>

                            </form>
                     <script src="functiones/prestamo.js"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
