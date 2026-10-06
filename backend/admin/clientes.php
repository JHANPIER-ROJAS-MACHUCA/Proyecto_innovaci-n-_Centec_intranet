<?php

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Dictionary;
use CrediSoporte\Domain\Models\User;

include('head.php');

$users = User::where('estadoU', '1')->whereIn('tipoU', [3, 4])->get();
$dictionaries = Dictionary::whereIn('type', ['CLIENT_TYPE', 'RISK_PROFILE'])->get();
$customers = Customer::with(['riskProfile', 'attachments', 'ubigeo' => function ($query) {
	$query->join('ubigeo_provinces', 'ubigeo_districts.province_id', 'ubigeo_provinces.id')
		->join('ubigeo_departments', 'ubigeo_provinces.department_id', 'ubigeo_departments.id')
		->select(
			'ubigeo_districts.id',
			'ubigeo_districts.name as district',
			'ubigeo_provinces.name as province',
			'ubigeo_departments.name as department'
		)
		->selectRaw('concat_ws(" ",ubigeo_departments.name, ubigeo_provinces.name, ubigeo_districts.name) as full_name');
}])->orderBy('idCG', 'desc')->get();

function textRiskProfile($value)
{
	switch ($value) {
		case 'red':
			return 'Alto riesgo';

		case 'yellow':
			return 'Mediano riesgo';

		case 'gray':
			return 'No registra información en el mes';

		case 'green':
			return 'Sin riesgo';
	}
}

?>

<style>
	.item_ubigeo {
		border: 0;
		padding: 5px 10px;
		width: 100%;
		background: white;
		text-align: left;
	}

	.item_ubigeo:hover {
		background: #F2F3F4;
	}
</style>

