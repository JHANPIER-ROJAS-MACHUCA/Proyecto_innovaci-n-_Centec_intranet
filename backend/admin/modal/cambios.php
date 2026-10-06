<div id="cambiosPrestamos" class="modal fade">
		<div class="modal-dialog">
			<form name="cambiosSarpado" id="cambiosSarpado">
				 <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="modal-title">Cambios de Prestamo</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
             <div class="panel-body">
							<div class="form-group">
                   <label class="">Taza(*)</label>
                   <div class="input-group">
                     <input type="number" min="0" step="1" onkeyup="monti()" onkeypress="return numero(event)" class="form-control" required placeholder="%" id="txtporcenta"  name="txtporcenta">
										 <input type="hidden" min="0" class="form-control" required  id="codi"  name="codi">
										 <input type="hidden" class="form-control" id="txtcuota" name="txtcuota" value="">
                     <span class="input-group-addon">%</span>
                   </div>
              </div>
							<div class="form-group">
								<label for="">Fecha del Desembolso (*)</label>
								<input type="date" min="0" class="form-control" required  id="txtfechaDesembolso"  name="txtfechaDesembolso">
							</div>
							<div class="form-group">
								<label for="">Monto Aprobado(*)</label>
								<input type="text" id="txtmonto" onkeyup="monti()" name="txtmonto" onkeypress="return numero(event)" class="form-control" value="">
							</div>
              <div class="form-group">
								<label>Tipo de Pago(*)</label>
								<select class="form-control" id="lstpago" onchange="monti()"  name="lstpago">
									<option value="" disabled selected>- -Seleccione- -</option>
									<option value="1">Diario</option>
									<option value="2">Semanal</option>
									<option value="3">Pago Unico</option>
									<option value="4">Mensual</option>
								</select>
							</div>
							<div class="form-group" id="fecha" style="display:none">
								<label for="">Fecha de Pago (*)</label>
								<input class="form-control" type="date" id="txtfechaPago" name="txtfechaPago" value="">
							</div>
							<div class="form-group">
								<label>Plazo (*)</label>
								<input type="number" style="text-align:right" onkeyup="monti()" onkeypress="return numero(event)" name="txtplazom" id="txtplazom" class="form-control" required>
							</div>

               </div>
               <div align="right" class="panel-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
							  <input type="submit" class="btn btn-info" value="Confirmar">
               </div>
   				</div>
				</form>
		</div>
	</div>
<style type="text/css">
		/* Modal styles */
	.modal .modal-dialog {
		max-width: 400px;
	}
	.modal .modal-header, .modal .modal-body, .modal .modal-footer {
		padding: 20px 30px;
	}
	.modal .modal-content {
		border-radius: 3px;
	}
	.modal .modal-footer {
		background: #ecf0f1;
		border-radius: 0 0 3px 3px;
	}
    .modal .modal-title {
        display: inline-block;
    }
	.modal .form-control {
		border-radius: 2px;
		box-shadow: none;
		border-color: #dddddd;
	}
	.modal textarea.form-control {
		resize: vertical;
	}
	.modal .btn {
		border-radius: 2px;
		min-width: 100px;
	}
	.modal form label {
		font-weight: normal;
	}
</style>
