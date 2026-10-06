$(document).ready(function() {
  /*setInterval(function () {
    btn();
  }, 10);*/
  opera();
});
/*setTimeout(function()
   {

   },510,"JavaScript");*/
function opera()
{
  $.post( 'listarBasic/soli.php').done( function( respuesta )
  {
    $( '#solici' ).html( respuesta );
  });

}
//enviar solicitud de EXTORNO
function solicitar()
{
  var codigo=$('#txtcodigo').val();
  var motivo=$('#txtmotivo').val();
  if(codigo!="" && motivo!="")
  {
    $.post( 'add/solicitarExtorno.php',{codigo:codigo,motivo:motivo}, function( data )
    {
      if(data.resultado)
      {
        swal("Solicitud enviada con exito.","","success");
      }
      else {
        swal("La solicitud no procede","el codigo ya fue solicitado, extornado o no existe","warning");
      }
    },"json");
    setTimeout(function()
       {
         opera();
         limpiar();
       },500,"JavaScript");
  }
  else
  {
    swal("COMPLETE LOS CAMPOS CON (*)","","info");
  }
}
//limpieza
function limpiar()
{
  $('#txtcodigo').val('');
  $('#txtmotivo').val('');
}
//eliminar l solicitud si esta
function elimini(id)
{
  $.post( 'adelete/deleteSoli.php',{id:id});
  swal("SOLICITUD DE EXTORNO ELIMINADA","","success");
  setTimeout(function()
     {
       opera();
     },500,"JavaScript");
}
