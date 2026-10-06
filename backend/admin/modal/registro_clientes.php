<?php
if (isset($con)) {
?>

	<!-- Central Modal Medium Success -->

	<!-- Modal -->
	<div class="modal fade" id="nuevoCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

		<!--<div class="modal-dialog" role="document">-->
		<div class="modal-dialog modal-notify modal-info" role="document" style="width: 1000px;">
			<div class="panel panel-info">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo Cliente</h4>
				</div>

				<form autocomplete="off" class="form-horizontal" method="post" id="guardar_cliente" name="guardar_cliente">
					<div class="panel-body">
						<div id="resultados_ajax_clientes"></div>

						<div class="col-lg-4">
							<div class="form-group">
								<label for="codigo">DNI</label>
								<div class="col-sm-12">
									<input tabindex="1" type="text" class="form-control" id="txtdni" onkeyup="dni2('txtdni','txtap','txtam','txtnom')" name="txtdni" placeholder="DNI" required onkeypress="return numero(event)" maxlength="8">
								</div>
							</div>
							<!--
			   <div class="form-group">
				<label for="fecha">Nombres</label>

				  <input type="text" class="form-control" id="txtnom" name="txtnom" placeholder="Nombres" value=""  required>

			  </div>
			-->
							<div class="form-group">
								<label for="fecha">Nombres</label>
								<div class="col-sm-12">
									<input tabindex="4" type="text" class="form-control" id="txtnom" name="txtnom" placeholder="Nombres" value="" required>
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Correo</label>
								<div class="col-sm-12">
									<input tabindex="7" type="email" class="form-control" id="correo" name="correo" placeholder="Correo" value="">
								</div>
							</div>
							<div class="form-group">
								<label for="fecha">Nº Hijos</label>
								<div class="col-sm-12">
									<input tabindex="10" type="number" min="0" max="30" class="form-control" id="nhijos" name="nhijos" placeholder="Nº Hijos" value="">
								</div>

							</div>
							<div class="form-group">
								<label for="fecha">Estado Civil</label>
								<div class="col-sm-12">
									<select tabindex="13" class="form-control" name="ecivil">
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
									<input tabindex="16" type="text" class="form-control" id="lugarnac" name="lugarnac" placeholder="Lugar de Nacimiento">
								</div>
							</div>
							<?php $idO = $_COOKIE['tofi'];  ?>
							<div class="form-group">
								<label class="">Usuario</label>
								<div class="col-sm-12">
									<select tabindex="19" required class="form-control m-b" name="idusu" id="idusu">
										<option value="" disabled selected>--Seleccione--</option>
										<?php
										$userId = $_COOKIE['user1'];

										//mostramos las oficinas
										if ($_COOKIE['tuser'] != '4') {
											$uofi = extraer("select * from tusuario where (tipoU='4' or tipoU='3') and estadoU=1");
										} else {
											$uofi = extraer("select * from tusuario where idU='$userId' and estadoU=1");
										}

										// if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '7' || $_COOKIE['tuser'] == '5') {
										// 	$uofi = extraer("select * from tusuario where (tipoU='4' or tipoU='3') and estadoU=1");
										// } else {
										// 	$uofi = extraer("select * from tusuario where idO='$idO' and tipoU='4' and estadoU=1");
										// }
										while ($row1 = mysqli_fetch_array($uofi)) {
										?>
											<option value="<?php echo $row1['idU']; ?>">
												<?php echo $row1['dniU'] . " " . $row1['apU'] . ' ' . $row1['amU'] . ' ' . $row1['nomU'] ?>
											</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label>Condición</label>
								<select name="status" class="form-control">
									<option value="active" selected>ACTIVO</option>
									<option value="inactive">INACTIVO</option>
								</select>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label for="fecha">A.P</label>
								<div class="col-sm-12">
									<input tabindex="2" type="text" class="form-control" id="txtap" name="txtap" placeholder="Apellido Paterno" value="" required>
								</div>
							</div>


							<div class="form-group">
								<label for="fecha">Sexo</label>
								<div class="col-sm-12">
									<select tabindex="5" class="form-control" name="sexo">
										<option value="M">Masculino</option>
										<option value="F">Femenino</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="codigo">Rubro</label>
								<div class="col-sm-12">
									<input tabindex="8" type="text" class="form-control" id="rubro" name="rubro" placeholder="Rubro.">
								</div>
							</div>
							<div class="form-group">
								<label for="codigo">Grado Inst.</label>
								<div class="col-sm-12">
									<input tabindex="11" type="text" class="form-control" id="grado" name="grado" placeholder="Grado Int.">
								</div>
							</div>

							<div class="form-group">
								<label for="fecha">Vivienda</label><br>
								<div class="col-sm-12">
									<select tabindex="14" class="form-control" name="tipo">
										<option value="Propia">Propia</option>
										<option value="Familiar">Familiar</option>
										<option value="Alquilada">Alquilada</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="fecha">Tipo de cliente</label><br>
								<div class="col-sm-12">
									<select tabindex="14" class="form-control" name="client_type">
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
									<textarea tabindex="17" type="text" class="form-control" id="comentario" name="comentario" placeholder="Comentario"> </textarea>
								</div>
							</div>
						</div>
						<div class="col-lg-4">

							<div class="form-group">
								<label for="fecha">A.M</label>
								<div class="col-sm-12">
									<input tabindex="3" type="text" class="form-control" id="txtam" name="txtam" placeholder="Apellido Materno" value="" required>
								</div>

							</div>
							<div class="form-group">
								<label for="fecha">Celular</label>
								<div class="col-sm-12">
									<input tabindex="6" type="text" class="form-control" id="celular" name="celular" placeholder="Celular" value="" required>
								</div>

							</div>
							<div class="form-group">
								<label for="fecha">Telefono</label>
								<div class="col-sm-12">
									<input tabindex="9" type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" value="">
								</div>

							</div>

							<div class="form-group">
								<label for="fecha">Fecha de Nacimiento</label>
								<div class="col-sm-12">
									<input tabindex="12" type="date" class="form-control" id="fecha" name="fecha" placeholder="Fecha Nacimiento" value="">
								</div>

							</div>
							<div class="form-group">
								<label for="codigo">Direccion Domiciliaria</label>
								<div class="col-sm-12">
									<input tabindex="15" type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion domiciliaria">
								</div>
							</div>
							<div class="form-group">
								<label for="codigo">Perfil de riesgo</label>
								<div class="col-sm-12">
									<select name="risk_profile_id" class="form-control">
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
									<input tabindex="18" type="text" class="form-control" id="referencias" name="referencias" placeholder="Referencias">
								</div>
							</div>

						</div>
					</div>
					<div align="right" class="panel-footer">
						<button tabindex="20" type="submit" class="btn btn-info">REGISTRAR</button>
						<button tabindex="21" type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
					</div>
				</form>
			</div>

		</div>
	</div>
<?php
}
?>