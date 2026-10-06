<style type="text/css">

table { vertical-align: top; }
tr    { vertical-align: top; }
td    { vertical-align: top; }
.midnight-blue{
	background:#D7DFDC;
	padding: 4px 4px 4px;
	color:black;
	font-weight:bold;
	font-size:10px;
}
.silver{
	background:white;
	padding: 3px 4px 3px;
}
.clouds{
	background:#ecf0f1;
	padding: 3px 4px 3px;
}
.border-top{
	border-top: solid 1px #bdc3c7;

}
.border-left{
	border-left: solid 1px #bdc3c7;
}
.border-right{
	border-right: solid 1px #bdc3c7;
}
.border-bottom{
	border-bottom: solid 1px #bdc3c7;
}
table.page_footer {width: 100%; border: none; background-color: white; padding: 2mm;border-collapse:collapse; border: none;}

</style>
<page backtop="15mm" backbottom="20mm" backleft="20mm" backright="15mm" style="font-size: 10pt; font-family: Arial" >
        <page_footer>
        <table class="page_footer">
            <tr>

                 <td style="width: 100%; text-align: right">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
	<?php include("encabezado_contrato.php");?>
    	<?php
				$sql_cliente=mysqli_query($con,"select * from tclie_general where idCG='$id_cliente'");
				$rw_cliente=mysqli_fetch_array($sql_cliente);
				$datosC=$rw_cliente['ap']." ".$rw_cliente['am']." ".$rw_cliente['nom'];
				$nomC=$rw_cliente['nom'];
				$apeC=$rw_cliente['ap']." ".$rw_cliente['am'];
				$dniC=$rw_cliente['dni'];
				$direccionC=strtoupper($rw_cliente['direc']);
					$direcC=$rw_cliente['direc'];
			?>
      <?php
$fechaD = strtotime($fechaD);
$anioD = date("Y",$fechaD);
$mesD = date("m",$fechaD);
$diaD = date("d",$fechaD);
switch ($mesD) {
  case '1':
    $mesD2="enero";
    break;
  case '2':
    $mesD2="febrero";
    break;
  case '3':
    $mesD2="marzo";
    break;
  case '4':
    $mesD2="abril";
    break;
  case '5':
    $mesD2="mayo";
    break;
  case '6':
    $mesD2="junio";
    break;
  case '7':
    $mesD2="julio";
    break;
  case '8':
    $mesD2="agosto";
    break;
  case '9':
    $mesD2="setiembre";
    break;
  case '10':
    $mesD2="octubre";
    break;
  case '11':
    $mesD2="noviembre";
    break;
  case '12':
    $mesD2="diciembre";
    break;
}
switch ($pago) {
  case '1':
    $pago1="diario";
    break;

  case '2':
    $pago1="semanal";
    break;
  case '3':
    $pago1="pago unico";
    break;
  case '4':
    $pago1="mensual";
    break;
}
 ?>
  <h5 style="width: 100%; text-align: center; font-size: 10pt;"><b><u>CONTRATO PRIVADO DE PRÉSTAMO</u></b></h5>
  <p style="width: 100%; text-align: justify; font-size: 10pt;">Conste por el presente documento el contrato de préstamo que celebran de una parte, el <B><?php echo $jua1['nombreEmpresa']." ".$jua1['siglas']; ?></B>, con RUC <b><?php echo $jua1['ruc'] ?></b>, con domicilio <b><?php echo $jua1['direccion'];?></b>, Junín, debidamente representada por su <b>Gerente General, <?php echo $jua1['representante']; ?></b>, identificada con D.N.I.: <b><?php echo $jua1['dnir'];?></b>, con poderes inscritos en la partida registral Nº <b><?php echo $jua1['partida'];?></b>, de los Registros Públicos de Satipo - Junín, a quien en adelante se denominara <b><?php echo $jua1['nombreEmpresa'] ?></b>y de la otra parte el Sr./Sra <b><?php echo"$datosC";?></b>, identificado/a con DNI N°<b><?php echo "$dniC"; ?></b>, a quien en adelante se le denominará PRESTATARIO, en los términos contenidos en las cláusulas siguientes:</p>
	<h4 style="width: 100%; text-align: left; font-size: 10pt;"><b><u>ANTECEDENTES</u></b></h4>
  <p style="width: 100%; text-align: justify; font-size: 10pt;">PRIMERO.-<b><?php echo $jua1['nombreEmpresa'] ?></b>, es una institución privada, que apoya el desarrollo de las Pymes a través de la Asesoría de sus proyectos y del financiamiento para sus negocios. <br>
  SEGUNDO.- El PRESTATARIO es una persona natural, comerciante empresaria de esta ciudad.</p>
  <h4 style="width: 100%; text-align: left; font-size: 10pt;"><b><u>OBJETO DEL CONTRATO</u></b></h4>
  <p style="width: 100%; text-align: justify; font-size: 10pt;">TERCERO.- El presente convenio tiene como objetivo establecer un vinculo de préstamo entre <b><?php echo $jua1['nombreEmpresa'] ?></b> y el PRESTATARIO a fin de apoyar su labor empresarial.</p>
 <h4 style="width: 100%; text-align: left; font-size: 10pt;"><b><u>OBLIGACIONES DE LAS PARTES</u></b></h4>
  <p style="width: 100%; text-align: justify; font-size: 10pt;">CUARTO.- Por el presente contrato el PRESTATARIO se compromete a devolver el capital y pagar como interés compensatorio, el equivalente a la tasa promedio del sistema financiero para créditos a la microempresa, vigente a la fecha del presente contrato. <br>QUINTO.- Adicionalmente el PRESTATARIO pagará los gatos administrativos, el costo de evaluación de crédito, la búsqueda en central de riesgo y la gestión <?php echo "$pago1"; ?> de cobranza. <br>SEXTO.- Dicho crédito será amortizado en forma <?php echo "$pago1"; ?>, pagándose primero el importe del capital y en la última o últimas cuotas el importe de los intereses y gastos administrativos, por lo que el <b><?php echo $jua1['nombreEmpresa']." ".$jua1['siglas']; ?></b>, en ese momento se emitirá la boleta de venta y/o factura, con la cancelación del Total de Crédito. <br>SEPTIMO.- En todo lo no previsto por las partes en el presente convenio, ambas se someten a lo establecido por las normas del Código Civil y demás del sistema jurídico que resulten aplicables. <br>En señal de conformidad las partes suscriben este documento en la ciudad de Satipo a los <?php echo "$diaD ";?>días del mes <?php echo "$mesD2";?> de <?php echo "$anioD"; ?>.
</p>
  <br><br><br><br>
        	<table cellspacing="0" style="width: 100%; text-align: center; font-size: 10pt;">
        <tr>
           <td align="center" style="width:50%;"><b>...............................................................................</b></td>
            <td align="center" style="width:50%;"><b>........................................</b></td>
        </tr>
		<tr>
          <td align="center" style="width:50%;"><b><?php echo $jua1['nombreEmpresa'] ?></b></td>
           <td  align="center" style="width:50%;"><b>EL PRESTATARIO</b></td>
      </tr>
    </table>
</page>
