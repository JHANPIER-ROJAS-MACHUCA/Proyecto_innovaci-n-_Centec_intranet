//numeros
function numero(e) {
  key = e.keyCode || e.whick;
  teclado = String.fromCharCode(key);
  numeros = "0123456789";
  especiales = "8-46";
  teclado_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      teclado_especial = true;
    }
  }
  if (numeros.indexOf(teclado) == -1 && !teclado_especial) {
    return false;
  }
}

function envioInfo() {
  var usu = $('#txtusuario').val();
  var pas = $('#txtpassword').val();
  $.post('/Centecp_Intranet/backend/public/index.php/api/auth/login', { usu: usu, pas: pas }).done(function (respuesta) {
    if (String(respuesta).trim() == 1) {
      location.href = '/Centecp_Intranet/frontend/'
    } else {
      swal({
        title: "Hola!! algo anda mal con sus credenciales, verifique su información!",
        icon: 'error'
      });
    }
  });
}
function tecleo(event) {
  var codigo = event.which || event.keyCode;
  //console.log("Presionada: " + codigo);
  if (codigo === 13) {
    envioInfo();
  }
}
