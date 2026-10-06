$(document).ready(function() {
  setTimeout(function()
  {
    mostr();
  },100,"JavaScript");
  setInterval(mostr,10000);
});
//Designar
$('#iniOperacionesF').on('submit',(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: 'add/agreCajaOfiDesig.php',
        data:formData,
        cache:false,
        contentType: false,
        processData: false

});
$('#UsuariOPE').val('');
$('#MontOPE').val('');
//$('#TipOPE').val('');
/*verB1();
verB1();*/
  mostr();
setTimeout(function()
{
  mostr();
},100,"JavaScript");
}));
//enviar la fecha pasada porbuscador y devuelve la info
function verB1()
{
  var fe=$('#fechi').val();
  $.post( 'listar/iniOpe.php', { fechi: fe}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
  });
}
//elimina el registro
function Elimi(id)
{
  $.post( 'adelete/deleteCaOpeDes.php', { valor: id});
  setTimeout(function()
  {
    mostr();
  },100,"JavaScript");
}
//muestra el historial de cajaoperaciones detalle
function mostr()
{
  //cogemos el id identificado si existe la caja
  var id=$('#idCO').val();
  $.post( 'listar/iniOpe1.php', { ideNece: id}).done( function( respuesta )
  {
    $( '#iOPEHISTO' ).html( respuesta );
  });
  saldOPEA();
}

function saldOPEA()
{
;
  var iden=$('#idCO').val();

  $.post( 'add/saldo.php', { defini:iden}).done( function( respuesta )
  {
    $( '#saldOPE' ).html(respuesta);
    $('#saldOPE2').val(respuesta);
  });
}
//verificar cantidad de dinero
function veriDiner()
{
  var desi=parseFloat($('#MontOPE').val());
  var dinerin=parseFloat($('#saldOPE2').val());
  if(dinerin < desi)
  {
    swal("El monto supera el saldo actual","","warning");
    $('#MontOPE').val('');
  }
}
