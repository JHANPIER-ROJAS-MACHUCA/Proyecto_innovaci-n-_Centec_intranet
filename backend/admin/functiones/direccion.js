$(document).ready(function() {
direccion1();
inic();
inicq();
negocios();
});
function inic()
{
  var ide=$('#idClie').val();
  $.post( 'listar/listarPres.php',{id:ide} ).done( function(respuesta)
  {
    $( '#resulPre' ).html( respuesta );
  });
}
//DIRECCION
//caragr las direcciones
function direccion1()
{
  var ide=$('#idClie').val();
  if(ide!="")
  {
    $.post( 'listar/listarDirecCLiente.php', { ide: ide } ).done( function(respuesta)
    {
      $( '#listarDirec' ).html( respuesta );
    });
  }
};

//limpiar los campos
function limpiar()
{
    $('#txtdepa').val('');
    $('#txtprovi').val('');
    $('#txtdis').val('');
    $('#lugar').val('');
    $('#direccion').val('');
    $('#referencia').val('');
};
//guardar los campos
function enviar()
{
  var de=$('#txtdepa').val();
  var prov=$('#txtprovi').val();

  var dis=$('#txtdis').val();
  var lug=$('#lugar').val();
  var dir=$('#direccion').val();
  var ref=$('#referencia').val();
  var ide=$('#idClie').val();
  if(de!="" && prov!="" && dis!="" && lug!="" && dir!="" && ref!="")
  {
    if(ide!="")
    {
      $.post( 'add/agreDirec.php', { ide: ide,dis: dis,ane: lug,dir: dir,ref: ref });
    }
    limpiar();

    setTimeout(function()
    {

      direccion1();
    },100,"JavaScript");
  }
  else
  {
    swal("Complete toda la información","","warning");
  }
}
//ELIMINAR EL LA Direccion
function eli(id)
{
  if(id!="")
  {
    $.post( 'adelete/deleteDirec.php', { ide: id});
  }
  swal("Se elimino correctamente","","succcess");
  setTimeout(function()
  {
    direccion1();
  },100,"JavaScript");
}
//caragr Negocios
function negocios()
{
  var ide=$('#idClie').val();
  if(ide!="")
  {
    $.post( 'listar/listarNegoCliente.php', { ide: ide } ).done( function(respuesta)
    {
      $( '#listarNegoClie' ).html( respuesta );
    });
  }
}
//enviar negocios
function enviar1()
{
  var ide=$('#idClie').val();
  var direc=$('#direccion2').val();
  var tipo=$('#txttipo1').val();
  var negocio=$('#txttipoNego').val();
  var local=$('#txttipoLocal').val();
  var tiempo=$('#txttiempo').val();
  if(direc!="" && tipo!="" && negocio!="" && local!="" && tiempo!="")
  {
    if(ide!="")
    {
      $.post( 'add/agreNego.php', { ide: ide,dire: direc,tipo: tipo,negocio: negocio,local: local,tiempo:tiempo });
    }
    linNego();

    setTimeout(function()
    {
      negocios();
    },100,"JavaScript");
  }
  else
  {
    swal("Complete toda la información","","warning");
  }
}
//limpiar negocio
function linNego()
{
  $('#direccion2').val('');
  $('#txttipo1').val('');
  $('#txttipoNego').val('');
  $('#txttipoLocal').val('');
  $('#txttiempo').val('');
}
//ELIMINAR El negocio
function eli1(id)
{
  if(id!="")
  {
    $.post( 'adelete/deleteNego.php', { ide: id});
  }
  setTimeout(function()
  {
    negocios();
  },100,"JavaScript");
}
//caragr historial de
function inicq()
{
  var ide=$('#idClie').val();
  $.post( 'listar/listarPrestamos.php', { id: ide} ).done( function(respuesta)
  {
   $( '#HistoPrestamo' ).html( respuesta );
  });
}
