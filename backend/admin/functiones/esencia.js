/*
document.oncontextmenu = function() {
      return false
   }
   function right(e) {
      var msg = "No se necesita usar el click derecho. Att. Desarrollador 1";
      if (navigator.appName == 'Netscape' && e.which == 3) {
         //alert(msg); //- Si no quieres asustar a tu usuario entonces quita esta linea...
         return false;
      }
      else if (navigator.appName == 'Microsoft Internet Explorer' && event.button==2) {
        // alert(msg); //- Si no quieres asustar al usuario que utiliza IE,  entonces quita esta linea...
                        //- Aunque realmente se lo merezca...
      return false;
      }
   return true;
}
document.onmousedown = right;
//bloqueo esc
$(document).keydown(function(e) { if (e.keyCode == 27) return false; });
//tecla f12
$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I
        return false;
    }
});*/
//bloqueo de teclas ctrl + u
var isCtrl = false;
document.onkeyup=function(e){
if(e.which == 17) isCtrl=false;
}
document.onkeydown=function(e){
if(e.which == 17) isCtrl=true;
//ctrl  p
if(e.which == 80 && isCtrl == true)
  {

  return false;
  }
  //ctrl + u
  if(e.which == 85 && isCtrl == true)
    {

    return false;
    }
    //ctrl + s
    if(e.which == 83 && isCtrl == true)
      {

      return false;
      }
}
/*
///poner full screen
function alterna_modo_de_pantalla() {
  if ((document.fullScreenElement && document.fullScreenElement !== null) ||    // metodo alternativo
      (!document.mozFullScreen && !document.webkitIsFullScreen)) {               // metodos actuales
    if (document.documentElement.requestFullScreen) {
      document.documentElement.requestFullScreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullScreen) {
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
}*/
//****/
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
function numero2(e) {
    key = e.keyCode || e.whick;
    teclado = String.fromCharCode(key);
    numeros = "0123456789-.";
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
function numi(e) {
    key = e.keyCode || e.whick;
    teclado = String.fromCharCode(key);
    numeros = "0123456789.";
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
$(document).ready(function() {
//alterna_modo_de_pantalla();
lanzar();
setInterval(lanzar, 5000);
setInterval(avi,10000);
});
function avi()
{
  var follo=$('#reconocimiento').val();
  if(follo>0)
  {
    // swal("Tiene deposito, para Confirmar","","success");
  }
}
function lanzar()
{
    //swal("mensaje","","success");
    $.post( 'add/notif.php').done( function( respuesta )
    {
      $( '#notiJuve' ).html( respuesta );
    });
};
function confiB(ide)
{
  $.post( 'add/notif.php', { acept: ide} ).done( function(respuesta)
  {
    $( '#notiJuve' ).html( respuesta );
  });
}
function rechaB(ide)
{
  $.post( 'add/notif.php', { recha: ide} ).done( function(respuesta)
  {
    $( '#notiJuve' ).html( respuesta );
  });
}
function posterB(ide)
{
  $.post( 'add/notif.php', { poste: ide} ).done( function(respuesta)
  {
    $( '#notiJuve' ).html( respuesta );
  });
}
