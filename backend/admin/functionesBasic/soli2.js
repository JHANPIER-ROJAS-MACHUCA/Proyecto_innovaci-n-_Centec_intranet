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
  $.post( 'listarBasic/gastos.php').done( function( respuesta )
  {
    $( '#solici' ).html( respuesta );
  });
}
//enviar solicitud de EXTORNO
function solicitar()
{
  var codigo=$('#txtcodigo').val();
  var motivo=$('#txtmotivo').val();
  var tipo=$('#lsttipo').val();
  if(codigo!="" && motivo!="" && tipo!="-1")
  {
    if(codigo>0)
    {
      $.post('add/gastosAdmin.php',{monto:codigo,motivo:motivo,tipo:tipo}).done(function(respuesta)
      {
        /*if(data.resultado)
        {*/
          swal("Solicitud enviada con exito.","","success");
      //  }
      tika(respuesta);
      });
      setTimeout(function()
         {
           opera();
           limpiar();
         },500,"JavaScript");
    }
    else {
    swal("INGRESE UN MONTO REAL MAYOR DE 0","","info");
    }

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
  $.post( 'adelete/eliGasto.php',{id:id});
  swal("GASTO ELIMINADO ","","success");
  setTimeout(function()
     {
       opera();
     },500,"JavaScript");
}
//===============
//imprimir
function tika(valor)
{
  window.open("tik.php?code="+valor+"& type=2","ventana1","width=300,height=300,scrollbars=NO");
}
