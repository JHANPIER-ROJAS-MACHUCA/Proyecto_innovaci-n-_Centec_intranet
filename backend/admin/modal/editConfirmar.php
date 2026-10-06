<div id="confirmarPrestamo" class="modal fade">
	<div class="modal-dialog">

		<form name="edit_prestamo" id="edit_prestamo">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4 class="modal-title">Confirmar Prestamo</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Monto Propuesto</label>
						<input readonly type="text" name="edit_montop" id="edit_montop" class="form-control" required>
						<input readonly type="hidden" name="edit_id" id="edit_id" required>
					</div>
					<div class="form-group">
						<label>Monto Aprobado</label>
						<input type="number" name="edit_montoa" id="edit_montop2" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="">Tasa</label>
						<div class="input-group">
							<input type="number" step="0.01" class="form-control" required placeholder="%" id="edit_taza" name="edit_taza">
							<span class="input-group-addon">%</span>
						</div>

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

	.modal .modal-header,
	.modal .modal-body,
	.modal .modal-footer {
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