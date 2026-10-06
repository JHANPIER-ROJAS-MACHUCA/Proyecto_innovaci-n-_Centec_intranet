<?php include("conection/bdcredito.php") ?>
<html>
<head>
<script type="text/javascript">
function mostrar() {
document.getElementById('ee').style.display ='inherit';
};
</script>
</head>
<body bgcolor = "#DDDDDD">
<form id='alta' name='nalta' action='otra_pagina.php' method="post" >

<select onchange='mostrar()'>
<option value="0"></option>
<option value="1">uno</option>
<option value="2">dos</option>
<option value="3">tres</option>
</select>
<?php
//fecha
date_default_timezone_set('america/lima');
$date=date("Y-m-d H:i:s");
$data=extraer("select idPD FROM tpresta_detalle where idP='22' and (estado='2' or estado is null or estado='') and fechaProg<'2019-05-04'");
$m=mysqli_fetch_array($data);


?>
<div id="ee" style="display:none">listado
<select>
<option value="0"></option>
<option value="1">111</option>
<option value="2">222</option>
<option value="3">333</option>
</select>
</div>
</form>
<form method="get">
  <input type="text" name="contra" value="">
  <input type="submit" name="" value="enviar">
</form>
<br>
<div>
  <input type="text" name="txtid" id="txtid" value="">
  <a onclick="envio()" >Enviar</a>
  <a onclick="window.close()">Cerrar </a>
  <a id="btnImprimir">imprimir</a>
</div>
<div class="">
  <table id="tabla1">
  <tr>
    <td>hola</td>
    <td>como estas</td>
  </tr>
  <tr>
    <td>15</td>
    <td>25</td>
  </tr>
  </table>
</div>
<script type="text/javascript">
function envio()
{
  imprimir(5);
}
function imprimir(id)
{
      VentanaCentrada('./pdf/documentos/boucher.php?id='+id,'Boucher','','1024','768','true');
}

function imprimirElemento(elemento) {
  var ventana = window.open('', 'PRINT', 'height=400,width=600');
  ventana.document.write('<html><head><title>' + document.title + '</title>');
  ventana.document.write('</head><body >');
  ventana.document.write(elemento.innerHTML);
  ventana.document.write('</body></html>');
  ventana.document.close();
  ventana.focus();
  ventana.print();
  ventana.close();
  return true;
}

document.querySelector("#btnImprimir").addEventListener("click", function () {
  var div = document.querySelector("#ee");
  imprimirElemento(div);
});
</script>
 <script type="text/javascript" src="js/VentanaCentrada.js"></script>
 <?php
 $cantidad=10;
while ( 2<= 2)
{

}
  ?>
</body>
</html>
