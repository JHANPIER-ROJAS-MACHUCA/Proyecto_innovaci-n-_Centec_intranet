<?php
	if ($con){
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 25%; color: #444444;">
           <img style="width: 35%;" src="../../titulo/img/<?php echo $jua1['logo'] ?>"><br>
            </td>
			<td style="width: 50%; color: #34495e;font-size:10px;text-align:left">
                <span style="color: #34495e;font-size:10px;font-weight:bold"><?php echo $jua1['nombreEmpresa']." ".$jua1['siglas'];?></span>
            </td>
			<td style="width: 25%;text-align:right">
			</td>
        </tr>
    </table>
	<?php }?>
