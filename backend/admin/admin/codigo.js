$(document).ready(function() {
  contendor();
});
function contendor()
{
  montoDia();
  verInicio();
  fechaActual();
  inicioOperacionesAdmin();
  verRegistroDesignado();
  cajaCerrado();
  ///cierreDeOficinas();
}
//cargar el inicio de operaciones
function inicioOperacionesAdmin()
{
  var fecha=$('#fecha').val();
  $.post('admin/abrir/iniciarOperaciones.php',{fecha:fecha}).done( function(respuesta)
  {
    $('#iniOperacionesF').html(respuesta);
  });
}
//fecha abierta
function fechaActual()
{
  $.post('admin/abrir/fechaActual.php').done( function(respuesta)
  {
    var ca = respuesta.split("|");
    $('#fechaAbierta').html(ca[0]);
    $('#idCO').val(ca[1]);
    setTimeout(function()
     {
        VerSAldoAbierto(ca[1]);
     },100,"JavaScript");
  });
}
//monto del dia
function montoDia()
{
  $.post('admin/abrir/anterior.php').done( function(respuesta)
  {
    $('#sald').val(respuesta);
  });
}
//VER EL SALDO RESPECTIVO AL DIA SI ESTA ABIERTO LA CAJA
function VerSAldoAbierto(valor)
{
  $.post('admin/abrir/saldo.php',{valor:valor}).done( function(respuesta)
  {
    $('#saldOPE').html(respuesta);
    $('#txtsaldoOperativo').val(respuesta);
  });
}
//gerente
function abrirDia()
{
  $.post('admin/abrir/iniciarDia.php').done( function(respuesta)
  {
    setTimeout(function()
     {
         contendor();
     },100,"JavaScript");

    swal(respuesta,"","info");
  });
}
//se abrio con la FECHA
function seac()
{
  var fecha=$('#fecha').val();
  $.post('admin/abrir/montoDeFecha.php',{fecha:fecha}).done( function(respuesta)
  {
    $('#txtabrioCon').html(respuesta);
  });
}
//designar dinero
function verInicio()
{
  var fecha=$('#fecha').val();
  $.post('admin/abrir/iniciar.php',{fecha:fecha}).done( function(respuesta)
  {
    $('#contenidoApertura').html(respuesta);
    setTimeout(function()
     {
         seac();
         inicioOperacionesAdmin();
         verRegistroDesignado();
         cajaCerrado();
         detalleAdminCierre();
     },100,"JavaScript");
  });
  //cierreDeOficinas();
}
//verificar que el saldo no exeda a la cantidad brindada
function veriDiner()
{
  var cantidad=convertidor($('#txtsaldoOperativo').val());
  var brindar=convertidor($('#MontOPE').val());
  if(cantidad<brindar)
  {
    swal("El monto supera el Saldo Actual","","info");
    $('#MontOPE').val(cantidad);
  }
}
function convertidor(valor)
{
  resul=0;
  if(valor!="")
  {
    resul=parseFloat(valor);
  }
  return resul;
}
//****************************************************************************************
//operaciones de desgindacion
function limpiarCampos()
{
    $('#UsuariOPE').val('-1');
    $('#MontOPE').val('');
}
//agregar designacion
function darDesignacion()
{
  var ide=$('#UsuariOPE').val();
  var monto=$('#MontOPE').val();
  var valor=$('#idCO').val();

  monto=convertidor(monto);
  if(monto>0 && ide!=-1 && ide!=null  && valor!="")
  {
    $.post('admin/abrir/designar.php',{valor:valor,idU:ide,monto:monto}).done( function(respuesta)
    {
      limpiarCampos();
      swal("Operacion realizada","","");
      setTimeout(function()
       {
           VerSAldoAbierto(valor);
           verRegistroDesignado();
       },100,"JavaScript");
    });
  }
  else
  {
      swal("Verifique los datos!","","info");
  }
}
//ver registro
function verRegistroDesignado()
{
  var fecha=$('#fecha').val();
  $.post('admin/abrir/registro.php',{fecha:fecha}).done( function(respuesta)
  {
    $('#contenidoRegistroDesignacion').html(respuesta);
    setTimeout(function()
     {
         //VerSAldoAbierto(valor);
     },100,"JavaScript");
  });
}
//elimar designacion
function Elimi(valor)
{
  $('#desig'+valor).toggle();
  $.post('admin/abrir/delete.php',{codigo:valor}).done( function(respuesta)
  {
    if(respuesta==1)
    {
      swal("Designacion eliminada!","","info");
    }
    else {
      swal("No se pudo eliminar","ya se confirmo la designacion","warning");
    }
  });
}
///*******************cierre de CAJa
function cajaCerrado()
{
  var fecha=$('#fecha').val();
  $.post('admin/cierre/cierreUsuarios.php',{fecha:fecha}).done( function(respuesta)
  {
    $('#cerraronCaja').html(respuesta);
     setTimeout(function()
       {
        actulizarSaldoCierre();
       },100,"JavaScript");
  });
}
//optener el saldo correspondiente
function actulizarSaldoCierre()
{
  var fecha=$('#fecha').val();
  $.post('admin/cierre/saldo.php',{fecha:fecha}).done( function(respuesta)
  {
    var a = respuesta.split("|");
    $('#montoCierre').val(a[1]);
    if(a[0]==0 || a[0]==1)
    {
      $('#btnContinuar').removeAttr("style");
    }
    else {
      $('#btnContinuar').attr("style","display:none");
    }
  });
}
///******************reabirr caja de UsuariOPE
function reabrirCaja(valor)
{
  $('#caja'+valor).toggle();
  $.post('admin/cierre/reabrir.php',{codigo:valor}).done( function(respuesta)
  {
    if(respuesta==1)
    {
      swal("Caja Abierta!","","info");
      setTimeout(function()
        {
         actulizarSaldoCierre();
        },100,"JavaScript");
    }
  });
}
//detelle de cieere de caja
function detalleAdminCierre()
{
  var fecha=$('#fecha').val();
  var bille=$('#txtbilletajeAdmi').val();
  var id=$('#idCO').val();
    $.post('admin/cierre/detalle.php',{fecha:fecha,billetaje:bille,id12:id}).done( function(respuesta)
    {
      $('#detalleAdmin').html(respuesta);
    });
}
//cerrar la caja del adminsitrador
function cerrarCajaAdministrador()
{
  var id=$('#idCO').val();
  var monto=$('#txtbilletaje').val();
  if(id!="")
  {
    $.post('admin/cierre/cerrarCaja.php',{idOfi:id,monto:monto}).done( function(respuesta)
    {
      $('#btnCerrarDia').toggle();
      swal(respuesta,"","info");
    });
  }
}
