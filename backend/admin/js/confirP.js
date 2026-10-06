//pasar nombre
$(function() {
    var tipoa = $(this).val();
    $.post( 'listar/listarConfirP.php').done( function(respuesta)
    {
      $( '#listarA' ).html( respuesta );
    });
});
function ver_estado()
{
   $.post( 'listar/listarConfirP.php').done( function(respuesta)
    {
      $( '#listarA' ).html( respuesta );
    });
}
// Editar estado activo
function confirmar(idBille)
{
  //alert(hola);
  var id=idBille;

  $.post('ajax/ECbilletaje.php',{id:id});
  //swal("Billetaje confirmado con exito","","info");
  setTimeout(function()
  {
  ver_estado();
},100,"JavaScript");
}
// Editar estado inactivo
function desconfirmar(idBille)
{
  var id=idBille;
  $.ajax({
    type:"GET",
    url:"ajax/EDbilletaje.php?id="+id,
    }).done(function(data){
       //  setInterval("actualizar()",600);
         ver_estado();
    });
    swal("Billetaje eliminado con exito","","info");
}
function eliminar(idBille)
{
  var id=idBille;
  $.ajax({
    type:"GET",
    url:"ajax/Ebilletaje.php?id="+id,
    }).done(function(data){
       //  setInterval("actualizar()",600);
         ver_estado();
    });
    swal("Billetaje eliminado con exito","","info");
}
