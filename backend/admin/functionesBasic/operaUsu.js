$(document).ready(function() {
  inicio();
  //saldoUser();
  //setInterval(saldoUser,5000);
});
function inicio()
{
  var feci=$('#fecha').val();
  $('#fechi').val($('#fecha').val());
  $.post( 'listarBasic/deudores.php', { fechi: feci}).done( function( respuesta )
  {
    $( '#iOPEHISTO' ).html( respuesta );
  });
}
function verDeuda(id)
{
  var feci=$('#fecha').val();
  $.post( 'listarBasic/cuotasP.php', { ide: id,fecha:feci}).done( function( respuesta )
  {
    $( '#hitoPAGO' ).html( respuesta );
  });
  var nom=($('#'+id).data("nombre"));
  $('#clieNombre').html(nom);
  $('#idCGene').val(id);

}

//pagar por cuota
function pagarCuota(id,mora,pago)
{
  $.post( 'add/pagoCuota.php', {id:id,mora:mora,pago:pago});
  //alert(id+" "+ mora);
  var ide=$('#idCGene').val();
  setTimeout(function()
  {
  verDeuda(ide);
  inicio();
  },100,"JavaScript");
}
///pago por monto
function pagoMonto()
{
  var monto1=$('#txtmonto').val();
  var id1=$('#idCGene').val();
  var fecha1=$('#fecha').val();
  $.post('add/pagoMonto.php', {id:id1,monto:monto1,fecha:fecha1});
  $('#txtmonto').val('');
  setTimeout(function()
  {
    verDeuda(id1);
    inicio();
  },100,"JavaScript");
}
/*
setTimeout(function()
{
  verB();
},100,"JavaScript");*/
function imprimir_boucher(idprestadet){
      VentanaCentrada('./pdf/documentos/ver_boucher.php?idprestadet='+idprestadet,'Boucher','','1024','768','true');
}