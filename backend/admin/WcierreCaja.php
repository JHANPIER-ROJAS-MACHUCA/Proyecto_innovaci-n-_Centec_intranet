<?php
include('head.php');
?>
<div class="panel panel-info">
         <div class="panel-heading">
                Cierre de Caja
         </div>
         <div class="panel-body">
         	       <div class="form-group  row">           
                              <label class="col-md-1 control-label">Fecha</label>
                              <div class="col-md-3">
                                  <input type="date" class="form-control input-sm" id="" required>
                                  <input type="hidden" id="id" name="id" readonly required value="" /> 
                              </div>
                              <label class="col-md-1 control-label">Codigo</label>
                                        <div class="col-md-3">
                                            <input style="width: 30%" type="text" class="form-control input-sm"  readonly>
                                        </div>
                                <label  class="col-md-1 control-label">Usuario</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control input-sm" id=""  readonly>
                                        </div>
                     </div>
                       <div class="form-group  row">  
                       <div align="right"><label class="col-md-1 control-label">Nombre</label></div>         
                              <div class="col-md-7">
                                  <input type="text" readonly class="form-control input-sm" id="" required>
                              </div>
                     </div>
         	<div class="tabs-container">
              	<ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#tab-1"> Cuadre Caja en Soles (S/.)</a></li>
               </ul>   
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                    	<br>
                    	<div class="form-group">
                    		<div class="col-md-12 ">
                    		<div class="col-md-8">
	                    		<label align="right"  class="col-md-4">SALDO INICIAL   SOLES (S/.):</label>
								<div class="col-md-4">
									<input type="text" readonly class="form-control" value="0.00">
								</div>
                    		</div>
                    	</div>
                    	</div><br><br>
                    	<div class="form-group">
                    		<div class="col-md-12 ">
                    		<div class="col-md-6">
	                    		 <div class="panel panel-info">
                                        <div class="panel-heading">
                                              <h3 class="panel-title" align="center"><b>INGRESOS</b></h3>
                                        </div>
                                        <div class="panel-body">
                                             <div class="ibox-content table-responsive" id="ingresos">
                    						</div>
                                        </div>
                                        <div class="panel-footer">
                                        	             <div class="form-group row">
             	<div  align="right"><label class="col-md-5 control-label">TOTAL INGRESOS:</label></div>
                     <div class="col-md-6">
                        <input style="width: 50%" readonly type="text" class="form-control input-sm" id="">
                      </div>
            			</div>
                                        </div>
                                    </div>
                    		</div>
                    		<div class="col-md-6">
	                    		 <div class="panel panel-info">
                                        <div class="panel-heading">
                                             <h3 class="panel-title" align="center"><b>EGRESOS</b></h3>
                                        </div>
                                        <div class="panel-body">
                                            <p>Egresos</p>
                                        </div>
                                        <div class="panel-footer">
                                          <!--  TOTAL EGRESOS : <input style="width:30%" class="form-control" type="number" name="" readonly>-->
                                             <div class="form-group row">
             	<div style="text-align: right;"><label class="col-md-5 control-label">TOTAL EGRESOS:</label></div>
                     <div class="col-md-6">
                        <input style="width: 50%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
                                        </div>
                                    </div>
                    		</div>
                    	</div>
                    	</div>
                   </div>
                </div>
             </div> 
             <div class="form-group row">
             	<div align="right"><label class="col-md-5 control-label">SALDO FINAL SOLES (S/.):</label></div>
                     <div class="col-md-6">
                        <input style="width: 30%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
               <div class="form-group row">
             	<div align="right"><label class="col-md-4 control-label">SALDO FINAL CORTE (S/.):</label></div>
                     <div class="col-md-2">
                        <input style="width: 100%" readonly type="text" class="form-control input-sm" id="">
                      </div>
                      <div align="lefth"><label class="col-md-1 control-label">DIFERENCIA (S/.):</label></div>
                     <div align="lefth"  class="col-md-2">
                        <input style="width: 100%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
        </div>
          <div align="right" class="panel-footer">
                                          	  	<button type="submit" class="btn btn-info">GUARDAR</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">SALIR</button>
                                        </div>
 </div>
<?php
include('footer.php');
 ?>
  <script src="functiones/cierrecaja.js"></script>