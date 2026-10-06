$(function() {
	listaringresos();
});
function listaringresos()
{
  setTimeout(function()
  {
    $.post( 'listar/listarIngresos.php').done( function(respuesta)
    {
      $('#ingresos').html( respuesta );
    });
  },100,"JavaScript");

}

