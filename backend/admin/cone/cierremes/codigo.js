$(document).ready(function() {
  //contendor();
  //fechaActual();

});
function BuscarUsuario()
{
  var ofi=$('#lstofi').val();
  var ani=$('#lstanio').val();
  var mes=$('#lstmes').val();
  $.post('cone/cierremes/detalleCierre.php',{ofi:ofi,ani:ani,mesi:mes}).done( function(respuesta)
  {
    $('#detalleCaja').html(respuesta);
  });
  $.post('cone/cierremes/detalleMes.php',{ofi:ofi,ani:ani,mesi:mes}).done( function(respuesta)
  {
    $('#detalleMesi').html(respuesta);
  });
  setTimeout(function()
  {
    //billetajeActual();
  },100,"JavaScript");

}
