	<table class="pricing_table_wdg"   cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
           		<thead >
           			<tr>
           				<th style="width:100%;text-align: center;">CREDITOS</th>
           			</tr>
           		</thead>
           		<tbody>
           			<tr>
           				<td style="width:100%;">Nº Operacion: 000<?php echo "$idPrestdet"; ?><br>
           					cliente: <?php echo "$clienteD"; ?>
           				</td>
           			</tr>
           			<tr style="text-align: center;">
           				<td style="width:100%; text-align: center;">
           				 <table class="pricing_table_wdg2" cellspacing="0" style="width: 100%; text-align: center; font-size: 6pt;">
           				 		<tr>
           				 			<th style="width: 30%"> </th>
           				 			<td style="text-align: left">COBRANZA:</td>
           				 			<td style="width: 10%"></td>
           				 			<td style="text-align: right"><?php echo "$cuotaP"; ?></td>
           				 			<td></td>
           				 		</tr>
           				 		<tr>
           				 			<th><br></th>
           				 		</tr>
           				 		<tr>
           				 			<th style="width: 30%"></th>
           				 			<th style="text-align: left">PAGO:</th>
           				 			<th style="width: 10%"></th>
           				 			<th style="text-align: right"><?php echo "$montoP"; ?></th>
           				 			<th></th>
           				 		</tr>
           				 </table> 
           				</td>
           			</tr>
           			<tr>
           				 <?php date_default_timezone_set('america/lima');      
    					$datehora=date("Y-m-d H:i:s"); ?>
           				<td style="width:100%;">Cuota Pag: <?php echo "$ncuotaP"; ?><br>Cuota Pend: <?php echo "$pend"; ?><br>saldo: <?php echo "$saldoP"; ?><br>Usuario: <?php echo "$datosU"; ?><br>Fecha/Hora: <?php echo "$datehora"; ?></td>
           			</tr>
           			
           		</tbody>
           	</table>