<div class="panel panel-info" x-data="customers" style="border-color:<?php echo $jua1['color'] ?>;">
	<div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
		<div class="pull-right">
			<a href="./../app/exel/clientes.php" class="btn btn-warning" style="margin-right: 15px;">Exportar a excel</a>
			<a class="btn btn-primary" target="_blank" href="./ubicacionCliente.php" style="margin-right: 10px;">
				<i class="fa fa-map-marker"></i> Ver en el mapa
			</a>
			<button type='button' class="btn btn-info" @click="modal_create = true">
				<span class="glyphicon glyphicon-plus"></span> Nuevo Cliente
			</button>
		</div>

		<h4><i class='glyphicon glyphicon-search'></i> Buscar Clientes <small style="color:black"> <?php echo $comentaJuve ?></small></h4>
	</div>
	<div class="panel-body">
		<div>
			<div style="display: flex; justify-content: center; align-items: center; padding: 20px;">
				<label style="margin-right: 20px;">Buscar</label>
				<input type="text" class="form-control" style="max-width: 300px;" x-model="search">
			</div>
			<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
				<template x-for="customer in filteredData" :key="customer.idCG">
					<div style="border: 1px solid #e7eaec; display: flex; flex-direction: column;">
						<div style="flex: 1 1 auto; display: flex; flex-direction: column; align-items: center; padding: 20px;" :style="{background: colorRiskProfile(customer.risk_profile?.description)}">
							<div>
								<img class="img-circle" :src="customer.sexo == 'F' ? 'img/profile2.jpg' : 'img/profile.jpg'" width="70" height="70" alt="">
							</div>
							<h3 style="text-align: center;margin-top: 10px;" x-text="customer.ap + ' ' + customer.am + ' ' + customer.nom"></h3>
							<div class="font-bold"><i class="fa fa-address-card-o"></i> <span x-text="customer.dni"></span></div>
							<div><span style="font-weight: bold;">Código:</span> <span x-text="customer.idCG"></span></div>
							<div style="text-align: center;"><span style="font-weight: bold;">Perfil de riesgo:</span> <span x-text="textRiskProfile(customer.risk_profile?.description)"></span></div>
							<div x-show="customer.cel"><i class="fa fa-phone"> </i> <span x-text="customer.cel"></span></div>
							<div x-show="customer.direc"><i class="fa fa-map-marker"></i> <span x-text="customer.direc"></span></div>
						</div>
						<div style="display: flex; flex-direction: column; align-items: center;padding: 10px 20px; border-top: 1px solid #e7eaec;">
							<div class="m-t-xs btn-group">
								<a :href="'./../app/pdf/constanciaNoAdeudo.php?customerId=' + customer.idCG" target="_blank" class="btn btn-xs btn-default">
									<i class="fa fa-file-pdf-o" style="color:black"></i>
									<span style="margin-left: 5px;">No adeudo</span>
								</a>
								<a :href="'./cliente.php?customerId=' + customer.idCG" title="Editar cliente" class="btn btn-xs btn-white">
									<i class="fa fa-linode"></i> Datos
								</a>
								<button class="btn btn-xs btn-default" title="Editar cliente" @click="openModalCustomerUpdate(customer.idCG)">
									<i class="glyphicon glyphicon-edit"></i> Editar
								</button>
								<a x-show="customer.coordinate_lat && customer.coordinate_lng" :href="'./ubicacionCliente.php?customerId=' + customer.idCG" target="_blank" class="btn btn-xs btn-default" title="Ver ubicación">
									<i class="fa fa-map-marker"></i> Ubicación
								</a>
								<!-- <a href="#" class='btn btn-xs btn-default' title='Borrar cliente'><i class="glyphicon glyphicon-trash"></i> Eliminar</a> -->
							</div>
						</div>
					</div>
				</template>
			</div>

			<template x-if="customerToUpdate">
				<div @keyup.escape.window="customerToUpdate = null" style="position: fixed;inset: 0; z-index: 3000; background: rgba(0, 0, 0, 0.5);padding: 20px;">
					<div style="display: flex; flex-direction: column; border: 1px solid #e7eaec;background: white;max-width: 700px; max-height: 100%; margin: 0 auto;border-radius: 5px;">
						<div style="padding: 10px 20px;display: flex; justify-content: space-between;">
							<div>
								<button @click="tab_modal_edit = 'general'" style="border: 1px solid transparent; font-weight: 600; border-radius: 5px; padding: 5px 15px;" :style="{background: tab_modal_edit === 'general' ? '#0EA5E9': '#F8FAFC', color: tab_modal_edit === 'general' ? 'white' : '#64748B'}">
									General
								</button>
								<button @click="tab_modal_edit = 'images'" style="border: 1px solid transparent; font-weight: 600; border-radius: 5px; padding: 5px 15px;" :style="{background: tab_modal_edit === 'images' ? '#0EA5E9': '#F8FAFC', color: tab_modal_edit === 'images' ? 'white' : '#64748B'}">
									Imagenes
								</button>
							</div>
							<button style="display: flex; justify-content: center; align-items: center;" @click="customerToUpdate = null">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
									<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
								</svg>
							</button>
						</div>
						<style>
							.child {
								display: none;
							}

							.contenido:hover .child {
								display: flex;
							}
						</style>
						<div x-show="tab_modal_edit === 'images'" style="padding: 0 20px 20px;">
							<div>
								<div>
									<div style="font-weight: semibold;">Agregar imagenes de negocio</div>
									<div style="margin-top: 10px;">
										<input type="file" class="form-control" @change="uploadImage" />
									</div>

									<template x-if="image !== null">
										<div>
											<div style="display: flex; align-items: center; justify-content: center; width: 150px; height: 150px; border: 1px solid black;">
												<img :src="image.path + image.name" style="max-width: 100%; max-height: 100%;" />
											</div>
											<div style="margin-top: 10px;">
												<button class="btn btn-sm btn-danger" @click="image = null">CANCELAR</button>
												<button class="btn btn-sm btn-success" @click="addAttachmentCustomer(customerToUpdate.idCG)">GUARDAR</button>
											</div>
										</div>
									</template>

									<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; margin-top: 20px;">
										<template x-for="attachment in customerToUpdate.attachments" :key="attachment.id">
											<div class="contenido" style="position: relative; border: 1px solid black; overflow: hidden; display: flex; justify-content: center; align-items: center;">
												<img :src="attachment.path + attachment.name" width="100%" />
												<div class="child" style="position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%);">
													<button @click="deleteAttachment(attachment.id)">Eliminar</button>
												</div>
											</div>
										</template>
									</div>
								</div>
							</div>
						</div>
						<div x-show="tab_modal_edit === 'general'" style="flex: 1 1 auto; padding: 0 20px; overflow: auto;">
							<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));gap: 20px;">
								<div>
									<label>DNI · requerido</label>
									<input type="text" class="form-control" x-model="customerToUpdate.dni" @input.debounce.500ms="fetchNamesUpdate">
									<div x-show="errors?.dni" x-text="errors?.dni" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Apellido paterno · requerido</label>
									<input type="text" class="form-control" x-model="customerToUpdate.ap">
									<div x-show="errors?.ap" x-text="errors?.ap" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Apellido materno · requerido</label>
									<input type="text" class="form-control" x-model="customerToUpdate.am">
									<div x-show="errors?.am" x-text="errors?.am" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Nombres · requerido</label>
									<input type="text" class="form-control" x-model="customerToUpdate.nom">
									<div x-show="errors?.nom" x-text="errors?.nom" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Genero</label>
									<select class="form-control" x-model="customerToUpdate.sexo">
										<option value="" selected disabled>Seleccione</option>
										<option value="M">Masculino</option>
										<option value="F">Femenino</option>
									</select>
									<div x-show="errors?.sexo" x-text="errors?.sexo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Celular</label>
									<input type="tel" class="form-control" x-model="customerToUpdate.cel">
									<div x-show="errors?.cel" x-text="errors?.cel" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Correo</label>
									<input type="email" class="form-control" x-model="customerToUpdate.correo">
									<div x-show="errors?.correo" x-text="errors?.correo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Rubro</label>
									<input type="text" class="form-control" x-model="customerToUpdate.rubro">
									<div x-show="errors?.rubro" x-text="errors?.rubro" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Dirección de negocio</label>
									<input type="text" class="form-control" x-model="customerToUpdate.telefono">
									<div x-show="errors?.telefono" x-text="errors?.telefono" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Número hijos</label>
									<input type="number" class="form-control" x-model="customerToUpdate.n_hijos">
									<div x-show="errors?.n_hijos" x-text="errors?.n_hijos" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Grado Inst.</label>
									<input type="text" class="form-control" x-model="customerToUpdate.grado_inst">
									<div x-show="errors?.grado_inst" x-text="errors?.grado_inst" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Fecha de nacimiento</label>
									<input type="date" class="form-control" x-model="customerToUpdate.fec_nac">
									<div x-show="errors?.fec_nac" x-text="errors?.fec_nac" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Estado civil</label>
									<select class="form-control" x-model="customerToUpdate.estado_civil">
										<option value="" selected>Seleccione</option>
										<option value="S">Soltero</option>
										<option value="C">Casado</option>
										<option value="V">Viudo</option>
										<option value="D">Divorciado</option>
										<option value="Conv">Conviviente</option>
										<option value="Sep">Separado</option>
									</select>
									<div x-show="errors?.estado_civil" x-text="errors?.estado_civil" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Vivienda</label>
									<select class="form-control" x-model="customerToUpdate.tipo">
										<option value="" selected>Seleccione</option>
										<option value="Propia">Propia</option>
										<option value="Familiar">Familiar</option>
										<option value="Alquilada">Alquilada</option>
									</select>
									<div x-show="errors?.tipo" x-text="errors?.tipo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Direccion domiciliaria</label>
									<input type="text" class="form-control" x-model="customerToUpdate.direc">
									<div x-show="errors?.direc" x-text="errors?.direc" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Lugar de nacimiento</label>
									<input type="text" class="form-control" x-model="customerToUpdate.lugar_nac">
									<div x-show="errors?.lugar_nac" x-text="errors?.lugar_nac" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Tipo de cliente</label>
									<select class="form-control" x-model="customerToUpdate.client_type">
										<option value="" selected>Seleccione</option>
										<?php foreach ($dictionaries->where('type', 'CLIENT_TYPE') as $dictionary) { ?>
											<option value="<?php echo $dictionary->id ?>"><?php echo $dictionary->description ?></option>
										<?php } ?>
									</select>
									<div x-show="errors?.client_type" x-text="errors?.client_type" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Perfil de riesgo</label>
									<select class="form-control" x-model="customerToUpdate.risk_profile_id">
										<option value="" selected>Seleccione</option>
										<?php foreach ($dictionaries->where('type', 'RISK_PROFILE') as $dictionary) { ?>
											<option value="<?php echo $dictionary->id ?>"><?php echo textRiskProfile($dictionary->description) ?></option>
										<?php } ?>
									</select>
									<div x-show="errors?.risk_profile_id" x-text="errors?.risk_profile_id" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Usuario</label>
									<select class="form-control" x-model="customerToUpdate.idU">
										<option value="" selected>Seleccione</option>
										<?php foreach ($users as $user) { ?>
											<option value="<?php echo $user->idU ?>">
												<?php echo $user->apU . ' ' . $user->amU . ' ' . $user->nomU ?>
											</option>
										<?php } ?>
									</select>
									<div x-show="errors?.idU" x-text="errors?.idU" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Referencia</label>
									<input type="text" class="form-control" x-model="customerToUpdate.referencia">
									<div x-show="errors?.referencia" x-text="errors?.referencia" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Coordenada latitud</label>
									<input type="text" class="form-control" placeholder="latitude" x-model="customerToUpdate.coordinate_lat">
									<div x-show="errors?.coordinate_lat" x-text="errors?.coordinate_lat" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Coordenada longitud</label>
									<input type="text" class="form-control" placeholder="longitude" x-model="customerToUpdate.coordinate_lng">
									<div x-show="errors?.coordinate_lng" x-text="errors?.coordinate_lng" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Ubigeo</label>
									<div x-data="ubigeo" style="position: relative;">
										<button x-ref="button" style="width: 100%; height: 36px; border-radius: 5px; border: 1px solid #e5e6e7; background: white; text-align: left; padding: 5px 10px;" @click="open = true" x-text="customerToUpdate.ubigeo ? customerToUpdate.ubigeo.full_name : ''"></button>
										<div x-show="open" @click.outside="open = false" style="position: fixed; background: white; width: 250px; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);">
											<div style="display: flex; flex-direction: column; max-height: 200px; overflow: hidden">
												<div>
													<input type="search" class="form-control" x-model.debounce.500ms="search">
												</div>
												<div style="flex: 1 1 auto; overflow: auto;">
													<template x-for="ubigeo in data">
														<button class="item_ubigeo" x-text="ubigeo.full_name" @click="customerToUpdate.ubigeo = ubigeo;search='';data=[],open=false"></button>
													</template>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="margin-top: 20px;">
								<label>Nombre de Establecimiento</label>
								<input type="text" class="form-control" x-model="customerToUpdate.comentario">
								<div x-show="errors?.comentario" x-text="errors?.comentario" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
							</div>
						</div>
						<div x-show="tab_modal_edit === 'general'">
							<div style="padding: 20px 20px;display: flex;">
								<button class="btn btn-warning" @click="obtenerUbicacion">Obtener mi ubicación</button>
								<button class="btn btn-secundary" style="margin-left: auto; margin-right: 10px;" @click="customerToUpdate = null">Cancelar</button>
								<button class="btn btn-primary" @click="onSubmitUpdate">Actualizar cliente</button>
							</div>
						</div>
					</div>
				</div>
			</template>

			<template x-if="modal_create">
				<div @keyup.escape.document="closeModalCreateCustomer" style="position: fixed;inset: 0; z-index: 3000; background: rgba(0, 0, 0, 0.5);padding: 20px;">
					<div style="display: flex; flex-direction: column; border: 1px solid #e7eaec;background: white;max-width: 700px;max-height: 100%; margin: 0 auto;border-radius: 5px;">
						<div style="padding: 10px 20px;display: flex; justify-content: space-between;">
							<h3>Crear cliente</h3>
							<button style="display: flex; justify-content: center; align-items: center;" @click="closeModalCreateCustomer">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
									<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
								</svg>
							</button>
						</div>
						<div style="flex: 1 1 auto; padding: 0 20px; overflow: auto;">
							<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));gap: 20px;">
								<div>
									<label>DNI · requerido</label>
									<input type="text" class="form-control" x-model="factory.dni" @input.debounce.500ms="fetchNames">
									<div x-show="errors?.dni" x-text="errors?.dni" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Apellido paterno · requerido</label>
									<input type="text" class="form-control" x-model="factory.ap">
									<div x-show="errors?.ap" x-text="errors?.ap" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Apellido materno · requerido</label>
									<input type="text" class="form-control" x-model="factory.am">
									<div x-show="errors?.am" x-text="errors?.am" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Nombres · requerido</label>
									<input type="text" class="form-control" x-model="factory.nom">
									<div x-show="errors?.nom" x-text="errors?.nom" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Genero</label>
									<select class="form-control" x-model="factory.sexo">
										<option value="" selected disabled>Seleccione</option>
										<option value="M">Masculino</option>
										<option value="F">Femenino</option>
									</select>
									<div x-show="errors?.sexo" x-text="errors?.sexo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Celular</label>
									<input type="tel" class="form-control" x-model="factory.cel">
									<div x-show="errors?.cel" x-text="errors?.cel" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Correo</label>
									<input type="email" class="form-control" x-model="factory.correo">
									<div x-show="errors?.correo" x-text="errors?.correo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Rubro</label>
									<input type="text" class="form-control" x-model="factory.rubro">
									<div x-show="errors?.rubro" x-text="errors?.rubro" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Dirección de negocio</label>
									<input type="text" class="form-control" x-model="factory.telefono">
									<div x-show="errors?.telefono" x-text="errors?.telefono" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Número hijos</label>
									<input type="number" class="form-control" x-model="factory.n_hijos">
									<div x-show="errors?.n_hijos" x-text="errors?.n_hijos" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Grado Inst.</label>
									<input type="text" class="form-control" x-model="factory.grado_inst">
									<div x-show="errors?.grado_inst" x-text="errors?.grado_inst" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Fecha de nacimiento</label>
									<input type="date" class="form-control" x-model="factory.fec_nac">
									<div x-show="errors?.fec_nac" x-text="errors?.fec_nac" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Estado civil</label>
									<select class="form-control" x-model="factory.estado_civil">
										<option value="" selected>Seleccione</option>
										<option value="S">Soltero</option>
										<option value="C">Casado</option>
										<option value="V">Viudo</option>
										<option value="D">Divorciado</option>
										<option value="Conv">Conviviente</option>
										<option value="Sep">Separado</option>
									</select>
									<div x-show="errors?.estado_civil" x-text="errors?.estado_civil" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Vivienda</label>
									<select class="form-control" x-model="factory.tipo">
										<option value="" selected>Seleccione</option>
										<option value="Propia">Propia</option>
										<option value="Familiar">Familiar</option>
										<option value="Alquilada">Alquilada</option>
									</select>
									<div x-show="errors?.tipo" x-text="errors?.tipo" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Direccion domiciliaria</label>
									<input type="text" class="form-control" x-model="factory.direc">
									<div x-show="errors?.direc" x-text="errors?.direc" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Lugar de nacimiento</label>
									<input type="text" class="form-control" x-model="factory.lugar_nac">
									<div x-show="errors?.lugar_nac" x-text="errors?.lugar_nac" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Tipo de cliente</label>
									<select class="form-control" x-model="factory.client_type">
										<option value="" selected>Seleccione</option>
										<?php foreach ($dictionaries->where('type', 'CLIENT_TYPE') as $dictionary) { ?>
											<option value="<?php echo $dictionary->id ?>"><?php echo $dictionary->description ?></option>
										<?php } ?>
									</select>
									<div x-show="errors?.client_type" x-text="errors?.client_type" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Perfil de riesgo</label>
									<select class="form-control" x-model="factory.risk_profile_id">
										<option value="" selected>Seleccione</option>
										<?php foreach ($dictionaries->where('type', 'RISK_PROFILE') as $dictionary) { ?>
											<option value="<?php echo $dictionary->id ?>"><?php echo textRiskProfile($dictionary->description) ?></option>
										<?php } ?>
									</select>
									<div x-show="errors?.risk_profile_id" x-text="errors?.risk_profile_id" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Usuario</label>
									<select class="form-control" x-model="factory.idU">
										<option value="" selected>Seleccione</option>
										<?php foreach ($users as $user) { ?>
											<option value="<?php echo $user->idU ?>">
												<?php echo $user->apU . ' ' . $user->amU . ' ' . $user->nomU ?>
											</option>
										<?php } ?>
									</select>
									<div x-show="errors?.idU" x-text="errors?.idU" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Referencia</label>
									<input type="text" class="form-control" x-model="factory.referencia">
									<div x-show="errors?.referencia" x-text="errors?.referencia" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Coordenada latitud</label>
									<input type="text" class="form-control" placeholder="latitude" x-model="factory.coordinate_lat">
									<div x-show="errors?.coordinate_lat" x-text="errors?.coordinate_lat" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Coordenada longitud</label>
									<input type="text" class="form-control" placeholder="longitude" x-model="factory.coordinate_lng">
									<div x-show="errors?.coordinate_lng" x-text="errors?.coordinate_lng" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
								</div>
								<div>
									<label>Ubigeo</label>
									<div x-data="ubigeo" style="position: relative;">
										<button x-ref="button" style="width: 100%; height: 36px; border-radius: 5px; border: 1px solid #e5e6e7; background: white; text-align: left; padding: 5px 10px;" @click="open = true" x-text="factory.ubigeo ? factory.ubigeo.full_name : 'Seleccione'"></button>
										<div x-show="open" @click.outside="open = false" style="position: fixed; background: white; width: 250px; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);">
											<div style="display: flex; flex-direction: column; max-height: 200px; overflow: hidden">
												<div>
													<input type="search" class="form-control" x-model.debounce.500ms="search">
												</div>
												<div style="flex: 1 1 auto; overflow: auto;">
													<template x-for="ubigeo in data">
														<button class="item_ubigeo" x-text="ubigeo.full_name" @click="factory.ubigeo = ubigeo;search='';data=[], open =false"></button>
													</template>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="margin-top: 20px;">
								<label>Nombre de Establecimiento</label>
								<input type="text" class="form-control" x-model="factory.comentario">
								<div x-show="errors?.comentario" x-text="errors?.comentario" style="color: #EC7063; font-weight: 600; margin-top: 5px;"></div>
							</div>
						</div>
						<div style="padding: 20px 20px;display: flex;">
							<button class="btn btn-warning" @click="obtenerUbicacion">Obtener mi ubicación</button>
							<button class="btn btn-secundary" style="margin-left: auto; margin-right: 10px;" @click="closeModalCreateCustomer">Cancelar</button>
							<button class="btn btn-primary" @click="onSubmitCreate">Crear cliente</button>
						</div>
					</div>
				</div>
			</template>
		</div>
	</div>
