<?php
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
            </td>
			<td style="width: 50%; color: black;font-size:11px;text-align:center">
                <span style="color: black;font-size:11px;font-weight:bold"><?php echo "<u>".$jua1['nombreEmpresa']." ".$jua1['siglas']."</u>";?></span><br>
				<br><?php echo get_row('toficina','direccion', 'idO',$idOfi);?> <br> <br>
				Pagare Nº 00<?php echo "$idPrest"; ?><br>
            </td>
			<td style="width: 25%;text-align:right">
			</td>
        </tr>
    </table>
	<?php }?>
