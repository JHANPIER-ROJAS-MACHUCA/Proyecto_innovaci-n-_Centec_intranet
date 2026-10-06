$(document).ready(function() {
  //contendor();
});
function cliente()
{
  limpiar();
  var id=$('#lstclientes').val();
  creditos(id);

  fechas();
}
//================================================
function creditos(id)
{
  $.post('more/creditos.php',{id:id}).done( function(respuesta)
  {
    $('#lstprestamos').html(respuesta);
  });

}
//===============================================
function fechas()
{
  $('#txtmore').val("");
  $('#moraPendi').val("");
  $('#condonar').val("");
  var id=$('#lstprestamos').val();
  $.post('more/mora.php',{id:id}).done( function(respuesta)
  {
    $('#fechaMor').html(respuesta);
  });
  $.post('more/moretotal.php',{id:id}).done( function(respuesta)
  {
    $('#moraPendi').val(respuesta);
    $('#txtmore').val(respuesta);
  });
}
//===============================================
//marcar chec
function verificaR()
{
  var origen=parseFloat($('#txtmore').val());
  var pro=parseFloat($('#condonar').val());
  if(origen<pro)
  {
    swal("Ha superado la deuda de mora!","","info");
    $('#condonar').val(origen);

  }
}
//===============
function limpiar()
{
  $('#lstprestamos').val("-1");
  $('#txtmore').val("");
  $('#moraPendi').val("");
  $('#condonar').val("");
}
//==========================
//realizar Pago
function condonarMora()
{
  var id=$('#lstprestamos').val();
  var mora=$('#condonar').val();
  mora=asw(mora);
  if(id!="-1" && mora >0)
  {
    $.post('more/condonar.php',{id:id,mora:mora}).done( function(respuesta)
    {
      data = JSON.parse(respuesta);
      console.log(data);
      fechas();      
    });
  }
  else
  {
      swal("Verifique la información","","warning");
  }

}
//==
function asw(code)
{
  resul=0;
  if(code!="")
  {
    resul=code;
  }
  return resul;
}
