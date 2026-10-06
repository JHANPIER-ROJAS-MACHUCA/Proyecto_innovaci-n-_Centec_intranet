$(document).ready(function() {
  inicio();
setInterval(function () {
  cerrar();
  //alert("hola");
}, 60000);
});
/*setTimeout(function()
{
  mostr();
},100,"JavaScript");*/
function inicio()
{
  var feci=$('#fecha').val();
  $('#fechi').val($('#fecha').val());
  $.post( 'listar/iniOpePrinci.php', { fechi: feci}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
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
function abrir(idCO)
{
  var id=idCO;
  $.ajax({
        type:"GET",
    url:"ajax/AbrirG.php?id="+id,
    }).done(function(data){
       // setInterval("actualizar()",600);
        cerrar();
    })
}
function mostr()
{
  alert("mirar");
}
function cambio()
{
    $('#fechi').val($('#fecha').val());
    verB();
}
//evalua
function verB()
{
  var fe=$('#fechi').val();
  $.post( 'listar/iniOpePrinci.php', { fechi: fe}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
  });
   cerrar();
}
//iniciar al abrir caja
function iniciaB()
{
  var fe=$('#fechi').val();
  
  $.post('add/agreCajaIni.php',{fechi:fe});
  //setInterval(verB,1000);
  setTimeout(function()
  {
    verB();
  },100,"JavaScript");
}
