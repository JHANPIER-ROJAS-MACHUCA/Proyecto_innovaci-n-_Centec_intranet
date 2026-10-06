 <table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
        <tr>
           <td style="width:50%;" class='midnight-blue'>PRESTAMO DE</td>
        </tr>
		<tr>
           <td style="width:50%;" >
			<?php
				$sql_cliente=mysqli_query($con,"select * from tclie_general where idCG='$id_cliente'");
				$rw_cliente=mysqli_fetch_array($sql_cliente);
			echo $rw_cliente['ap'];echo " ";echo $rw_cliente['am'];echo " ";echo $rw_cliente['nom'];
				echo "<br> Direccion	:";
				echo $rw_cliente['direc'];
				echo "<br> Teléfono		: ";
				echo $rw_cliente['cel'];
				echo "<br> Email 		  : ";
				echo $rw_cliente['correo'];
			?>

		   </td>
        </tr>
    </table>
       <br>
		<table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
      <tr>
         <td style="width:25%;text-align:center" class='midnight-blue'>FECHA DESEMBOLSO</td>
          <td style="width:25%;text-align:center" class='midnight-blue'>MONTO APROBADO</td>
    <td style="width:25%;text-align:center" class='midnight-blue'>TAZA</td>
     <td style="width:25%;text-align:center" class='midnight-blue'>TIPO</td>
      </tr>
		<tr>
           <td style="width:25%;text-align:center">
					<?php $fec12 = preg_split("~-~", $fechaD);
			    $fechaD="$fec12[2] / $fec12[1] / $fec12[0]";
          echo $fechaD;
					?>
				</td>
           <td style="width:25%;text-align:right"><?php echo $S. $montoA;?></td>
		  <td style="width:25%;text-align:center"><?php echo $taza." %";?></td>
		  <td style="width:25%;text-align:center">
				<?php
				switch ($tipo) {
					case 1:
							$ti="TRANSPORTE";
						break;
				  case 2:
							$ti="COMERCIO";
						break;
					case 3:
							$ti="PRENDATARIO";
						break;
				}
				 echo $ti;?></td>
      </tr>
    </table>
	<br>
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 7pt;" border="0.1px">
        <tr>
            <th style="width: 10%;text-align:center" class='midnight-blue'>Nº Cuota</th>
            <th style="width: 16%;text-align:center" class='midnight-blue'>Fecha Prog.</th>
            <th style="width: 15%;text-align:center" class='midnight-blue'>Cuota</th>
          	<th style="width: 10%;text-align:center;" class='midnight-blue'>Monto Pag.</th>
            <th style="width: 10%;text-align:center" class='midnight-blue'>Pago Mora</th>
            <th style="width: 13%;text-align:center" class='midnight-blue'>Fecha de pago</th>
            <th style="width: 15%;text-align:center" class='midnight-blue'>Saldo</th>
            <th style="width: 10%;text-align:center" class='midnight-blue'>Firma</th>
            <!--<th style="width: 8%;text-align:center" class='midnight-blue'></th>
            <th style="width: 8%;text-align:center" class='midnight-blue'></th>-->
        </tr>
<?php
$nums=1;
//$sql=mysqli_query($con,"select * from tprestamo p,tpresta_detalle dp where p.idP=dp.idP and p.idP='".$idprestamo."'");
$sql=mysqli_query($con,"select * from tpresta_detalle where idP='".$idprestamo."'");
while ($row=mysqli_fetch_array($sql))
	{
		$ncuota=$row['ncuota'];
		$fechap=$row['fechaProg'];
		$fechapag=$row['fechaPago'];
		$cuota=$row['cuota'];
		$cuota_f=number_format($cuota,2);//Formateo variables
			if(!empty($row['montoPagado']))
			{
				$monto=$row['montoPagado'];
			}
			else
			{
			$monto="";
			}
		$monto_p=number_format($monto,2);//Formateo variables
		if(!empty($row['pagoMora']))
		{
			$mora=$row['pagoMora'];
		}
		else
		{
				$mora="";
		}

		$mora_f=number_format($mora,2);//Formateo variables
		$saldo=$row['saldo'];
		$saldo_f=number_format($saldo,2);//Precio total formateado
		if ($nums%2==0)
		{
			$clase="clouds";
		} else
		{
			$clase="silver";
		}
		$fec1 = preg_split("~-~", $fechap);
    $fechap="$fec1[2] / $fec1[1] / $fec1[0]";
	?>
   <tr>
   <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $ncuota;?></td>
   <td class='<?php echo $clase;?>' style="width: 16%; text-align: left"><?php echo $fechap;?></td>
   <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $S." ".$cuota_f;?></td>
   <td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php echo $monto_p;?></td>
   <td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php echo $mora_f;?></td>
   <td class='<?php echo $clase;?>' style="width: 13%; text-align: center"><?php echo $fechapag;?></td>
   <td class='<?php echo $clase;?>' style="width: 15%; "><?php echo $S." ".$saldo_f;?></td>
   <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"></td>
   <!--<td class='<?php echo $clase;?>' style="width: 8%; text-align: center"></td>
   <td class='<?php echo $clase;?>' style="width: 8%; text-align: center"></td>-->
   </tr>
	<?php
	$nums++;
	}
?>
    </table>
