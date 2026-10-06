$(document).ready(function() {
  cargarUsuarios();
});
function BuscarUsuario()
{
  var ofi=$('#lstofi').val();
  var ani=$('#lstanio').val();
  var mes=$('#lstmes').val();
  $.post('cone/cierremes1/detalleCierre.php',{ofi:ofi,ani:ani,mesi:mes}).done( function(respuesta)
  {
    $('#detalleCaja').html(respuesta);
  });
  /*$.post('cone/cierremes1/detalleMes.php',{ofi:ofi,ani:ani,mesi:mes}).done( function(respuesta)
  {
    $('#detalleMesi').html(respuesta);
  });*/
  setTimeout(function()
  {
  },100,"JavaScript");
}
//=====con usuario
function BuscarUsuario1()
{
  var ofi=$('#lstofi').val();
  var ani=$('#lstanio').val();
  var mes=$('#lstmes').val();
  var usua=$('#lstusuarios').val();
  $.post('cone/cierremes1/detalleCierre.php',{ofi:ofi,ani:ani,mesi:mes,usua:usua}).done( function(respuesta)
  {
    $('#detalleCaja').html(respuesta);
  });
  $.post('cone/cierremes1/detalleMes.php',{ofi:ofi,ani:ani,mesi:mes}).done( function(respuesta)
  {
    $('#detalleMesi').html(respuesta);
  });
  setTimeout(function()
  {
  },100,"JavaScript");
}
function cargarUsuarios()
{
  var ofi=$('#lstofi').val();
  $.post('cone/cierremes1/user.php',{ofi:ofi}).done( function(respuesta)
  {
    $('#lstusuarios').html(respuesta);
  });
}
