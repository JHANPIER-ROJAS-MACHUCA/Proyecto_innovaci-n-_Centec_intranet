$(document).ready(function() {
  inicio();
  saldoUser();
  setInterval(saldoUser,5000);
});
/*setTimeout(function()
{
  mostr();
},100,"JavaScript");*/
function inicio()
{
  var feci=$('#fecha').val();
  $('#fechi').val($('#fecha').val());
  $.post( 'listarBasic/iniOpeUs.php', { fechi: feci}).done( function( respuesta )
  {
  $( '#iniciarB' ).html( respuesta );
  });
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
  $.post( 'listarBasic/iniOpeUs.php', { fechi: fe}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
  });
}
//iniciar al abrir caja
function iniciaCAU(ide)
{
  var fe=$('#fechi').val();

  $.post('listarBasic/usuAbrirCaja.php',{iden:ide});
  //setInterval(verB,1000);
  setTimeout(function()
  {
    verB();
  },100,"JavaScript");
}
//saldo de usuario en su cuenta con la ultima caja abierta de su oficina
function saldoUser()
{

  $.post( 'listarBasic/actuSaldo.php').done( function( respuesta )
  {
    $( '#salUSU' ).val( respuesta );
  });

}
