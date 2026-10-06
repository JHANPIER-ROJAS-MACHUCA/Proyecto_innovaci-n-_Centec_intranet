$(document).ready(function() {
listaringresos();
VerificarCaja();
setInterval(function () {
  listaringresos();
  setInterval(function()
  {
      cuadreCaja();
      VerificarCaja();
      VerificarCaja();
  },5000);
}, 60000);
});

function listaringresos()
{
  setTimeout(function()
  {
    $.post( 'listarBasic/listarIngresos.php').done( function(respuesta)
    {
      $('#ingresos').html( respuesta );
    });
  },100,"JavaScript");
}

//sumar
function sumar2()
{
  var b200=$('#txt200').val();
  var b100=$('#txt100').val();
  var b50=$('#txt50').val();
  var b20=$('#txt20').val();
  var b10=$('#txt10').val();
  var b5=$('#txt5').val();
  var b2=$('#txt2').val();
  var b1=$('#txt1').val();
  var b05=$('#txt05').val();
  var b02=$('#txt02').val();
  var b01=$('#txt01').val();

  b200=veri(b200);
  b100=veri(b100);
  b50=veri(b50);
  b20=veri(b20);
  b10=veri(b10);
  b5=veri(b5);
  b2=veri(b2);
  b1=veri(b1);
  b05=veri(b05);
  b02=veri(b02);
  b01=veri(b01);
 total=((parseFloat(b200)*200)+(parseFloat(b100)*100)+(parseFloat(b50)*50) +(parseFloat(b20) * 20)+(parseFloat(b10)*10)  + (parseFloat(b5)*5)+(parseFloat(b2)*2)+(parseFloat(b1)*1)+(parseFloat(b05)*0.5)+(parseFloat(b02)*0.2)+(parseFloat(b01)*0.1)).toFixed(2);
  $('#txttotal').val(total);
}
function veri(valor)
{
  if(valor=="")
  {
    valor=0;
  }
  return valor;
}
//Limpiar
function limpiar()
{
  $('#txt200').val('');
  $('#txt100').val('');
  $('#txt50').val('');
  $('#txt20').val('');
  $('#txt10').val('');
  $('#txt5').val('');
  $('#txt2').val('');
  $('#txt1').val('');
  $('#txt05').val('');
  $('#txt02').val('');
  $('#txt01').val('');
  $('#txttotal').val('0.00');

  $('#btnenvio').removeAttr('disabled');
  $('#txt200').removeAttr('disabled');
  $('#txt100').removeAttr('disabled');
  $('#txt50').removeAttr('disabled');
  $('#txt20').removeAttr('disabled');
  $('#txt10').removeAttr('disabled');
  $('#txt5').removeAttr('disabled');
  $('#txt2').removeAttr('disabled');
  $('#txt1').removeAttr('disabled');
  $('#txt05').removeAttr('disabled');
  $('#txt02').removeAttr('disabled');
  $('#txt01').removeAttr('disabled');

  $('#txtbilletaje').val('');
  $('#txtdiferencia').val('');

  $('#btnCerrarCaja').attr('disabled','disabled');
}
//ver la INFORMACIÓN
function ver()
{
  $('#btnenvio').attr('disabled','disabled');

  $('#txt200').attr('disabled','disabled');
  $('#txt100').attr('disabled','disabled');
  $('#txt50').attr('disabled','disabled');
  $('#txt20').attr('disabled','disabled');
  $('#txt10').attr('disabled','disabled');
  $('#txt5').attr('disabled','disabled');
  $('#txt2').attr('disabled','disabled');
  $('#txt1').attr('disabled','disabled');
  $('#txt05').attr('disabled','disabled');
  $('#txt02').attr('disabled','disabled');
  $('#txt01').attr('disabled','disabled');
  cuadreCaja();
}
function cuadreCaja()
{
  var efectivoReal=$('#txttotal').val();
  $('#txtbilletaje').val(efectivoReal);
  var billetaje=$('#txtbilletaje').val();
  var efectivoSistema=$('#txtmontoDis').val();
  var operacion=(parseFloat(billetaje)-parseFloat(efectivoSistema)).toFixed(2);

  $('#txtdiferencia').val(operacion);
  VerificarCaja();
}
function VerificarCaja()
{
  var resul=$('#txtdiferencia').val();
  resul=parseFloat(resul);
  if(resul==0)
  {
    $('#btnCerrarCaja').removeAttr('disabled');
  }
  else
  {
    $('#btnCerrarCaja').attr('disabled','disabled');
  }
}
function cerrarOficina()
{
  var monto=$('#txtbilletaje').val();
  monto=parseFloat(monto);
  $.post('add/cerrarOfi.php',{monto:monto});
  setTimeout(function()
  {
    cerr();

  },100,"JavaScript");
  swal("ACABA DE CERRAR CAJA","hasta mañana","success");
  location.href='iniOperaciones.php';
}
function cerr()
{
  $('#btnlimpio').attr('disabled','disabled');
  $('#btnCerrarCaja').attr('disabled','disabled');
}
//imprimir
function impre() {
     window.print(ingresos);
}
