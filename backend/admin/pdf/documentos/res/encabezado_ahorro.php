<?php
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
           <img style="width: 35%;" src="../../titulo/<?php echo $jua1['logo']; ?>"><br>
            </td>
			<td style="width: 50%; color: #34495e;font-size:10 px;text-align:center">
                <span style="color: #34495e;font-size:12px;font-weight:bold"><?php echo $jua1['nombreEmpresa'];;?></span>
				<br><?php echo get_row('toficina','direccion', 'idO','1');?> <br>
				Teléfono: <?php echo get_row('toficina','telefono', 'idO','1');?><br>
				Email: <?php echo get_row('toficina','correo', 'idO', '1');?>
            </td>
			<td style="width: 25%;text-align:right">

			</td>
        </tr>
    </table>
	<?php }?>
