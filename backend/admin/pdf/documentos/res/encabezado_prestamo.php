<?php
if ($con) {
?>
	<table cellspacing="0" style="table-layout:fixed; width: 100%;">
		<tr>
			<td style="width: 25%; color: #444444;">
				<img style="height: 50px;" src="../../titulo/img/<?php echo $jua1['logo']; ?>"><br>
			</td>
			<td style="width: 50%; color: #34495e;font-size:8px;text-align:center">
				<span style="color: #34495e;font-size:10px;font-weight:bold"><?php echo $jua1['nombreEmpresa']; ?></span>
				<br><?php echo get_row('toficina', 'direccion', 'idO', $idOfi); ?> <br>
				Teléfono: <?php echo get_row('toficina', 'telefono', 'idO', $idOfi); ?><br>
				Email: <?php echo get_row('toficina', 'correo', 'idO', $idOfi); ?>
			</td>
			<td style="width: 25%;text-align: right;">
				<table>
					<tbody>
						<tr>
							<td>Cod. crédito:</td>
							<td><?php echo $codigo_credito ?></td>
						</tr>
						<tr>
							<td>Nº credito:</td>
							<td><?php echo $ncredito; ?></td>
						</tr>
						<tr>
							<td>Tipo:</td>
							<td>
								<?php
								switch ($tipo) {
									case '1':
										echo 'Diario';
										break;
									case '2':
										echo 'Semanal';
										break;
									case '3':
										echo 'Pago unico';
										break;
									case '4':
										echo 'Mensual';
										break;
									case '4':
										echo 'Quincenal';
										break;
								}
								?>
							</td>
						</tr>
						<tr>
							<td>Periodo:</td>
							<td><?php echo $plazo; ?></td>
						</tr>
					</tbody>
				</table>
				
			</td>
		</tr>
	</table>
<?php } ?>