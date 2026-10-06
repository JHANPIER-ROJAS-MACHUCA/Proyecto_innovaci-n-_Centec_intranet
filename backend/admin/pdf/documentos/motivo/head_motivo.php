<?php
date_default_timezone_set('america/lima');
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
           		<img style="width: 35%;" src="../../img/logocreditos.png"><br>
            </td>
						<td style="width: 50%; color: #34495e;font-size:6px;text-align:center">
			                <span style="color: #34495e;font-size:7px;font-weight:bold"><?php echo "CENTRO DE TECNOLOGIA Y CREDITOS DEL PERU";?></span>
							<br><?php echo get_row('toficina','direccion', 'idO','1');?> <br>
							Teléfono: <?php echo get_row('toficina','telefono', 'idO','1');?><br>
							Email: <?php echo get_row('toficina','correo', 'idO', '1');?><br>
							Dirección: <?php echo get_row('toficina','direccion', 'idO', '1');?><br>
            </td>
					<td style="width: 25%;text-align:right">
					 Nº Canasta - <?php echo $canasta;?>
					</td>
        </tr>
    </table>
	<?php }

	?>
