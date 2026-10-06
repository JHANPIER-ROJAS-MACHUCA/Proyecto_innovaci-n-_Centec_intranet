$(document).ready(function() {
llamado();
setInterval(function () {
  llamado();
  //alert("hola");
}, 60000);
});
//verificar si ya se le aprobo su billetaje
//listaringresos

//obtener el lso valores de cada uno de los billetes registrados en billetaje
function llamado()
{
  $.post('add/btn.php',
  function(data)
  {
    $('#txt200').val(data.b200);
    $('#txt100').val(data.b100);
    $('#txt50').val(data.b50);
    $('#txt20').val(data.b20);
    $('#txt10').val(data.b10);
    $('#txt5').val(data.b5);
    $('#txt2').val(data.b2);
    $('#txt1').val(data.b1);
    $('#txt05').val(data.b05);
    $('#txt02').val(data.b02);
    $('#txt01').val(data.b01);
    $('#txttotal').val(data.total);
    if(data.total)
    {
      $('#btnenvio').attr('disabled','disabled');
      $('#btnlimpio').attr('disabled','disabled');
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
    }
    else
    {
        $('#btnenvio').removeAttr('disabled');
        $('#btnlimpio').removeAttr('disabled');
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
    }
    if(data.estado==1)
    {
      listaringresos();
    }
  },"json");
}
function avance(id)
{
  var valor="A";
  if(id)
  {
    valor=id;
  }
  return valor;
}

//enviar a
function enviar1()
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

  var total=$('#txttotal').val();
  total=veri(total);
  if(total>0)
  {
    $.post( 'add/agreBilletaje.php',{b200:b200,b100:b100,b50:b50,b20:b20,b10:b10,b5:b5,b2:b2,b1:b1,b05:b05,b02:b02,b01:b01,total:total}, function( data )
    {
      if(data.resultado)
      {
        swal("Billetaje registrado.","A espera de Confirmación","success");
        llamado();
      }
      else {
        swal("La operacion no procede","","warning");
      }
    },"json");
  }
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
//ahora cerramos caja pe mascota
function cerrarCaja()
{
  var monto=$('#txtmontoDis').val();
  $.post( 'add/cerrarCaja.php', { monto: monto} );
  llamado();
  swal("ACABA DE CERRAR CAJA","hasta mañana","success");
}
