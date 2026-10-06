$(document).ready(function() {
  //contendor();
  //fechaActual();

});
function BuscarUsuario()
{
  var id=$('#lstusuarios').val();
  var fe=$('#fecha').val();
  $.post('modulo1/detalleCierre.php',{id:id,fe:fe}).done( function(respuesta)
  {
    $('#detalleCaja').html(respuesta);
  });
  setTimeout(function()
  {
    //billetajeActual();
  },100,"JavaScript");

}
