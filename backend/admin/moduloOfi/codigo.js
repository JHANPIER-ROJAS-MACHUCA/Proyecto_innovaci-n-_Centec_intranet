$(document).ready(function() {
  inicio();
});
function inicio()
{
  $.post('listar/listarOficina.php').done( function( respuesta )
  {
    $( '#listarO' ).html( respuesta );
  });
}
function agregarOficina()
{
  var depa=$('#txtdepa').val();
  var prov=$('#txtprovi').val();
  var dis=$('#txtdis').val();
  var dir=$('#txtdirec').val();
  var tel=$('#txttel').val();
  var ema=$('#txtema').val();
  var id=$('#idoe').val();
  $.post('add/agreOfi.php',{id:id,depa:depa,prov:prov,dis:dis,dir:dir,tel:tel,ema:ema}).done( function( respuesta )
  {
    inicio();
  });
}
function limpiar()
{
  $('#txtdepa').val('');
  $('#txtprovi').val('');
  $('#txtdis').val('');
  $('#txtdirec').val('');
  $('#txttel').val('');
  $('#txtema').val('');
  $('#idoe').val('');
}
function AgregarP()
{
  agregarOficina();
  limpiar();
}
function salir()
{
  AgregarP();
  $('#agreOfi').hidden();
}