</div>

<script>
	const factoryDefault = {
		dni: '',
		risk_profile_id: '',
		client_type: '',
		direc: '',
		correo: '',
		telefono: '',
		rubro: '',
		cel: '',
		nom: '',
		ap: '',
		am: '',
		fec_nac: '',
		n_hijos: '',
		grado_inst: '',
		estado_civil: '',
		lugar_nac: '',
		referencia: '',
		tipo: '',
		comentario: '',
		distrito: '',
		provincia: '',
		tiempo_residencia: '',
		sexo: '',
		idU: '',
		status: '',
		coordinate_lat: '',
		coordinate_lng: '',
		ubigeo: null
	};

	function formatFactoryCreate(data) {
		return {
			dni: data.dni,
			risk_profile_id: data.risk_profile_id,
			client_type: data.client_type,
			direc: data.direc,
			correo: data.correo,
			telefono: data.telefono,
			rubro: data.rubro,
			cel: data.cel,
			nom: data.nom,
			ap: data.ap,
			am: data.am,
			fec_nac: data.fec_nac,
			n_hijos: data.n_hijos,
			grado_inst: data.grado_inst,
			estado_civil: data.estado_civil,
			lugar_nac: data.lugar_nac,
			referencia: data.referencia,
			tipo: data.tipo,
			comentario: data.comentario,
			tiempo_residencia: data.tiempo_residencia,
			sexo: data.sexo,
			idU: data.idU,
			status: data.status,
			coordinate_lat: data.coordinate_lat,
			coordinate_lng: data.coordinate_lng,
			ubigeo_id: data.ubigeo ? data.ubigeo.id : ''
		}
	}

	function formatDataUpdate(data) {
		return {
			idCG: data.idCG,
			risk_profile_id: data.risk_profile_id,
			client_type: data.client_type,
			dni: data.dni,
			direc: data.direc,
			correo: data.correo,
			telefono: data.telefono,
			rubro: data.rubro,
			cel: data.cel,
			nom: data.nom,
			ap: data.ap,
			am: data.am,
			fec_nac: data.fec_nac,
			n_hijos: data.n_hijos,
			grado_inst: data.grado_inst,
			estado_civil: data.estado_civil,
			lugar_nac: data.lugar_nac,
			referencia: data.referencia,
			tipo: data.tipo,
			comentario: data.comentario,
			distrito: data.distrito,
			provincia: data.provincia,
			tiempo_residencia: data.tiempo_residencia,
			sexo: data.sexo,
			imgC: data.imgC,
			idU: data.idU,
			idO: data.idO,
			status: data.status,
			coordinate_lat: data.coordinate_lat,
			coordinate_lng: data.coordinate_lng,
			risk_profile: data.risk_profile,
			ubigeo_id: data.ubigeo ? data.ubigeo.id : ''
		}
	}

	document.addEventListener('alpine:init', () => {
		Alpine.data('customers', () => ({
				data: <?php echo $customers ?>,
				search: '',
				factory: {
					...factoryDefault
				},
				image: null,
				deletingAttachment: false,
				uploadingImage: false,
				customerToUpdate: null,
				errors: null,
				modal_create: false,
				tab_modal_edit: 'general', // images
				get filteredData() {
					const searchValue = this.search.trim();
					if (!searchValue) {
						return this.data;
					}

					return this.data.filter(i => {
						const sss = searchValue.toLowerCase().split(' ');
						const nombre = i.ap.toLowerCase() + ' ' + i.am.toLowerCase() + ' ' + i.nom.toLowerCase();

						var encontrado = true;
						sss.forEach(value => {
							if (encontrado) {
								encontrado = nombre.includes(value);
							};
						});
						return encontrado || i.dni.startsWith(searchValue);
					});
				},
				onSubmitCreate() {
					this.errors = null;
					fetch(`../app/api/crearCliente.php`, {
							method: 'POST',
							body: JSON.stringify(formatFactoryCreate(this.factory)),
							headers: {
								'Content-Type': 'application/json'
							}
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								this.data = [data.data, ...this.data];
								swal({
									title: 'Cliente registrado con exito!!',
									icon: 'success'
								});
								this.factory = factoryDefault;
								this.modal_create = false
							} else {
								this.errors = data.errors;
							}
						});
				},
				fetchNames() {
					searchDni(this.factory.dni, (data) => {
						this.factory.ap = data.father_first_surname;
						this.factory.am = data.mother_first_surname;
						this.factory.nom = data.name;
					});
				},
				fetchNamesUpdate() {
					searchDni(this.customerToUpdate.dni, (data) => {
						this.customerToUpdate.ap = data.father_first_surname;
						this.customerToUpdate.am = data.mother_first_surname;
						this.customerToUpdate.nom = data.name;
					});
				},
				openModalCustomerUpdate(id) {
					const customer = this.data.find(item => item.idCG == id);

					if (customer) {
						this.customerToUpdate = {
							...customer
						}
					}
				},
				onSubmitUpdate() {

					this.errors = null;
					fetch(`../app/api/actualizarCliente.php`, {
							method: 'POST',
							body: JSON.stringify(formatDataUpdate(this.customerToUpdate)),
							headers: {
								'Content-Type': 'application/json'
							}
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								this.data = this.data.map(item => {
									if (item.idCG == this.customerToUpdate.idCG) {
										return data.data;
									}

									return item;
								});
								this.customerToUpdate = null;
								swal({
									title: 'Cliente actualizado con exito!!',
									icon: 'success'
								});
							} else {
								this.errors = data.errors;
							}
						});
				},
				closeModalCreateCustomer() {
					this.factory = {
							...factoryDefault
						},
						this.errors = null,
						this.modal_create = false
				},
				obtenerUbicacion() {
					navigator.geolocation.getCurrentPosition((ubicacion) => {
						const coordenadas = ubicacion.coords;
						if (this.customerToUpdate) {
							this.customerToUpdate.coordinate_lat = coordenadas.latitude;
							this.customerToUpdate.coordinate_lng = coordenadas.longitude;
						} else if (this.modal_create) {
							this.factory.coordinate_lat = coordenadas.latitude;
							this.factory.coordinate_lng = coordenadas.longitude;
						}
					}, (error) => {
						alert('Lo sentimos, no se pudo obtener tu ubicación.');
					}, {
						enableHighAccuracy: false,
						maximumAge: 0,
						timeout: 5000
					});
				},
				uploadImage(event) {
					if (this.uploadingImage) return;

					const file = event.target.files[0]
					const sizeByte = file.size;

					// el tipo tiene que ser imagen
					if (!file.type.startsWith('image/')) {
						swal({
							title: 'El archivo debe ser una imagen.',
							icon: 'error'
						});
						return;
					}

					// mayor a 2MB
					if (sizeByte > 2097152) {
						event.target.value = null;

						swal({
							title: 'Solo se permite 2MB como maximo en tamaño.',
							text: 'Puedes comprimir la imagen en https://tinypng.com',
							icon: 'info'
						})
						return;
					}

					this.uploadingImage = true

					const formData = new FormData()
					formData.append('file', event.target.files[0])

					fetch(`../app/api/uploadFile.php`, {
							method: 'post',
							body: formData
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								this.image = data.data
							}
						})
						.finally(_ => {
							event.target.value = null
							this.uploadingImage = false
						})
				},
				addAttachmentCustomer(customerId) {
					if (!this.image) return;

					fetch(`../app/api/addAttachmentCustomer.php`, {
							method: 'post',
							body: JSON.stringify({
								customer_id: customerId,
								name: this.image.name,
								extension: this.image.extension,
								size: this.image.size
							}),
							headers: {
								'Content-Type': 'application/json'
							}
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								// const newDataCustomers = this.data.map(item => {
								// 	if (item.idCG == data.customer.idCG) {
								// 		return {...item, attachments: [...item.attachments, data.data]}
								// 	}

								// 	return item;
								// })
								// console.log(newDataCustomers);
								// this.data = [];

								swal({
									title: 'Imagen subido con exito!!',
									icon: 'success'
								});
							} else {
								swal({
									title: 'Lo sentimos, no se pudo subir la imange.',
									icon: 'error'
								});
							}
						})
				},
				deleteAttachment(attachmentId) {
					if (this.deletingAttachment) return;

					this.deletingAttachment = true;

					fetch(`../app/api/deleteCustomerAttachment.php?attachment_id=${attachmentId}`)
						.then(response => resopnse.json())
						.then(data => {
							if (data.success) {
								swal({
									title: 'Imagen eliminado',
									icon: 'success'
								})
							} else {
								swal({
									title: 'Lo sentimos, no se pudo eliminar la imagen',
									icon: 'error'
								})
							}
						})
						.finally(_ => this.deletingAttachment = false);
				}
			})),
			Alpine.data('ubigeo', () => ({
				data: [],
				open: false,
				search: '',
				init() {
					this.$watch('search', (value) => {
						if (value) {
							fetch('./../app/api/queryUbigeo.php?search=' + this.search)
								.then(response => response.json())
								.then(data => this.data = data.data);
						} else {
							this.data = [];
						}
					});
				}
			}))
	});

	function searchDni(dni, callback) {
		fetch(`../app/api/queryDni.php?number=${dni}`)
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					callback(data.data)
				}
			});
	}

	function colorRiskProfile(value) {
		switch (value) {
			case 'red':
				return "#FFAB91";

			case 'yellow':
				return "#FFF59D";

			case 'gray':
				return "#B0BEC5";

			case 'green':
				return "#CCFF90";

			default:
				return 'white'

		}
	}

	function textRiskProfile(value) {
		switch (value) {
			case 'red':
				return "Alto riesgo";

			case 'yellow':
				return "Mediano riesgo";

			case 'gray':
				return "No registra información en el mes";

			case 'green':
				return "Sin riesgo";

			default:
				return 'Ninguno'

		}
	}
</script>
<script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>

<?php
include('footer.php');
?>