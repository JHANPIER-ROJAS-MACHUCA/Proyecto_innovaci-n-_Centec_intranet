$(document).ready(function() {
  inicio();

 confiMotivo("1");
 cargarDias();
});
//guardar motivo
function guardarMotivo()
{
  var motivo=$('#txtmotivo').val();
  var monto=$('#txtmontoMotivo').val();
  var fecha=veri($('#chkfechas').prop('checked'));
  var id=$('#txtidmotivo').val();
  if(motivo!="" && monto!="")
  {
    $.post( 'add/agregarMotivo.php',{motivo:motivo,fecha:fecha,monto:monto,id:id}, function( data )
    {
      if(data.resultado)
      {
        limpiar();
        inicio();
        swal("Motivo registrado.","","success");
      }
      else {
        swal("La operacion no pudo proceder","ya existe un motivo similar","warning");
      }
    },"json");
  }
  else {
    swal("Complete la información","las fechas no son necesarias","info");
  }

}

function limpiar()
{
  $('#txtmotivo').val("");
  $('#txtmontoMotivo').val("");
  $('#txtidmotivo').val("");
}
function inicio()
{
  $.post( 'motivos/listarMotivos.php' ).done( function(respuesta)
  {
    $( '#listarMotivos' ).html( respuesta );
  });
}
//==========================
//eidtar motivo
function editarMotivo(dato,motivo,valor)
{
  $('#txtidmotivo').val(dato);
  $('#txtmotivo').val(motivo);
  $('#txtmontoMotivo').val(valor);
}
//=============================
function eliminarMotivo(dato)
{
  $.post( 'motivos/deleteMotivo.php',{id:dato}, function( data )
  {
    if(data.resultado)
    {
      limpiar();
      inicio();
      swal("Motivo eliminado.","satisfactoriamente","success");
    }
    else {
      swal("La operacion no pudo proceder","","warning");
    }
  },"json");
}
//fechas del motivos

function confiMotivo(dato)
{
  $.post( 'motivos/listarFechas.php',{id:dato} ).done( function(respuesta)
  {
    $( '#configurarMotivo' ).html( respuesta );
    $('#txtidentificador').val(dato);
  });
}

//obtner dia de Inicio
function inicioMotivo()
{
  var fin=$('#txtfechaFinalizar').val();
  var cantidad=$('#txtcantidadDias').val();
  var identificador=$('#txtidentificador').val();
  if(fin!="" && cantidad!="" && identificador!="")
  {
    $.post("motivos/calularInicio.php", {inicio:fin,canti:cantidad,id:identificador}, function(data){
      // Si devuelve un apellido lo mostramos, si no, vaciamos la casilla
      if(data.fecha1)
      {
        $("#txtfechaIniciar").val(data.fecha1);
        cargaMeses(identificador);
      }
      else
      {
        $("#txtfechaIniciar").val("");
      }
    },"json");
  }
}
//cargar los Meses
function cargaMeses(dato)
{
  $.post( 'motivos/meses.php',{id:dato} ).done( function(respuesta)
  {
    $('#lstmeses').html( respuesta );
  });
}

//CARGAR Dias
function cargarDias()
{
  var dato=$('#lstmeses').val();
  $.post( 'motivos/listarDias.php',{id:dato} ).done( function(respuesta)
  {
    $('#diasP').html( respuesta );
  });
}
//cambio de diasP
function cambioDia()
{
  var mes=$('#lstmeses').val();
  if(mes!="-1")
  {
    var dom=veri($('#dom').prop('checked'));
    var  lu=veri($('#lu').prop('checked'));
    var ma=veri($('#ma').prop('checked'));
    var mi=veri($('#mi').prop('checked'));
    var ju=veri($('#ju').prop('checked'));
    var vi=veri($('#vie').prop('checked'));
    var sab=veri($('#sab').prop('checked'));
   $.post('motivos/cambioDias.php', { id:mes,dom:dom,lu:lu,ma:ma,mi:mi,ju:ju,vi:vi,sab:sab});
   $('#lstmeses').val(-1);
   cargarDias();
   setTimeout(function()
   {
     inicioMotivo();
   },200,"JavaScript");
  }

}
function veri(valor)
{
  resul="0";
  if(valor)
  {
    resul="1";
  }
  return resul;
}

/*******************************************/

///*****************************************
function imprimir_prestamo(){
  var id=$('#lstclientes').val();
  var canasta=$('#txtcantidadDias').val();
  VentanaCentrada('./pdf/documentos/ver_motivo.php?idCliente='+id+'j'+canasta,'Motivo','','1024','768','true');
}
