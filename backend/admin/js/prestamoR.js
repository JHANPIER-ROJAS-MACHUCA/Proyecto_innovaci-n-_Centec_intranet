//pasar nombre
$(function () {
  var tipoa = $(this).val();
  $.post('listar/listarPrestR.php').done(function (respuesta) {
    $('#listarA').html(respuesta);
  });
});

function confirmar(id) {
  var dato = $('#' + id);
  // var monto = prompt("Ingrese el monto aprobado");
  monto = parseInt(prompt("Ingrese el monto aprobado"));
  if (!/^([0-9])*$/.test(monto)) {
    alert("El valor " + monto + " no es un número");
  }
  else {
    $.post('adelete/updatePrestamo.php', { id: id, monto: monto });
    swal("Confirmacion con exito", "", "success");
    setTimeout(function () {
      $.post('listar/listarPrestR.php').done(function (respuesta) {
        $('#listarA').html(respuesta);
      });
    }, 100, "JavaScript");
  }
}

// function imprimir_prestamo(idprestamo) {
//   VentanaCentrada('./pdf/documentos/ver_prestamo.php?idprestamo=' + idprestamo, 'Prestamo', '', '1024', '768', 'true');
// }
function imprimir_prestamo(idprestamo) {
  VentanaCentrada('../app/pdf/cartillaPago.php?creditId=' + idprestamo, 'Prestamo', '', '1024', '768', 'true');
}
function imprimir_pagare(idprestamo) {
  VentanaCentrada('./pdf/documentos/ver_pagare.php?idprestamo=' + idprestamo, 'Pagare', '', '1024', '768', 'true');
}
function imprimir_contrato(idprestamo) {
  VentanaCentrada('./pdf/documentos/ver_contrato.php?idprestamo=' + idprestamo, 'Contrato', '', '1024', '768', 'true');
}
function imprimir_contrato_nuevo(idprestamo) {
  VentanaCentrada('./contrato.php?creditId=' + idprestamo, 'Contrato', '', '1024', '768', 'true');
}
function imprimir_voucher(idprestamo) {
  VentanaCentrada('../app/pdf/voucherDesembolso.php?creditId=' + idprestamo, 'Contrato', '', '1024', '768', 'true');
}
function imprimir_letra(idprestamo) {
  VentanaCentrada('../app/pdf/letraCredit.php?creditId=' + idprestamo, 'Contrato', '', '1024', '768', 'true');
}
function imprimir_contrato_blank() {
  VentanaCentrada('./contrato_blank.php', 'Contrato en blanco', '', '1024', '768', 'true');
}
function imprimir_historial(idprestamo) {
  VentanaCentrada('../app/pdf/resumenCredito.php?creditId=' + idprestamo, 'Historial', '', '1024', '768', 'true');
}
function imprimir_detalle_pago(idprestamo) {
  VentanaCentrada('../app/pdf/resumenCredito.php?creditId=' + idprestamo, 'Resumen', '', '1024', '768', 'true');
}
/***eliminar****/
function denegar(id) {
  var person = prompt("Ingrese el motivo de la denegación del prestamo.");
  while (person == "") {
    person = prompt("Ingrese el motivo de la denegación del prestamo.");
  }
  if (person == undefined) {
  } else if (person != "") {
    $.post('adelete/denegarPrestamo.php', { id: id, motivo: person }).done(function (respuesta) {
      swal(respuesta, "", "success");
    });
    //$.post('adelete/denegarPrestamo.php', {id:id,motivo:person});


    setTimeout(function () {
      actualizar();
    }, 1000, "JavaScript");
    //melas();
  }
}

$('#confirmarPrestamo').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var id = button.data('id')
  $('#edit_id').val(id)
  var montoa = button.data('montoa')
  $('#edit_montoa').val(montoa)
  var montop = button.data('montop')
  $('#edit_montop').val(montop)
  var montop2 = button.data('montop')
  $('#edit_montop2').val(montop2)
  var taza = button.data('taza')
  $('#edit_taza').val(taza)

})

