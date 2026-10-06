$(document).ready(function() {
  iniciar();
setInterval(function () {
  cerrar();
  //alert("hola");
}, 60000);
});
function iniciar()
{
  var feci=$('#fecha').val();
  $.post('listar/iniGerente.php',{fecha:feci}).done(function(respuesta)
  {
    $('#iniciarG').html(respuesta);
  });
  cerrar();
}
function cerrar()
{
  var feci=$('#fecha').val();
  $.post('listar/cerrarGerente.php',{fecha:feci}).done(function(respuesta)
  {
    $('#cerrarB').html(respuesta);
  });
}
function cerrarEmpresa()
{
	var feci=$('#fecha').val();
  var monto=$('#montoCierre1').val();
  //alert(monto);
  $.post('add/cerrarEmp.php',{monto:monto,fecha:feci});
  setTimeout(function()
  {
    cerrar();

  },100,"JavaScript");
  swal("ACABA DE CERRAR CAJA","hasta mañana","success");
  location.href='iniOperaciones.php';
}
