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
		background: #D7DFDC;
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
<page backtop="15mm" backbottom="20mm" backleft="20mm" backright="15mm" style="font-size: 11pt; font-family: Arial">
	<page_footer>
		<table class="page_footer">
			<tr>

				<td style="width: 100%; text-align: right">
					P&aacute;gina [[page_cu]]/[[page_nb]]
				</td>
			</tr>
		</table>
	</page_footer>
	<?php include("encabezado_pagare.php"); ?>
	<br>
	<?php
	$sql_cliente = mysqli_query($con, "select * from tclie_general where idCG='$id_cliente'");
	$rw_cliente = mysqli_fetch_array($sql_cliente);
	$datosC = $rw_cliente['ap'] . " " . $rw_cliente['am'] . " " . $rw_cliente['nom'];
	$nomC = $rw_cliente['nom'];
	$apeC = $rw_cliente['ap'] . " " . $rw_cliente['am'];
	$dniC = $rw_cliente['dni'];
	$direccionC = strtoupper($rw_cliente['direc']);
	$direcC = $rw_cliente['direc'];
	$sql_clienteD = mysqli_query($con, "select *,min(td.idCD) from tclie_direccion td,ta_dis d,ta_prov p,ta_depa de where td.idCG='$id_cliente' and td.iddis=d.iddis and d.idprov=p.idprov and p.iddepa=de.iddepa");
	$rw_clienteD = mysqli_fetch_array($sql_clienteD);
	$distrito = strtoupper($rw_clienteD['distri']);
	$provincia = strtoupper($rw_clienteD['provi']);
	$departamento = strtoupper($rw_clienteD['depa']);

	$sql_clienteV = mysqli_query($con, "SELECT * FROM tprestamo p,tvinculacion v where p.idP=$idPrest and p.idV=v.idV");
	$rw_clienteV = mysqli_fetch_array($sql_clienteV);
	$conyugue = $rw_clienteV['conyugue'];
	$aval = $rw_clienteV['aval'];
	$sql_clienteVC = mysqli_query($con, "select * from tclie_general where idCG='$conyugue'");
	$rw_clienteVC = mysqli_fetch_array($sql_clienteVC);
	$nomVC = $rw_clienteVC['nom'];
	$apeVC = $rw_clienteVC['ap'] . " " . $rw_clienteVC['am'];
	$dniVC = $rw_clienteVC['dni'];
	$direcVC = $rw_clienteVC['direc'];
	$sql_clienteVA = mysqli_query($con, "select * from tclie_general where idCG='$aval'");
	$rw_clienteVA = mysqli_fetch_array($sql_clienteVA);
	$nomVA = $rw_clienteVA['nom'];
	$apeVA = $rw_clienteVA['ap'] . " " . $rw_clienteVA['am'];
	$dniVA = $rw_clienteVA['dni'];
	$direcVA = $rw_clienteVA['direc'];
	$nVA = $rw_clienteVA['cel'];
	//===============fin del prestamo VENCIMIENTO
	$venida = mysqli_query($con, "select fechaProg as dato from tpresta_detalle where idP='$idPrest' order by idPD desc limit 1");
	$fula = mysqli_fetch_array($venida);
	?>
	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 11pt;">
		<tr align="center">
			<td style="width:50%;" class='midnight-blue'>IMPORTE ORIGINAL</td>
			<td style="width:50%;" class='midnight-blue'>IMPORTE DEUDOR</td>
		</tr>
		<tr align="center">
			<td style="width:50%;"><?php echo $S . $montoA; ?></td>
			<?php $importeD = $montoA + (($montoA * $taza) / 100); ?>
			<td style="width:50%;"><?php echo $S . $importeD; ?></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 11pt;">
		<tr>
			<td align="center" style="width:50%;" class='midnight-blue'>FECHA DE INICIO DE LA DEUDA</td>
			<td align="center" style="width:50%;" class='midnight-blue'>FECHA DE VENCIMIENTO DE LA DEUDA</td>

		</tr>
		<tr>
			<td align="center" style="width:50%;"><?php echo "$fechaD"; ?></td>
			<td align="center" style="width:50%;"><?php echo $fula['dato']; ?></td>
		</tr>
	</table>
	<br>
	
	<div style="text-align: justify;">YO (NOSOTROS),<b><?php echo $datosC; ?></b>, IDENTIFICADO CON D.N.I. Nº <?php echo $dniC; ?>, CON DOMICILIO REAL UBICADO EN,<?php echo $direccionC; ?>, DEL DISTRITO<?php echo $distrito; ?>, PROVINCIA<u><?php echo $provincia; ?></u>, DEPARTAMENTO<u><?php echo $departamento; ?></u>, RECONOZCO (RECONOCEMOS) QUE ADEUDO (ADEUDAMOS) Y PAGARÉ (PAGAREMOS) INCONDICIONALMENTE EN LA FECHA DE VENCIMIENTO CONSIGNADO EN EL PRESENTE PAGARÉ, A LA ORDEN DE EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?>, EN ADELANTE <?php echo $jua1['abre'] . " " . $jua1['siglas'] ?>, O A QUIEN ÉSTA SE LO HUBIERA CEDIDO, EN SU DOMICILIO SOCIAL O DONDE SE PRESENTARE PARA SU COBRO; EL IMPORTE DE <?php echo $montoA; ?> NUEVOS SOLES (<?php echo $S . " " . $montoA; ?>), SIN LUGAR A RECLAMO DE CLASE ALGUNA, PARA CUYO FIEL Y EXACTO CUMPLIMIENTO, ME OBLIGO CON TODOS MIS BIENES PRESENTE Y FUTUROS EN LA MEJOR FORMA DE DERECHO. AL EFECTO, ASUMO LA OBLIGACIÓN EN LAS SIGUIENTES CONDICIONES:</div>
	<h4 style="width: 100%; text-align: justify; font-size: 11pt;"><b><u>CONDICIONES GENERALES DEL TITULO</u></b></h4>

	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>PRIMERA:</b>ESTE PAGARÉ SERÁ PAGADO SÓLO EN LA MISMA MONEDA QUE EXPRESA ESTE TÍTULO VALOR.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>SEGUNDA:</b> A SU VENCIMIENTO, PODRÁ SER PRORROGADO POR EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?>, O POR SU TENEDOR, POR EL PLAZO QUE ÉSTE SEÑALE EN ESTE MISMO DOCUMENTO, SIN QUE SEA NECESARIO INTERVENCIÓN ALGUNA DEL OBLIGADO PRINCIPAL NI DE LOS AVALISTAS SOLIDARIOS.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>TERCERA:</b>EL IMPORTE DE ESTE PAGARÉ, Y /O DE LAS CUOTAS DEL CRÉDITO QUE REPRESENTA, GENERARÁN DESDE LA FECHA DE EMISIÓN HASTA LA FECHA DE SU RESPECTIVO(S) VENCIMIENTO(S), UN INTERÉS COMPENSATORIO QUE SE PACTA EN LA TASA DE <?php echo $taza; ?>% MENSUAL.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>CUARTA:</b>EN CASO DE INCUMPLIMIENTO EN EL PAGO DE UNA O MÁS CUOTAS PACTADAS, AL IMPORTE DEUDOR SE LE APLICARÁN LOS INTERESES COMPENSATORIOS E INTERESES MORATORIOS A LAS TASAS MÁXIMAS APROBADAS POR EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?> DESDE LA FECHA DE VENCIMIENTO HASTA SU TOTAL CANCELACIÓN, SIN QUE SEA NECESARIO EFECTUAR REQUERIMIENTO PREVIO DE PAGO PARA CONSTITUIR EN MORA AL OBLIGADO PRINCIPAL NI A LOS AVALISTAS SOLIDARIOS, INCURRIÉNDOSE EN ÉSTA AUTOMÁTICAMENTE POR EL SOLO HECHO DEL VENCIMIENTO.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>QUINTA:</b>EL CLIENTE Y SU CÓNYUGE OBLIGADOS PRINCIPALES Y LOS DEUDORES SOLIDARIOS ACEPTAN IGUALMENTE QUE LAS TASAS DE INTERÉS COMPENSATORIO Y/O MORATORIO PUEDAN SER VARIADAS POR EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?> O SU TENEDOR SIN NECESIDAD DE AVISO PREVIO, DE ACUERDO A LAS TASAS QUE ÉSTA TENGA VIGENTES. </p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>SEXTA:</b>LOS OBLIGADOS PRINCIPALES Y SOLIDARIOS SUSCRIBIENTES DEL PRESENTE PAGARÉ DEJAN CONSTANCIA QUE ESTE DOCUMENTO NO REQUIERE EL PROTESTO POR FALTA DE PAGO, PROCEDIENDO SU EJECUCIÓN POR EL SÓLO MÉRITO DE HABER VENCIDO SU PLAZO Y NO HABER SIDO PRORROGADO; SALVO EL PROTESTO DE LA CUOTA IMPAGA SI SE OPTA POR LA EL VENCIMIENTO ACELERADO DISPUESTO POR EL ART. 1323 DEL CÓDIGO CIVIL, Y/O LA PRECLUSIÓN DE LOS PLAZOS.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>SÉPTIMA:</b> EL IMPORTE DE ESTE PAGARÉ PODRÁ SER PACTADO EN UNA O MAS CUOTAS, SEGÚN EL/LOS IMPORTE(S) Y VENCIMIENTO QUE INDIQUE EL CORRESPONDIENTE CRONOGRAMA DE PAGOS, QUE NO REQUERIRÁ DE SUSCRIPCIÓN ADICIONAL AL PRESENTE DOCUMENTO.</p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>OCTAVA:</b>SERÁN DE CARGO DE LOS OBLIGADOS PRINCIPALES Y LOS SOLIDARIOS, EL PAGO ÍNTEGRO DE LOS TRIBUTOS Y GASTOS QUE AFECTEN A ÉSTE PAGARÉ O A LA OBLIGACIÓN EN ÉL CONTENIDA, LOS MISMOS QUE SERÁN CALCULADOS Y DETERMINADOS POR EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?> O SU TENEDOR EN LA OPORTUNIDAD EN QUE ELLO SE VERIFIQUE. </p>
	<p style="width: 100%; text-align: justify; font-size: 11pt;"><b>NOVENA:</b>EL O LOS OBLIGADO (S) PRINCIPAL (ES) Y LOS AVALISTAS SOLIDARIOS AUTORIZAN EXPRESAMENTE A EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?> A CARGAR DIRECTAMENTE EN SUS CUENTAS (SEA EN MONEDA NACIONAL Y/O EXTRANJERA) QUE MANTENGAN EN ELLA, EL O LAS CUOTAS DEL CRÉDITO QUE REPRESENTA EL PAGARÉ, ASÍ COMO A COMPENSARLOS CON CUALQUIER OTRO TIPO DE BIEN QUE PUDIERA TENER EN SU PODER, SIN QUE ELLO OBLIGUE O SIGNIFIQUE RESPONSABILIDAD PARA EL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?></p>
	<?php
	$fechaD = strtotime($fechaD);
	$anioD = date("Y", $fechaD);
	$mesD = date("m", $fechaD);
	$diaD = date("d", $fechaD);
	switch ($mesD) {
		case '1':
			$mesD2 = "enero";
			break;
		case '2':
			$mesD2 = "febrero";
			break;
		case '3':
			$mesD2 = "marzo";
			break;
		case '4':
			$mesD2 = "abril";
			break;
		case '5':
			$mesD2 = "mayo";
			break;
		case '6':
			$mesD2 = "junio";
			break;
		case '7':
			$mesD2 = "julio";
			break;
		case '8':
			$mesD2 = "agosto";
			break;
		case '9':
			$mesD2 = "setiembre";
			break;
		case '10':
			$mesD2 = "octubre";
			break;
		case '11':
			$mesD2 = "noviembre";
			break;
		case '12':
			$mesD2 = "diciembre";
			break;
	}

	?>

	<p style="width: 100%; text-align: right; font-size: 11pt;"><b>Satipo, <?php echo $diaD; ?> de <?php echo $mesD2; ?> de <?php echo $anioD; ?> </b></p>

	<br><br><br><br><br><br>
	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 11pt;">
		<tr>
			<td align="center" style="width:50%;"><b>........................................</b></td>
			<td align="center" style="width:50%;"><b>........................................</b></td>

		</tr>
		<tr>
			<td align="center" style="width:50%;"><b>TITULAR</b></td>
			<td align="center" style="width:50%;"><b>CONYUGE</b></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
		<tr>
			<td align="left" style="width:50%;">Nombres: <?php echo "$nomC"; ?></td>
			<td align="left" style="width:50%;">Nombres: <?php echo "$nomVC"; ?></td>

		</tr>
		<tr>
			<td align="left" style="width:50%;">Apellidos: <?php echo "$apeC"; ?></td>
			<td align="left" style="width:50%;">Apellidos: <?php echo "$apeVC"; ?></td>

		</tr>
		<tr>
			<td align="left" style="width:50%;">D.N.I: <?php echo "$dniC"; ?></td>
			<td align="left" style="width:50%;">D.N.I <?php echo "$dniVC"; ?></td>

		</tr>
		<tr>
			<td align="left" style="width:50%;">Domicilio: <?php echo "$direcC"; ?></td>
			<td align="left" style="width:50%;">Domicilio: <?php echo "$direcVC"; ?></td>

		</tr>
	</table>
