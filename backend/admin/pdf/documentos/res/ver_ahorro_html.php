<style type="text/css">

table { vertical-align: top; }
tr    { vertical-align: top; }
td    { vertical-align: top; }
.midnight-blue{
	background:#2c3e50;
	padding: 4px 4px 4px;
	color:white;
	font-weight:bold;
	font-size:7px;
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
}

</style>
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="100mm" style="font-size: 6pt; font-family: arial" >
        <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 33%; text-align: left">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 33%; text-align: center">
                    <span>Las oportunidades pequeñas son el principio de las grandes empresas</span>
                </td>
                <td style="width: 33%; text-align: right">
                    &copy; <?php echo " "; echo  $anio=date('Y'); ?>
                </td>
            </tr>
        </table>
    </page_footer>
	<?php include("encabezado_ahorro.php");?>
    <br>	
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
        <tr>
           <td style="width:50%;" class='midnight-blue'>AHORRO DE</td>
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
				echo "<br> Email 		: ";
				echo $rw_cliente['correo'];
			?>
			
		   </td>
        </tr> 
    </table>
       <br>
		<table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
        <tr>
           <td style="width:25%;" class='midnight-blue'>FECHA</td>
            <td style="width:25%;" class='midnight-blue'>TIPO</td>
		  <td style="width:25%;" class='midnight-blue'>SALDO</td>
		   <td style="width:25%;" class='midnight-blue'>USUARIO</td>
        </tr>
		<tr>
      <?php    date_default_timezone_set('america/lima');      

    $date_added=date("Y-m-d H:i:s"); ?>
   
          <td style="width:25%;"><?php echo $date_added;?></td>
           <td style="width:25%;"><?php echo $tipo;?></td>
		  <td style="width:25%;"><?php echo "";?></td>
		  <td style="width:25%;"><?php echo "";?></td>
        </tr>      
    </table>
	<br> 
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 5pt;">
        <tr>
            <th style="width: 20%;text-align:left" class='midnight-blue'>Tipo</th>
                <th style="width: 20%;text-align:center" class='midnight-blue'>Usuario</th>
            <th style="width: 20%;text-align:center" class='midnight-blue'>Obs.</th>
          	<th style="width: 20%;text-align:center;" class='midnight-blue'>Fecha</th>
        <th style="width: 20%;text-align:right" class='midnight-blue'>Monto</th>
        </tr>
<?php
$nums=1;
$saldo=0;
$sald2=0;
$sql=mysqli_query($con,"select * from tahorro a,tahorro_deta da,tusuario u where a.idA=da.idA and da.idU=u.idU and a.idA='".$idahorro."'");
while ($row=mysqli_fetch_array($sql))
	{
	$obs=$row['obs'];
	$fecha=$row['fecha'];
	$monto=$row['monto'];
  $dniU=$row['dniU'];
	$monto_f=number_format($monto,2);//Formateo variables
  $movimiento="";
            if($row['tipo']=='1')
            {
              $movimiento="Depósito";
              $saldo=$saldo+$monto;
            }
            else if($row['tipo']=='2')
            {
              $movimiento="Ahorro";
               $saldo=$saldo+$monto;
            }
            else if($row['tipo']=='3')
            {
              $movimiento="Retiro";
               $saldo2=$saldo2+$monto;
            }
            else if($row['tipo']=='4')
            {
              $movimiento="Descuento";
                $saldo2=$saldo2+$monto;
            }
            else if($row['tipo']=='5')
            {
              $movimiento="Adelanto";
                $saldo2=$saldo2+$monto;
            }
	if ($nums%2==0){
		$clase="clouds";
	} else {
		$clase="silver";
	}
	?>
   <tr>
   <td class='<?php echo $clase;?>' style="width: 20%; text-align: left"><?php echo $movimiento;?></td>
      <td class='<?php echo $clase;?>' style="width: 20%; text-align: right"><?php echo $dniU;?></td>
   <td class='<?php echo $clase;?>' style="width: 20%; text-align: left"><?php echo $obs;?></td>
   <td class='<?php echo $clase;?>' style="width: 20%; text-align: right"><?php echo $fecha;?></td>
  <td class='<?php echo $clase;?>' style="width: 20%; text-align: right"><?php echo $S." ".$monto_f;?></td>   
   </tr>
	<?php 
	$nums++;
	}
?>  
<tr>
        <?php $saldo3=$saldo-$saldo2; ?>
          <td></td>
            <td colspan="3" style="widtd: 85%; text-align: right;">SALDO <?php echo $S;?> </td>
            <td style="widtd: 15%; text-align: right;"> <?php echo number_format($saldo3,2);?></td>
        </tr>
    </table>
<br><div style="font-size:7pt;text-align:center;">SU PUNTUALIDAD ES SU MEJOR GARANTIA PARA SU PROXIMO CREDITO!<br></div>
<div style="font-size:7pt;text-align:center;"><br> Las oportunidades pequeñas son el principio de las grandes empresas</div>
</page>

