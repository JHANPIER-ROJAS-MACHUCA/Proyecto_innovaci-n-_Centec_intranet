$(document).ready(function() {
  mostrarRegistro();
});
//=====================================
//Tipo
function cambioTipo()
{
  var dato= $('#chktipo').prop('checked');
  if(dato)
  {
    $('#tipoM').html('Egreso');
  }
  else
  {
     $('#tipoM').html('Ingreso');
  }
}
//=====================================
//gurdar motivo
function guardarMotivo()
{
  var id=$('#txtidmoti').val();
  var motivo=$('#txtmotivo').val();
  var tipo=validarTipo($('#chktipo').prop('checked'));

  if(motivo!="")
  {
    $.post('modulOperacion/add.php',{id:id,motivo:motivo,tipo:tipo}).done( function(respuesta)
    {
      mostrarRegistro();
      limpiarMotivo();
    });
  }
  else
  {
      swal("Complete la información","","info");
  }
}
//=====================================
function validarTipo(tipo)
{
  resul=1;
  if(tipo)
  {
    resul=2;
  }
  return resul;
}
//=====================================
function mostrarRegistro()
{
  $.post('modulOperacion/motivos.php').done( function(respuesta)
  {
    $('#listarMotivos').html(respuesta);
  });
}
//=====================================
//eliminar
function eliminarMotivo(codigo)
{
  $.post('modulOperacion/delete.php',{id:codigo}).done( function(respuesta)
  {
    $('#moti'+codigo).toggle();
  });
}
//=====================================
//editar
function editarMoti(id,mo,tipo)
{
  limpiarMotivo();
  $('#txtidmoti').val(id);
  $('#txtmotivo').val(mo);
  var tipo1=validarTipo($('#chktipo').prop('checked'));
  if(tipo!=tipo1)
  {
    if(tipo==1)
    {
      swal("Coloque el tipo en Ingreso","","info");
    }
    else
    {
      swal("Coloque el tipo en Egreso","","info");
    }
  }

}
//=====================================
//limpiar
function limpiarMotivo()
{
  $('#txtidmoti').val('');
  $('#txtmotivo').val('');

}
//=====================================
//=====================================
//=====================================
