<div class="modal fade le" id="agreOfi">
		<div class="modal-dialog">
			<div class="modal-content">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title j7">
                            <h5 id="tituO">Nueva Oficina<small>.</small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="load(1)">&times;</button>
                        </div>
                        <div class="ibox-content">
                            <form id="aOficina" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
															<div class="form-group">
                                  <label class="col-sm-4 control-label">DEPARTAMENTO</label>
                                    <div class="col-sm-8">
																			<input type="hidden" name="idoe" id="idoe" class="form-control" placeholder="ide del usuario a editar">
																			<select class="form-control m-b" name="txtdepa" id="txtdepa" required>
																				<option value="">--Seleccione--</option>
																			</select>
                                    </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-sm-4 control-label">PROVINCIA</label>
                                    <div class="col-sm-8">
                                      <select class="form-control m-b" name="txtprovi" id="txtprovi" required>
																				<option value="">--Seleccione--</option>
																			</select>
                                    </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-sm-4 control-label">DISTRITO</label>
                                    <div class="col-sm-8">
																			<select class="form-control m-b" name="txtdis" id="txtdis" required>
																				<option value="">--Seleccione--</option>
																			</select>
                                    </div>
                                </div>

																<div class="form-group">
																	<label class="col-sm-4 control-label">DIRECCION</label>
																		<div class="col-sm-8">
																			<textarea class="form-control" rows="3" placeholder="DONDE SE ENCUENTRA LA OFICINA" autocomplete="off" style="text-transform:uppercase" required id="txtdirec" name="txtdirec"></textarea>
																		</div>
																</div>

                                <div class="form-group">
                                  <label class="col-sm-4 control-label">TELEFONO</label>
                                    <div class="col-sm-8">
                                      <input type="text"  class="form-control" maxlength="12" onkeypress="return numero2(event)" required placeholder="000-00000000" id="txttel" name="txttel">
                                    </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-sm-4 control-label">CORREO</label>
                                    <div class="col-sm-8">
                                      <input type="email" class="form-control" placeholder="centro@tecnologico.com" autocomplete="off" required id="txtema" name="txtema">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-3 ">
                                        <button data-dismiss="modal" aria-hidden="true" class="btn btn-white" type="reset">Cancel</button>
                                    </div>

                                    <div class="col-sm-4 ">
                                        <input type="submit" id="agreU" onclick="setTimeout('load(1)',1000);" name="" value="Guardar y Limpiar" class="btn btn-primary">
																		</div>

																		<div class="col-sm-4">
																				<input type="submit" id="agreU2" onclick="setTimeout('guarsalir(1)',1000);" name="" value="Guardar y Salir" class="btn btn-info">
                                    </div>
                                </div>

                            </form>
														<script src="extra/buscador.js">
														</script>
                            <script src="functiones/ahorro.js"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
  <!--<script src="../adaptadores/previsualizar.js"></script>-->

		<script type="text/javascript">
		function readURL(input) {
		if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
		$('#img1').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
		}
		}
		$("#img").change(function() {
		readURL(this);
		});

		</script>
