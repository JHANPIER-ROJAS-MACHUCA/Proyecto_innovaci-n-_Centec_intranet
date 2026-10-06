$(document).ready(function() {
inicio();
setInterval(function () {
  cerr();
  //alert("hola");
}, 60000);
});
//inicia
function inicio()
{
  var feci=$('#fecha').val();
  $('#fechi').val($('#fecha').val());
  $.post( 'listar/iniOpe.php', { fechi: feci}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
  });
  cerr();
}
function cerr()
{
  var feci=$('#fecha').val();
  $.post('listar/iniOpe2.php',{fecha:feci}).done(function(respuesta)
  {
    $('#cerraronCaja').html(respuesta);
  });
}
//cambia fecha y evalua
function cambio()
{
    $('#fechi').val($('#fecha').val());
    verB();
}
//evalua
function verB()
{
  var fe=$('#fechi').val();
  $.post( 'listar/iniOpe.php', { fechi: fe}).done( function( respuesta )
  {
    $( '#iniciarB' ).html( respuesta );
  });
  cerr();
}
//registra abrir caja nueva
function iniciaB()
{
  var fe=$('#fechi').val();
  var mon=$('#sal').val();
  $.post('add/agreCajaOficina.php',{fechi:fe});//,monto:mon});
  
  //setInterval(verB,1000);
  setTimeout(function()
  {
    verB();
  },100,"JavaScript");
}

//actualizar el
function abrirCaja(id)
{
  $.post('adelete/reabrirCaja.php',{id:id});
  setTimeout(function()
  {
    cerr();
  },100,"JavaScript");
}
//cerar la caja de la oficina
function cerrarOficina()
{
  var monto=$('#montoCierre').val();
  var camino=verr();
  if(camino=="")
  {
    camino=0;
  }
  monto=parseFloat(monto)+parseFloat(camino);
  $.post('add/cerrarOfi.php',{monto:monto});
  setTimeout(function()
  {
    cerr();
      location.href='iniOperaciones.php';
  },100,"JavaScript");
  swal("ACABA DE CERRAR CAJA","hasta mañana","success");

}
function verr()
{
  var montoDispo=$('#saldOPE2').val();
  return montoDispo;
}
