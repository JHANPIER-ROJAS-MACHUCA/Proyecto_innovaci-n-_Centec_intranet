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
<page backtop="15mm" backbottom="20mm" backleft="15mm" backright="15mm" style="font-size: 6pt; font-family: arial" >
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
<table  border="0"  cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
  <thead>
     <tr>
           <th style="width:49%;"><?php include("encabezado_prestamo.php");?>
    <br></th>
		   <th style="width:2%;">.</th>
		   	<th style="width:49%;"><?php include("encabezado_prestamo.php");?>
    <br></th>
     </tr>
  </thead>
  <tbody>
     <tr>
           <td style="width:49%;"><?php include("body_prestamo.php");?></td>
		   <td style="width:2%;">.</td>
		   <td style="width:49%;"><?php include("body_prestamo.php");?></td>
        </tr> 
  </tbody>
  <tfoot>
      <tr>
       <td style="width:49%;" ><?php include("footer_prestamo.php");?></td>
		<td style="width:2%;" >.</td>
		<td style="width:49%;"><?php include("footer_prestamo.php");?></td>
      </tr>
  </tfoot>
</table>
</page>
