<?php
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
           <img style="height: 60px;" src="../../titulo/img/<?php echo $jua1['logo'] ?>"><br>
            </td>
			<td style="width: 50%; color: #34495e;font-size:11px;text-align:center">
                <span style="color: #34495e;font-size:12px;font-weight:bold"><?php echo $jua1['nombreEmpresa'];?></span>
				<br><?php echo get_row('toficina','direccion', 'idO',$idOfi);?> <br>
				Teléfono: <?php echo get_row('toficina','telefono', 'idO',$idOfi);?><br>
				Email: <?php echo get_row('toficina','correo', 'idO',$idOfi);?>
            </td>
			<td style="width: 25%;text-align:right">

			</td>
        </tr>
    </table>
	<?php }?>
