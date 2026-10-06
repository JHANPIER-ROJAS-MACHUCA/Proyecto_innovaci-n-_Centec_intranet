<?php
if (isset($con)) {
?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width: 1000px;">
			<div class="panel panel-info">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar Cliente</h4>
				</div>
				<form autocomplete="off" class="form-horizontal" method="post" id="editar_cliente" name="editar_cliente">
					<div class="panel-body">
						<div id="resultados_ajax2"></div>
						<div class="col-lg-4">
							<div class="form-group">
								<label for="fecha">A.P</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_ap" name="mod_ap" placeholder="Apellido Paterno" value="" required>
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">DNI</label>
								<div class="col-sm-12">
									<input readonly type="text" class="form-control" id="mod_dni" name="mod_dni" placeholder="DNI" required onkeypress="return numero(event)" maxlength="8">
									<input type="hidden" id="mod_id" name="mod_id" required>
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Correo</label>
								<div class="col-sm-12">
									<input type="email" class="form-control" id="mod_correo" name="mod_correo" placeholder="Correo" value="">
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Nº Hijos</label>
								<div class="col-sm-12">
									<input type="number" min="0" max="30" class="form-control" id="mod_nhijos" name="mod_nhijos" placeholder="Nº Hijos" value="">
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Estado Civil</label><br>
								<div class="col-sm-12">
									<!--
				 <input type="text" class="form-control" id="mod_ecivil" name="mod_ecivil" pattern="S|C|V|D|Conv|Sep" required>-->
									<select class="form-control" id="mod_ecivil" name="mod_ecivil">
										<option value="S">Soltero</option>
										<option value="C">Casado</option>
										<option value="V">Viudo</option>
										<option value="D">Divorciado</option>
										<option value="Conv">Conviviente</option>
										<option value="Sep">Separado</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">Lugar de Nac.</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_lugarnac" name="mod_lugarnac" placeholder="Lugar de Nacimiento">
								</div>
							</div>
							<?php $idO = $_COOKIE['tofi'];  ?>
							<div class="form-group">
								<label class="">Usuario</label>
								<div class="col-sm-12">
									<select required class="form-control m-b" name="mod_idusu" id="mod_idusu">
										<option value="" disabled selected>--Seleccione--</option>
										<?php
										//mostramos las oficinas
										include('../conection/bdcredito.php');
										if ($_COOKIE['tuser'] != '4') {
											$uofi = extraer("select * from tusuario where (tipoU='4' or tipoU='3') and estadoU=1");
										} else {
											$uofi = extraer("select * from tusuario where idU='$userId' and estadoU=1");
										}

										while ($row1 = mysqli_fetch_array($uofi)) {
										?>
											<option value="<?php echo $row1['idU']; ?>"> <?php echo $row1['dniU'] . " " . $row1['apU'] . " " . $row1['amU'] . ' ' . $row1['nomU'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label>Condición</label>
								<select name="status" class="form-control" id="mod_status">
									<option value="ACTIVE">ACTIVO</option>
									<option value="INACTIVE">INACTIVO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group">
								<label for="fecha">A.M</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_am" name="mod_am" placeholder="Apellido Materno" value="" required>
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Sexo M/F</label><br>
								<!--<input type="text" class="form-control" id="mod_sexo" name="mod_sexo"  pattern="M|F" required>-->
								<div class="col-sm-12">
									<select class="form-control" id="mod_sexo" name="mod_sexo">
										<option value="M">M</option>
										<option value="F">F</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">Rubro.</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_rubro" name="mod_rubro" placeholder="Rubro.">
								</div>
							</div>
							<div class="form-group">
								<label for="codigo">Grado Inst.</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_grado" name="mod_grado" placeholder="Grado Int.">
								</div>
							</div>

							<div class="form-group">
								<label for="fecha">Vivienda</label><br>
								<div class="col-sm-12">
									<select class="form-control" id="mod_tipo" name="mod_tipo">
										<option value="Propia">Propia</option>
										<option value="Familiar">Familiar</option>
										<option value="Alquilada">Alquilada</option>

									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="fecha">Tipo de cliente</label><br>
								<div class="col-sm-12">
									<select class="form-control" name="client_type" id="mod_client_type">
										<option value="">Seleccione una opción</option>
										<?php
										$stmtClientType = extraer("select * from dictionaries where type='CLIENT_TYPE' and deleted_at is null");
										while ($dictionary = mysqli_fetch_array($stmtClientType)) {
										?>
											<option value="<?php echo $dictionary['id'] ?>"><?php echo $dictionary['description'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">Comentario</label>
								<div class="col-sm-12">
									<textarea type="text" class="form-control" id="mod_comentario" name="mod_comentario" placeholder="Comentario"> </textarea>
								</div>
							</div>
						</div>

						<div class="col-lg-4">

							<div class="form-group">
								<label for="fecha">Nombres</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_nombres" name="mod_nombres" placeholder="Nombres" value="" required>
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Celular</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_celular" name="mod_celular" placeholder="Celular" value="">
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Telefono</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_telefono" name="mod_telefono" placeholder="Telefono" value="">
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Fecha de Nacimiento</label>
								<div class="col-sm-12">
									<input type="date" class="form-control" id="mod_fecha" name="mod_fecha" placeholder="Fecha Nacimiento" value="">
								</div>

							</div>
							<div class="form-group">
								<label for="codigo">Direccion Domiciliaria</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_direccion" name="mod_direccion" placeholder="Direccion domiciliaria">
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">Perfil de riesgo</label>
								<div class="col-sm-12">
									<select name="risk_profile_id" id="mod_risk_profile_id" class="form-control">
										<option value="">Seleccione una opción</option>
										<?php
										$stmtClientType = extraer("select * from dictionaries where type='RISK_PROFILE' and deleted_at is null");
										while ($dictionary = mysqli_fetch_array($stmtClientType)) {
										?>
											<option value="<?php echo $dictionary['id'] ?>">
												<?php
												switch ($dictionary['description']) {
													case 'red':
														echo 'Alto riesgo';
														break;
													case 'yellow':
														echo 'Mediano riesgo';
														break;
													case 'gray':
														echo 'No registra información en el mes';
														break;
													case 'green':
														echo 'Sin riesgo';
														break;
												}
												?>
											</option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="codigo">Referencias</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="mod_referencias" name="mod_referencias" placeholder="Referencias">
								</div>
							</div>


						</div>
					</div>
					<div class="panel-footer" align="right">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary" id="actualizar_datos">Actualizar datos</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
?>