$("#edit_prestamo").submit(function (event) {
  event.preventDefault();

  const formData = new FormData();
  formData.append('edit_id', $('#edit_id').val());
  formData.append('edit_montoa', $('#edit_montop2').val());
  formData.append('edit_taza', $('#edit_taza').val());

  fetch("../app/api/confirmarPrestamo.php", {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      swal({
        title: data.message,
        icon: data.success ? 'success' : 'error'
      })
        .then(_ => {
          if (data.success) {
            window.location.reload();
          }
        });
    });
});
//==============================================================
//cambiar de locacion
function campero(id) {
  var valor = 2;
  var fin = 2;
  var estado = $('#txtconte' + id).val();
  var title = "Pagara en Oficina";
  var icone = "fa fa-university";
  var colorear = "red";
  if (estado == "1") {
    valor = 1;
  }
  else {
    fin = 1;
    title = "Pagara en Campo";
    icone = "fa fa-pied-piper-alt";
    colorear = "green";
  }
  $.post('add/camp.php', { codi: id, valor: valor });
  $('#txtconte' + id).val(fin);
  $('#btncampo' + id).attr("title", title);
  $('#btncampo2' + id).attr("style", "color:" + colorear);
  $('#btncampo2' + id).attr("class", icone);
}
//==================================================================
//cambios
function cambiasoSo(id, tza, pago, plzo, fecha, fechaF, monto, cuota) {
  $('#codi').val(id);
  $('#txtporcenta').val(tza);
  $('#lstpago').val(pago);
  $('#txtplazom').val(plzo);
  $('#txtfechaDesembolso').val(fecha);
  $('#txtfechaPago').val(fechaF);
  $('#txtmonto').val(monto);
  $('#txtcuota').val(cuota);
  if (pago == "3") {
    $('#fecha').attr("style", "");
  }
}
//========================
//cambiaso
//////

$('#cambiosSarpado').on('submit', (function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  if (verificador()) {
    // $.ajax({
    //   type: 'POST',
    //   url: "cone/cambio.php",
    //   data: formData,
    //   cache: false,
    //   contentType: false,
    //   processData: false
    // });
    swal("Información Actualizada", "Correctamente", "success");

  }
  else {
    swal("Complete la Información", "que esta marcada con *", "info");
  }
}));
function verificador() {
  var resul = false;
  var valor = 0;
  valor += verifica23('txtcuota');
  valor += verifica23('txtporcenta');
  valor += verifica23('txtfechaDesembolso');
  valor += verifica23('txtmonto');
  valor += verifica23('lstpago');
  valor += verifica23('txtplazom');

  var tipo = vl('lstpago');

  if (tipo == 3) {
    valor += verifica23('txtfechaPago');
  }
  if (valor == 0) {
    resul = true;
  }
  return resul;
}
//generar los cambios ultimos

function monti() {
  var monto = $('#txtmonto').val();
  var tasa = $('#txtporcenta').val();
  var pago = $('#lstpago').val();
  var cuota = 0;
  var tot = 0;
  if (pago == "3") {
    $('#txtplazom').val('1');
    $('#fecha').attr("style", "");
    $('#txtfechaPago').val('');
  }
  else {
    $('#fecha').attr("style", "display:none");
    $('#txtfechaPago').val('');
  }
  var plazo = $('#txtplazom').val();
  if (monto != "" && tasa != "" && pago != null && plazo != "") {
    cuota = parseFloat(monto) + parseFloat(monto * (tasa / 100));
    cuota = Math.ceil(cuota).toFixed(2);
    tot = cuota;
    cuota = parseFloat(cuota / plazo).toFixed(2);
    cuota *= 10;
    [cuota, tot] = dragonite(tot, cuota, plazo);
  }

  $('#txtcuota').val(cuota);
}
//verficicamos si el ultimo es negativo y corregimos
function dragonite(princi, cuota, plazo) {
  var monto = cuota;
  cuota = Math.ceil(cuota);
  cuota = (cuota / 10).toFixed(2);
  tot = ultimo(princi, cuota, plazo);
  if (tot < 0) {
    cuota = Math.floor(monto);
    cuota = (cuota / 10).toFixed(2);
    tot = ultimo(princi, cuota, plazo);
  }
  return [cuota, tot];
}
//veriicamos el monto final
function ultimo(princi, cuota, plazo) {
  var res = cuota;
  if (plazo > 1) {
    res = cuota * (plazo - 1);
    tot = parseFloat(princi - res).toFixed(2);
  }
  else {
    tot = parseFloat(res).toFixed(2);
  }
  return tot;
}