</page>

<page backtop="15mm" backbottom="20mm" backleft="20mm" backright="15mm" style="font-size: 11pt; font-family: Arial">
	<page_footer>
		<table class="page_footer">
			<tr>

				<td style="width: 100%; text-align: right">
					P&aacute;gina [[page_cu]]/[[page_nb]]
				</td>
			</tr>
		</table>
	</page_footer>
	<h3 align="center">Gerente General</h3>
	<h4 align="center"><u>AVALISTAS</u> </h4>
	<p style="width: 100%; text-align: justify; font-size: 11pt;">NOSOTROS, LOS ABAJO FIRMANTES, NOS CONSTITUIMOS EN AVALISTAS PERMANENTES Y EN GARANTES SOLIDARIOS DE LOS OBLIGADOS PRINCIPALES Y ENTRE NOSOTROS MISMOS, PARA LO CUAL COMPROMETEMOS NUESTRO PATRIMONIO AL <?php echo $jua1['nombreEmpresa'] . " " . $jua1['siglas'] ?> EN GARANTÍA DEL PAGO DE LAS OBLIGACIONES CONTENIDAS EN EL PRESENTE PAGARÉ, OBLIGÁNDONOS POR LA CANTIDAD ADEUDADA Y ACEPTANDO SIN LIMITACIONES NI RESTRICCIONES TODAS Y CADA UNA DE LAS CLÁUSULAS ESPECIALES QUE FIGURAN EN EL PRESENTE PAGARÉ.</p>
	<p style="width: 100%; text-align: right; font-size: 11pt;"><b>Satipo, <?php echo $diaD; ?> de <?php echo $mesD2; ?> de <?php echo $anioD; ?> </b></p>

	<br><br><br><br><br><br>
	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 11pt;">
		<tr>
			<td align="center" style="width:100%;"><b>........................................</b></td>


		</tr>
		<tr>
			<td align="center" style="width:100%;"><b>AVALISTA</b></td>

		</tr>
	</table>
	<br>
	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 11pt;">
		<tr>
			<td align="left" style="width:100%;">Nombres: <?php echo "$nomVA"; ?></td>


		</tr>
		<tr>
			<td align="left" style="width:100%;">Apellidos: <?php echo "$apeVA"; ?></td>


		</tr>
		<tr>
			<td align="left" style="width:100%;">Cliente Nº: <?php echo "$nVA"; ?></td>


		</tr>
		<tr>
			<td align="left" style="width:100%;">D.N.I. o L.E.: <?php echo "$dniVA"; ?></td>


		</tr>
		<tr>
			<td align="left" style="width:100%;">Domicilio: <?php echo "$direcVA"; ?></td>


		</tr>

	</table>
</page>