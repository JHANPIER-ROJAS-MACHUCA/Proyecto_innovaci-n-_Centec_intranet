<style type="text/css">
	table {
		vertical-align: top;
	}

	tr {
		vertical-align: top;
	}

	td {
		vertical-align: top;
	}

	.midnight-blue {
		background: #F6F6F5;
		padding: 4px 4px 4px;
		color: black;
		font-weight: bold;
		font-size: 11px;
	}

	.silver {
		background: white;
		padding: 3px 4px 3px;
	}

	.clouds {
		background: #ecf0f1;
		padding: 3px 4px 3px;
	}

	.border-top {
		border-top: solid 1px #bdc3c7;

	}

	.border-left {
		border-left: solid 1px #bdc3c7;
	}

	.border-right {
		border-right: solid 1px #bdc3c7;
	}

	.border-bottom {
		border-bottom: solid 1px #bdc3c7;
	}

	table.page_footer {
		width: 100%;
		border: none;
		background-color: white;
		padding: 2mm;
		border-collapse: collapse;
		border: none;
	}
</style>
<page backtop="15mm" backbottom="20m" backleft="20mm" backright="15mm" style="font-size: 9pt; font-family: arial">
	<page_footer>
		<table class="page_footer">
			<tr>
				<td style="width: 33%; text-align: left">
					P&aacute;gina [[page_cu]]/[[page_nb]]
				</td>
				<td style="width: 33%; text-align: center">
					<span>Las oportunidades pequeñas son el principio de las grandes empresas</span>
				</td>
				<td style="width: 33%; text-align: right">
					&copy; <?php echo " ";
							echo  $anio = date('Y'); ?>
				</td>
			</tr>
		</table>
	</page_footer>
	<?php include("encabezado_historial.php"); ?>
	<br>
	<?php $sql_cliente = mysqli_query($con, "select * from tclie_general where idCG='$id_cliente'");
	$rw_cliente = mysqli_fetch_array($sql_cliente); ?>
	<table align="center" cellspacing="0" style="width: 90%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:30%;" class='midnight-blue'>Cliente: <?php
																	echo $rw_cliente['ap'];
																	echo " ";
																	echo $rw_cliente['am'];
																	echo " ";
																	echo $rw_cliente['nom'];
																	?></td>
			<td align="center" style="width:30%;">Taza: <?php echo $taza . " %"; ?></td>
			<td style="width:30%;" class='midnight-blue'>Fecha de Desembolso: <?php $fec12 = preg_split("~-~", $fechaD);
																				$fechaD = "$fec12[2] / $fec12[1] / $fec12[0]";
																				echo $fechaD; ?>

			</td>
		</tr>
		<tr>
			<td style="width:30%;" class='midnight-blue'>Nº de Credito: <?php echo "CTCP0" . $idPrest; ?></td>
			<td align="center" style="width:30%;">Tipo: <?php
														switch ($tipo) {
															case 1:
																$ti = "TRANSPORTE";
																break;
															case 2:
																$ti = "COMERCIO";
																break;
															case 3:
																$ti = "PRENDATARIO";
																break;
														}
														echo $ti; ?> </td>
			<td style="width:30%;" class='midnight-blue'>Capital Desembolsado: <?php echo $S . $montoA; ?>

			</td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" style="width: 98%; text-align: left; font-size: 10pt;">
		<tr>
			<th style="width: 14%;text-align:left" class='midnight-blue'>Nº Cuota</th>
			<th style="width: 14%;text-align:center" class='midnight-blue'>Estado Cuota</th>
			<th style="width: 14%;text-align:left" class='midnight-blue'>Fecha Prog.</th>
			<th style="width: 14%;text-align:center" class='midnight-blue'>Fecha de pago</th>
			<th style="width: 14%;text-align:right" class='midnight-blue'>Cuota Progr.</th>
			<th style="width: 14%;text-align:right;" class='midnight-blue'>Cuota Pagado</th>
			<th style="width: 14%;text-align:right" class='midnight-blue'>Pago Mora</th>

		</tr>
		<?php
		$nums = 1;
		//$sql=mysqli_query($con,"select * from tprestamo p,tpresta_detalle dp where p.idP=dp.idP and p.idP='".$idprestamo."'");
		$sql = mysqli_query($con, "select * from tpresta_detalle where idP='" . $idprestamo . "'");
		while ($row = mysqli_fetch_array($sql)) {
			$ncuota = $row['ncuota'];
			$fechap = $row['fechaProg'];
			$fechapag = $row['fechaPago'];
			$cuota = $row['cuota'];
			$montoPag = $row['montoPagado'];
			$montoPagf += $montoPag;
			$cuotaf += $cuota;
			$cuota_f = number_format($cuota, 2); //Formateo variables
			if (!empty($row['montoPagado'])) {
				$monto = $row['montoPagado'];
				$S2 = "S/. ";
			} else {
				$monto = "";
				$S2 = "";
			}
			$monto_p = number_format($monto, 2); //Formateo variables
			if (!empty($row['pagoMora'])) {
				$mora = $row['pagoMora'];
			} else {
				$mora = "";
			}
			$mora_f = number_format($mora, 2); //Formateo variables
			$mora_f2 += $mora_f;
			$saldo = $row['saldo'];
			$saldo_f = number_format($saldo, 2); //Precio total formateado
			if ($nums % 2 == 0) {
				$clase = "clouds";
			} else {
				$clase = "silver";
			}
			$fec1 = preg_split("~-~", $fechap);
			$fechap = "$fec1[2] / $fec1[1] / $fec1[0]";
		?>
			<tr>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: center"><?php echo $ncuota; ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: center"><?php if ($cuota == $monto) {
																											echo "PAGADO";
																										} else if ($monto == "" or $monto == null) {
																											echo "NO PAGADO";
																										} else {
																											echo "DEUDA";
																										} ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: left"><?php echo $fechap; ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: center"><?php echo $fechapag; ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: right"><?php echo $S . " " . $cuota_f; ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: right"><?php echo $S2 . "" . $monto_p; ?></td>
				<td class='<?php echo $clase; ?>' style="width: 14%;font-size:9px; text-align: right">
					<?php
					if (!empty($row['tfechaMora'])) {
						echo "CANCELADO";
					}
					?>
					<?php echo $mora_f; ?>
				</td>

			</tr>
		<?php
			$nums++;
		}
		$mora_f2 = number_format($mora_f2, 2);
		$montoPagf = number_format($montoPagf, 2);
		$cuotaf = number_format($cuotaf, 2);
		?>
	</table>
	<br>
	<table cellspacing="0" style="width: 98%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:14%;" class='midnight-blue'></td>
			<td style="width:14%;" class='midnight-blue'></td>
			<td style="width:14%;" class='midnight-blue'></td>
			<td style="width:14%;" class='midnight-blue'></td>
			<td style="width:14%;" class='midnight-blue'>Total Cuota Prog. <?php echo "S/. $cuotaf"; ?></td>
			<td style="width:14%;text-align: right;" class='midnight-blue'>Total Cuota Pag. <?php echo "S/. $montoPagf"; ?></td>
			<td style="width:14%;text-align: right;" class='midnight-blue'>Total Mora: <?php echo "S/. $mora_f2"; ?></td>
		</tr>
		<tr>

			<td></td>
			<td colspan="5" style="width: 14%; text-align: right;">SUBTOTAL <?php echo $S; ?> </td>
			<td style="width: 14%; text-align: right;"> <?php echo number_format($cuotaf, 2); ?></td>
		</tr>
		<tr>

			<td></td>
			<td colspan="5" style="width: 14%; text-align: right;">MORA <?php echo $S; ?> </td>
			<td style="width: 14%; text-align: right;"> <?php echo number_format($moraf2, 2); ?></td>
		</tr>
		<tr>

			<td></td>
			<td colspan="5" style="width: 14%; text-align: right;">TOTAL <?php echo $S; ?> </td>
			<td style="width: 14%; text-align: right;"> <?php echo number_format($cuotaf + $moraf2, 2); ?></td>
		</tr>
	</table>
</page>