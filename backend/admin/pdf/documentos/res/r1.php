<style type="text/css">
table { vertical-align: top; }
tr    { vertical-align: top; }
td    { vertical-align: top; }
.midnight-blue{
	background:#2c3e50;
	padding: 4px 4px 4px;
	color:white;
	font-weight:bold;
	font-size:12px;
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
.juve{
	/*width: 100%;*/
    border: 1px solid #000;
}
.juve2{
	border: 1px solid #000;
   border-spacing: 0;
}
.juq
{
	align:center;
	font-size:10px;

}
.cone{
	font-size:15px;
	text-align: center;
	border: 1px solid;
	cellspacing:0;
}
.cone0{
	font-size:10px;
	text-align: center;
	cellspacing:0;
}
.cone01{
	font-size:10px;
	text-align: center;
	cellspacing:0;
	border: 1px solid;
	valing:middle;
}
.cone1{
	background: #9AA9A0;

	border: 1px solid;
	cellspacing:0;
}
.cone2{
	/*font-size:10px;*/
	text-align: center;
	border: 1px solid;
	cellspacing:0;
}
.late{
	border-bottom: 1px solid;
	font-size:12px;
}
</style>
<page backtop="15mm" backbottom="20mm" backleft="15mm" backright="15mm" style="font-size: 6pt; font-family: arial" >
        <page_footer>
	        <table class="page_footer">
	            <tr>
	                <td style="width: 33%; text-align: left">
	                    P&aacute;gina [[page_cu]]/[[page_nb]]
	                </td>
	                <td style="width: 33%; text-align: center" style="font-size:10px;">
		                    <span> <b>Directora: Dra. ASTORAY VIVANCO ,Marilu Isabel</b> <?php echo date("d/m/Y H:m:s");?></span>
	                </td>
	                <td style="width: 33%; text-align: right">
										<span style="font-size:8px">Desarrollador: Juvenal Perez R.</span>
	                    &copy; <?php echo " "; echo  $anio=date('Y'); ?>
	                </td>
	            </tr>
	        </table>
    		</page_footer>
<table  border=""  cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
  <thead>
     <tr>
        <th style="width:100%;">
					<table cellspacing="0" style="width: 100%;" >
			        <tr>
			            <td style="width: 25%; color: #444444;text-align:center" rowspan="3" >
                    <img style="width: 35%;" src="../../titulo/img/<?php echo $jua1['logo']; ?>">
									</td>
									<td style="width:60%;text-align:center;" valign="middle" colspan="2">
										<h3>RESULTADO DE CIERRE DE MES</h3>
									</td>
									<td style="width:15%;text-align:center">
										FECHA Y HORA CREADA: <?php echo date("d/m/Y H:m:s");?>
									</td>
			        </tr>
							<tr>
								<td style="width:2%;font-size:12px;" valign="middle">
									<label for="">OFICINA </label>
								</td>
								<td style="font-size:12px;border-bottom:1px solid" valign="middle" >
									<label for=""> </label>:
								</td>
								<td style="font-size:15px;" valign="middle">
									<label for=""> </label>
								</td>
							</tr>
							<tr>
								<td style="width:2%;font-size:12px;" valign="middle">
									<label for="">MES </label>
								</td>
								<td style="font-size:12px;border-bottom:1px solid" valign="middle">
										: <?php ECHO strtoupper($mes); ?>
								</td>
								<td style="font-size:15px;" valign="middle">
								</td>
							</tr>
			    </table>
					<br>
				</th>
     </tr>
  </thead>
  <tbody>
    <tr>
      <td style="width:100%;">
        <table style="width:100%;" cellspacing="0" border="0.1px">
          <tr>
            <th style="width:2%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Item</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Cliente </th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Fecha Desembolso</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Tipo Credito</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Tasa %</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Periodo</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Colocación</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Cuotas</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Interes</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">C + I</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Amortización</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Saldo</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Retraso Días</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Moras</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">N° Credito</th>
            <th style="width:5%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Tipo Pago</th>
          </tr>
          <?php
            //===================
            $totalclie=0;$tclie[0]="0";$nuevo=0;$re=0;$recurre[0]="0";$t1=0;$t2=0;$t3=0;$col=0;$inte=0;$mor=0;$diar=0;$sem=0;$item=0;
            $z=mysqli_query($con,"SELECT (select sum(tpd.cuota) from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and fechaProg>='$ini' and fechaProg<='$fin') as cuota,(select sum(montoPagado) from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and fechaPago>='$ini' and fechaPago<='$fin') as pagado,(select ROUND((sum(tpd.cuota) -sum(montoPagado)),2)as deuda from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tp.estado='4' and tc.idO='$idO') as deuda from tpresta_detalle limit 1");
            $zs=mysqli_fetch_array($z);
            $qw=mysqli_query($con,"SELECT motivo,sum(total) as suma FROM tcaja_usu_detal tcdu inner join tcaja_usuario tcu on tcdu.idCA=tcu.idCA inner join tcaja_oficina tco on tcu.idCO=tco.idCO inner join tahorro_motivo tm on tcdu.tipo=tm.idam  where tco.idO='$idO' and tco.ini>='$ini' and tco.ini<='$fin' and tcdu.estadodt='2' and tcdu.habilitacion is null group by tcdu.tipo order by motivo asc");
            $c=mysqli_query($con,"SELECT tpd.idP as id, fechaDesembolso from tpresta_detalle tpd inner join tprestamo tp on tpd.idP=tp.idP inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and (tp.estado='4' or tp.estado='5') and tp.comentario is null and fechaProg between cast('$ini' as date) and cast('$fin' as date) group by tpd.idP union SELECT tp.idP as id,fechaDesembolso from tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG where tc.idO='$idO' and (tp.estado='4' or tp.estado='5') and tp.comentario is null and fechaDesembolso between cast('$ini' as date) and cast('$fin' as date)");
            while ($r=mysqli_fetch_array($c))
            {
              $id=$r['id'];
              $item++;
              if ($item%2==0)
          		{
          			$clase="clouds";
          		} else
          		{
          			$clase="silver";
          		}
              $z=mysqli_query($con,"SELECT @id:=tp.idP as id,concat(ap,' ',am,' ',nom) as dato,tp.idCG as idc,tipoP,fechaDesembolso,pago,taza,plazo,montoAprovado,cuota,n_credito as credi,mora,@inte:=round((montoAprovado*(taza/100)),2) as interes,@ci:=(montoAprovado+@inte) as ci,@amr:=(SELECT if(sum(if(montoPagado is null,0,montoPagado)) is null,0,sum(if(montoPagado is null,0,montoPagado))) FROM tpresta_detalle where idP=@id and fechaPago between cast('$ini' as date) and cast('$fin' as date)) as amorti,ROUND((@ci-@amr),2) as saldo,@more:=round((SELECT if(sum(if(pagoMora is null,0,pagoMora)) is null,0,sum(if(pagoMora is null,0,pagoMora))) FROM tpresta_detalle where idP=@id and fechaPago between cast('$ini' as date) and cast('$fin' as date)),2) as moras,round((@more/mora)) as retra FROM tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG where idP='$id' and (tp.estado='4' or tp.estado='5')");
              $x=mysqli_fetch_array($z);

             $tcredi="";
             switch ($x['tipoP'])
             {
               case 1:
                 $tcredi="Transporte";
               break;
               case 2:
                 $tcredi="Comercio";
               break;
               case 3:
                 $tcredi="Prendatario";
               break;
             }
             $tpago="";
             switch ($x['pago']) {
               case 1:
                 $tpago="DIARIO";
                 $diar++;
               break;
               case 2:
                 $tpago="SEMANAL";
                 $sem++;
               break;
               case 3:
                 $tpago="PAGO UNICO";
               break;
               case 4:
                 $tpago="MENSUAL";
               break;
             }
             //====clientes
             if($mesi==fre($x['fechaDesembolso']))
             {
							 $totalclie++;
               $tclie[$totalclie]=$x['idc'];
               if($x['credi']=='1')
               {
                 $nuevo++;
               }
               else
               {
								$re++;
                $recurre[$re]=$x['idc'];
               }
               //===
               switch ($x['tipoP'])
               {
                 case 1:
                   $t1++;
                 break;
                 case 2:
                   $t2++;
                 break;
                 case 3:
                   $t3++;
                 break;
               }
               //===
                $col+=$x['montoAprovado'];
                $inte+=$x['interes'];
                $mor+=$x['moras'];
             }
             ?>
             <tr>
               <td class="<?php echo $clase; ?>" style="text-align:center;"><?php echo $item; ?></td>
               <td class="<?php echo $clase; ?>" style=""><?php echo $x['dato'] ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo fra($x['fechaDesembolso']); ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:center"><?php echo $tcredi; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:center"><?php echo $x['taza']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:center"><?php echo $x['plazo']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['montoAprovado']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['cuota']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['interes']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['ci']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['amorti']; ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['saldo'] ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['retra'] ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:right"><?php echo $x['moras'] ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:center"><?php echo $x['credi'] ?></td>
               <td class="<?php echo $clase; ?>" style="text-align:center"><?php echo $tpago ?></td>
             </tr>
             <?php
            }
            ?>
        </table>
				<br>
				<table style="width:100%;" cellspacing="0" border="0.1px">
					<tr>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Cliente</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Clientes Recurrentes</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Clientes Nuevos</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Credito Comercial</td>
						<td style="width: 8%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Credito Trasporte</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Credito Prendatario</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Colocación</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Interes</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Cobrar</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Cobrado</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Mora</td>
						<td style="width: 8%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Total Proyectado</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Creditos Diarios</td>
						<td style="width: 7%;background-color:<?php echo $jua1['color'] ?>;text-align:center;" valign="middle">Creditos Semanales</td>
					</tr>
					<tr>
						<td style=""><?php echo (count(array_unique($tclie))-1); ?></td>
						<td style=""><?php echo (count(array_unique($recurre))-1); ?></td>
						<td style=""><?php echo $nuevo; ?></td>
						<td style=""><?php echo $t1; ?></td>
						<td style=""><?php echo $t2; ?></td>
						<td style=""><?php echo $t3; ?></td>
						<td style=""><?php echo number_format($col, 2, '.', ''); ?></td>
						<td style=""><?php echo number_format($inte, 2, '.', ''); ?></td>
						<td style=""><?php echo $zs['cuota']; ?></td>
						<td style=""><?php echo $zs['pagado']; ?></td>
						<td style=""><?php echo number_format($mor, 2, '.', ''); ?></td>
						<td style=""><?php echo $zs['deuda']; ?></td>
						<td style=""><?php echo $diar; ?></td>
						<td style=""><?php echo $sem; ?></td>
					</tr>
				</table>
      </td>
    </tr>
		
  </tbody>
</table>
</page>
