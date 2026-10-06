//*-----------------------------------*
//verificacion de avanze
$(document).ready(function () {
  var elem = document.querySelector('.js-switch');
  var switchery = new Switchery(elem, { color: '#23c6c8' });

  var elem_2 = document.querySelector('.js-switch_2');
  var switchery_2 = new Switchery(elem_2, { color: '#23c6c8' });

  var elem_3 = document.querySelector('.js-switch_3');
  var switchery_3 = new Switchery(elem_3, { color: '#23c6c8' });
});
//fechas
$('.datepicker').bootstrapMaterialDatePicker({
  weekStart: 0,
  time: false,
  format: 'DD/MM/YYYY'
});
//barra
function avanze() {
  var limi = parseInt($('#conteo').html());

  ca = limi + 10;
  var va = ca + "%";
  document.getElementById("canti").style.width = va;
  $('#conteo').html(ca);
}
//visuslizar la barra de porcentaje
function ver() {
  $('#barra').toggle();
}
//obtener el la cuota a pagar

function monti() {
  var monto = $('#txtmonto').val();
  $('#txtmora').val(mora(monto));
  var tasa = $('#txtinteres').val();
  var pago = $('#txtpago').val();
  var cuota = 0;
  var tot = 0;
  if (pago == "3") {
    $('#txtplazo').val('1');
    $('#AfechaP').val('');
  }
  else {
    $('#AfechaP').val('');
  }
  var plazo = $('#txtplazo').val();
  if (monto != "" && tasa != "" && pago != null && plazo != "") {
    cuota = parseFloat(monto) + parseFloat(monto * (tasa / 100));
    //cuota*=10;
    cuota = Math.ceil(cuota).toFixed(2);
    //cuota=(cuota/10);
    tot = cuota;
    /*if(pago=='1')
    {
      cuota=parseFloat(cuota /plazo).toFixed(2);
      console.log(cuota);
    }
    else if(pago=="2")
    {
      //plazo*=7;
      cuota=parseFloat(cuota /plazo).toFixed(2);
    }
    else if(pago=="3")
    {
      cuota=parseFloat(cuota /plazo).toFixed(2);
    }*/
    cuota = parseFloat(cuota / plazo).toFixed(2);


    cuota *= 10;
    [cuota, tot] = dragonite(tot, cuota, plazo);
  }

  $('#txtcuota').val(cuota);
  $('#txtcuotaf1').val(cuota);
  $('#txtcuotaF').val(tot);
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

function mora(monto) {
  var mon = 0.00;
  if (monto != "") {
    mon = parseFloat(monto);
    if (mon <= 500) {
      mon = 0.50;
    }
    else if (mon > 500 && mon <= 1000) {
      mon = 1.00;
    }
    else if (mon > 1000) {
      mon = 2.00;
    }
  }
  return mon.toFixed(2);
}
//imagen de NEGOCIO
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('#img1').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#img").change(function () {
  readURL(this);
});
//imagen de Domicilio
function readURL1(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('#img3').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#img2").change(function () {
  readURL1(this);
});
//croquis AVAL
function readURL2(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('#img5').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#img4").change(function () {
  readURL2(this);
});
//reconocer si es Prendatario
function detal() {
  var det = $('#txttipoPresta').val();
  if (det == '3') {
    $('#btn1').attr("href", "#tab-a6");
    $('#btn4').attr("href", "#tab-a3");
    $('#btn5').attr("href", "#tab-a6");
    /*$('#fechiPa').attr("style","");

    $("#txtpago").val("3");
    $("#txtplazo").val("1");*/
    monti();
  }
  else {
    $('#btn1').attr("href", "#tab-a4");
    $("#txtpago").val("1");
    $('#btn5').attr("href", "#tab-a5");
    $('#AfechaP').val('');
  }
}
//agregar prestamo Nuevo
$('#agrePresta').on('submit', (function (e) {
  e.preventDefault();
  var tipoP = $('#txttipoPresta').val();
  var monto = $('#txtmonto').val();
  var interes = $('#txtinteres').val();
  var pagos = $('#txtpago').val();
  var cuotas = $('#txtplazo').val();
  var pagoporcuota = $('#txtcuotaf1').val();
  var mora = $('#txtmora').val();
  var fechalimit = $('#AfechaP').val();
  var userId = $("#user_id").val();
  //conyugue
  var dniC = $('#txtdni').val();
  var apC = $('#txtap').val();
  var amC = $('#txtam').val();
  var nomC = $('#txtnom').val();
  var sexo = $('#sexo').val();
  var nac = $('#Afecha').val();
  var croNego = $('#img').val();
  var croDom = $('#img2').val();
  //aval
  var dniA = $('#txtdni1').val();
  var apA = $('#txtap1').val();
  var amA = $('#txtam1').val();
  var nomA = $('#txtnom1').val();
  var direcA = $('#txtdirec').val();
  var cel = $('#txtcel').val();
  var ocu = $('#txtocu').val();
  var direcTra = $('#txtdirecTra').val();
  var croA = $('#img4').val();
  //prendatario
  var decripG = $('#txtdescrip').val();
  var obsG = $('#txtobser').val();
  //documentos
  var docu = $('#txtdocus').val();
  var luz = $('#txtluz').prop('checked');
  var alqui = $('#txtalqui').prop('checked');
  var foto = $('#txtfoto').prop('checked');
  var descripD = $('#txtdecrip1').val();

  //ejecutor
  var unico = 0;

  //-**************//
  //**documentos importantes
  //if(docu==""){unico=5;}
  //

  //***AVAL
  if (dniA != "") {
    if (dniA.length == 8) {
      //datos
      if (apA == "") { unico = 3; }
      if (amA == "") { unico = 3; }
      if (nomA == "") { unico = 3; }
      //direccion
      if (direcA == "") { unico = 3; }
      //cel
      if (cel == "") { unico = 3; }
    }
    else {
      unico = 3;
    }
  }
  //conyugue
  if (dniC != "") {
    if (dniC.length == 8) {
      //Apellidos
      if (apC == "") { unico = 2; }
      if (amC == "") { unico = 2; }
      //Nombres
      if (nomC == "") { unico = 2; }
      //Sexo
      if (sexo == "") { unico = 2; }
    }
    else {
      unico = 2;
    }
  }
  //cuotas
  if (cuotas != "") { if (cuotas == 0) { unico = 1; } } else { unico = 1; }
  //Monto
  if (monto != "") { if (monto == 0) { unico = 1; } } else { unico = 1; }
  //Interes
  if (interes != "") { if (interes == 0) { unico = 1; } } else { unico = 1; }
  //mora
  if (mora != "") { if (mora == 0) { unico = 1; } } else { unico = 1; }
  //Pagos
  if (pagos == "") { unico = 1; }
  //tipoP
  if (tipoP == 3) {
    if (fechalimit == "") { unico = 6 }
    /*descripcion nula*/
    if (decripG == "") { unico = 4; }
  };

  var identificado = $('#idClie').val();
  if (unico == 0) {
    var formData = new FormData(this);
    $.ajax({
      type: 'POST',
      url: '../app/api/generarPrestamo.php?identi=' + identificado,
      data: formData,
      cache: false,
      contentType: false,
      processData: false

    });


    $('#btnInput').attr('type', 'hidden');

    swal("La información se guardo correctamente", "Att. Desarrollador", "success");
    //.attr("href","#tab-a6");
  }
  else {
    switch (unico) {
      case 1:
        swal("Los campos en donde se coloca el monto del prestamo no pueden tener valor 0 o vacio", "completelos por favor, vuelva atrás", "warning");
        break;
      case 2:
        swal("Termine de completar los campos de conyugue", "completelos por favor, vuelva a conyugue", "warning");
        break;
      case 3:
        swal("Termine de completar los campos de Aval", "completelos por favor, vuelva a aval", "warning");
        break;
      case 6:
        swal("Al ser de pago unico, complete la fecha de pago", "completelos por favor, vuelva prestamo", "warning");
        break;
      case 4:
        swal("Al ser prendatario debe de rellenar la descripción de la prenda", "completelos por favor, vuelva descripcion de garantias", "warning");
        break;
      case 5:
        swal("Tiene que cargar un documento PDF con los documentos", "recibo de luz, alquiler, fotografias e seleccionar los documentos que posee el PDF", "warning");
        break;
    }
  }
}));

/*** validamos los campos de input***/
$(document).on('change', 'input[type="file"]', function () {
  // this.files[0].size recupera el tamaño del archivo
  // alert(this.files[0].size);

  var fileName = this.files[0].name;
  var fileSize = this.files[0].size;

  if (fileSize > 1806000) {
    alert('El archivo no debe superar el 1.5 MB');
    this.value = '';
    this.files[0].name = '';
  } else {
    // recuperamos la extensión del archivo
    var ext = fileName.split('.').pop();

    // console.log(ext);
    switch (ext) {
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'pdf': break;
      default:
        alert('El archivo no tiene la extensión adecuada');
        this.value = ''; // reset del valor
        this.files[0].name = '';
    }
  }
});

function inicq1() {
  var ide = $('#idClie').val();

  $.post('listar/listarPrestamos.php', { id: ide }).done(function (respuesta) {
    $('#HistoPrestamo').html(respuesta);
  });
  /*$.post( 'listar/listarPres.php',{id:ide} ).done( function(respuesta)
  {
    $( '#resulPre' ).html( respuesta );
  });*/
}
