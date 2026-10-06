$(document).ready(function () {
  /*setInterval(function () {
    btn();
  }, 10);*/
  opera();
  solicitudes();
});
/*setTimeout(function()
   {

   },510,"JavaScript");*/
//listar las solicitudes de operacion de extorno
function solicitudes() {
  $.post('listarBasic/listarPedi.php').done(function (respuesta) {
    $('#extor1').html(respuesta);
  });
}
function opera() {
  $.post('listarBasic/listarOpera.php').done(function (respuesta) {
    $('#extor').html(respuesta);
  });
}
// function extor(id) {
//   $.post('../app/api/eliminarTransaccion.php', { id: id })
//     .done(function (e) {
//       const data = JSON.parse(e);
//       if (data.success) {
//         swal(data.message, "", "success");
//       } else {
//         swal(data.message, "", "error");
//       }
//     }).fail(function () {
//       swal("Se produjo un error desconocido", "", "error");
//     });
//   setTimeout(function () {
//     opera();
//   }, 1000, "JavaScript");
// }
//buscador
function buscar() {
  var buscar = $('#txtbuscar').val();
  var start = $('#textdatestart').val();
  var end = $('#textdateend').val();
  console.log(start, end);

  $.post('listarBasic/listarOpera.php', { buscar, start, end }).done(function (respuesta) {
    $('#extor').html(respuesta);
  });
}
//elimianr aceptar extonro de solicitud
function confiSoli(id, identifi) {
  $.post('../app/api/eliminarTransaccion.php', { id: id })
    .done(function (e) {
      const data = JSON.parse(e);
      if (data.success) {
        swal(data.message, "", "success");
      } else {
        swal(data.message, "", "error");
      }
    }).fail(function () {
      swal("Se produjo un error desconocido", "", "error");
    });
  // $.post('adelete/confiSoli.php', { id: identifi });
  swal("Se hizo la anulación de la operación", "", "success");
  setTimeout(function () {
    solicitudes();
  }, 1000, "JavaScript");

}
