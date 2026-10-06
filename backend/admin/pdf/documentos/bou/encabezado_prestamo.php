<?php
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
           <img style="width: 35%;" src="../../titulo/img/<?php echo $jua1['logo']; ?>"><br>
            </td>
			<td style="width: 50%; color: #34495e;font-size:6px;text-align:center">
                <span style="color: #34495e;font-size:8px;font-weight:bold"><?php echo $jua1['nombreEmpresa'];?></span>
				<br><?php echo get_row('toficina','direccion', 'idO',$idOfi);?> <br>
				Teléfono: <?php echo get_row('toficina','telefono', 'idO',$idOfi);?><br>
				Email: <?php echo get_row('toficina','correo', 'idO',$idOfi);?>
            </td>
			<td style="width: 25%;text-align:right">
			 Nº credito - <?php echo $ncredito;?>
			</td>
        </tr>
    </table>
	<?php }?>
