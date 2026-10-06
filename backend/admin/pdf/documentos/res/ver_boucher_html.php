<style type="text/css">
 #diag{
	position: absolute;
	top: 280px;
	border:1px dotted red;
	text-transform:capitalize;
		font-style: italic;
			 font-size:10px;
   font-family:Courier;
   /*width:200px;
   height:100px;*/
   /*
   background-image: url(../../img/dollar.jpg);
   background-attachment: fixed;
   background-repeat: repeat;*/
}
 #diag2{
	position: absolute;
	top: 280px;
	left: 360px;
	border:1px dotted red;
	text-transform:capitalize;
		font-style: italic;
			 font-size:10px;
   font-family:Courier;
   /*width:200px;
   height:100px;*/
   /*
   background-image: url(../../img/dollar.jpg);
   background-attachment: fixed;
   background-repeat: repeat;*/
}
td#back {
	background: url(../../img/logocreditos3.png);
	background-repeat: no-repeat;
	background-position: center;
}
tr#altura{
height: 150px;
 }
 .pricing_table_wdg {
	list-style:none;
	float:left;
	width:147px;
	margin:0;
	padding:5px;
	text-align:center;
}
.pricing_table_wdg tr td {
	border-bottom:1px dashed #cfd2d2;
	padding:10px 0;
}
.pricing_table_wdg2 tr td {
	border-bottom:1px  #cfd2d2;
	padding:10px 0;
}
</style>
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
<page backtop="15mm" backbottom="20mm" backleft="20mm" backright="15mm" style="font-size: 6pt; font-family: Courier" >
	<div id="diag">Usuario</div>
	<div id="diag2">Cliente</div>
			<?php
				$sql_U=mysqli_query($con,"select * from tusuario where idU='".$idU."'");
				$rw_U=mysqli_fetch_array($sql_U);
				$datosU= $rw_U['dniU']."".$rw_U['apU'];
				$sql_det=mysqli_query($con,"select * from tpresta_detalle where idPD='".$idPrestdet."'");
				$rw_det=mysqli_fetch_array($sql_det);
				$montoP= $rw_det['montoPagado'];
				$cuotaP= $rw_det['cuota'];
				$ncuotaP= $rw_det['ncuota'];
				$saldoP= $rw_det['saldo'];
				$idP= $rw_det['idP'];

				$sql_p=mysqli_query($con,"select * from tprestamo where idP='".$idP."'");
				$rw_p=mysqli_fetch_array($sql_p);
				$plazo= $rw_p['plazo'];
				if ($rw_det['estado']==2) {
					$pend=($plazo-$ncuotaP)+1;
				}else{
					$pend=$plazo-$ncuotaP;
				}
				$sql_detC=mysqli_query($con,"select * from tpresta_detalle d,tprestamo p,tclie_general c where d.idPD='".$idPrestdet."' and d.idP=p.idP and p.idCG=c.idCG");
				$rw_detC=mysqli_fetch_array($sql_detC);
				$clienteD= $rw_detC['ap']." ".$rw_detC['am']." ".$rw_detC['nom'];
			?>
	<table  border="0"  cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
  <thead>
     <tr>
           <th style="width:30%;" ><?php include("encabezado_boucher.php");?></th>
		  <td style="width:19%;" ><label style="font-family: Courier"><b>CREDITO DIARIO<br>CREDITO A TRANSPORTISTA<br>PRENDARIOS</b></label></td>
		   <th style="width:2%;" >.</th>
		   	  <th style="width:30%;"><?php include("encabezado_boucher.php");?></th>
		     <td style="width:19%;" ><label style=" font-family: Courier"><b>CREDITO DIARIO<br>CREDITO A TRANSPORTISTA<br>PRENDARIOS</b></label></td>
        </tr>
  </thead>
  <tbody>
     <tr id="altura">
           <td  id="back"  colspan="2" style="width:49%;height: 150%" ><?php include("body_boucher.php");?></td>
		   <td style="width:2%;height: 150%" >.</td>
		   	<td id="back" colspan="2" style="width:49%;height: 150%"><?php include("body_boucher.php");?></td>
        </tr>
        <tr>
           <td style="width:30%"></td>
		  <td align="center" style="width:19%;" >_________________________<br><br>FIRMA <br><br></td>
		   <td style="width:2%;" >.</td>
		   	  <td style="width:30%;"></td>
		    <td align="center" style="width:19%;" >_________________________<br><br>FIRMA <br><br></td>
        </tr>
  </tbody>
  <tfoot>
      <tr>
       <td colspan="2" style="width:49%;" ><?php include("footer_boucher.php");?></td>
		  <!--<td style="width:19%;" >2</td>-->
		   <td style="width:2%;" >.</td>
		   	  <td colspan="2" style="width:49%;"><?php include("footer_boucher.php");?></td>
		   <!--<td style="width:19%;" >5</td>-->
        </tr>
  </tfoot>
</table>
</page>
