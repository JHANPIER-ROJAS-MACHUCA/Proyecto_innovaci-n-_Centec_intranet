// $(document).ready(function () {
//   setInterval(function () {
//     btn();
//   }, 10);
// });
//habilitar boton de guardado

function btn() {
  var to = $("#txttotal").val();
  if (to > 0) {
    $("#btnguardar").removeAttr("disabled");
    $("#btnguardar").attr("onclick", "enviPago()");
  } else {
    $("#btnguardar").attr("disabled", "disabled");
    $("#btnguardar").removeAttr("onclick");
    //$('#btnguardar').attr('onclick','disabled');
  }
}
function enviPago() {
  swal({
    title: "¿Está seguro?",
    text: "Una vez confirmado, tendra que extornar en administración si se equivoco!!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      swal("Imprima su comprobante de pago ^_^", {
        icon: "success",
      });
      $("#btnguardar").attr("disabled", "disabled");
      $("#btnguardar").removeAttr("onclick");
      bloquebtn();
    } else {
      swal("La operación fue cancelada!");
    }
  });
  /*
  var define_situacion = confirm("Esta seguro??");
  if (define_situacion == true) {
    $("#btnguardar").attr("disabled", "disabled");
    $("#btnguardar").removeAttr("onclick");
    bloquebtn();
  }
*/
  //enviPago
}
//enviar la informcion a Guardar
function bloquebtn() {
  //identificador del prestamo
  //  var identificador=$('#txtprestamos').val();
  var identificador = $("#idpresta").val();
  //monto neto
  var montoNeto = $("#txtmontoNeto").val();

  //se seleciono checked por cuota
  var checuota = $("#chkcuota").prop("checked");
  //cantidad de cuotas
  var porCuota = $("#txtcuotaP").val();
  //pago por cuota cantidad de cuotaM
  var cuotas = $("#txtmonCuo").val();

  //seseleciono el checked Mora
  var chemora = $("#chkmora").prop("checked");
  //pago por mora
  var mora = $("#txtmora").val();
  //total que pagara
  var totalPago = $("#txttotal").val();
  //total de Mora
  var tomora = $("#txtmonmora").val();
  if (tomora == "") {
    tomora = 0;
  }
  //ver cuantas cuotas vencidasd hay
  var venci = $("#txtcuotasVenci").val();
  //verificar si es monto o cuota
  var juven = montoNeto;
  if (juven == "") {
    juven = 0;
  }
  var cumon = "";
  var cuo = "0";
  if (checuota) {
    cuo = "1";
    juven = cuotas;
    cumon = porCuota;
  }
  var mor = "0";
  if (chemora) {
    mor = "1";
  }
  //verificar si es mora
  if (identificador != "") {
    var opera = "";
    var usuario = "";
    var dire = "";
    var tel = "";
    var corre = "";
    var fecha = $("#txtfechaPago").val();

    var combo1 = document.getElementById("txtprestamos");
    var selected1 = combo1.options[combo1.selectedIndex].text;
    var prest = selected1.split("»»");
    presta = prest["0"];
    presta = presta.replace("credito", "");

    console.log({identificador, montoNeto, cuo, porCuota, cuotas,mor,mora,totalPago, tomora,venci,fecha});
    return;
    $.post("add/pagarUltimo.php", {
      id: identificador,
      montoN: montoNeto,
      cuo: cuo,
      cancuota: porCuota,
      mcuota: cuotas,
      mor: mor,
      cmora: mora,
      total: totalPago,
      tomora: tomora,
      venci: venci,
      fechaJuve: fecha,
    }).done(function (respuesta) {
      $("#opera").html(respuesta);
      opera = $("#opera1").val();
      usuario = $("#usu").val();
      dire = $("#dire").val();
      tel = $("#tel").val();
      corre = $("#corre").val();
      var totalF = $("#txttotalpendiente").val();
      tomora = parseFloat(tomora).toFixed(2);
      if (tomora <= 0) {
        tomora = "0.00";
      }
      var sub = parseFloat(juven).toFixed(2);
      totalF = (parseFloat(totalF) - parseFloat(sub)).toFixed(2);
      //============credito

      setTimeout(
        function () {
          llamado2();
          var pendi = $("#txtcuotaPendiente").val();
          var vencid = $("#txtcuotasVenci").val();

          var combo = document.getElementById("txtdni");
          var selected = combo.options[combo.selectedIndex].text;
          var usuario1 = selected.split("|");
          cliente = usuario1["1"];

          //imprimir(usuario,cliente,dire,tel,corre,opera,totalPago,sub,tomora,cumon,totalF,pendi,vencid);
          imprime(
            usuario,
            cliente,
            dire,
            tel,
            corre,
            opera,
            totalPago,
            sub,
            tomora,
            cumon,
            totalF,
            pendi,
            vencid,
            presta
          );
          //imprime();
        },
        510,
        "JavaScript"
      );
    });
  }
  //=============================================================================
  //caucher
  /*function imprimir(usu,clie,dire,tel,corre,opera,total,sub,mora,cumon,totalF,pendi,vencid,presta)
 {
       VentanaCentrada('./pdf/documentos/boucher.php?usu='+usu+'& clie='+clie+'& dire='+dire+'& tel='+tel+'& corre='+corre+'& opera='+opera+'& total='+total+'& sub='+sub+'& mora='+mora+'& cumon='+cumon+'& totalF='+totalF+'& pendi='+pendi+'& vencid='+vencid+'& pres='+presta,'Boucher','','1024','768','true');
 }*/
  //=============================================================================
  function imprime(
    usu,
    clie,
    dire,
    tel,
    corre,
    opera,
    total,
    sub,
    mora,
    cumon,
    totalF,
    pendi,
    vencid,
    presta
  ) {
    window.open('voucher.php?operacion='+opera,'voucher',"width=300,height=300,scrollbars=NO");
    // window.open(
    //   "pru.php?usu=" +
    //   usu +
    //   "&clie=" +
    //   clie +
    //   "&dir=" +
    //   dire +
    //   "&tel=" +
    //   tel +
    //   "&corre=" +
    //   corre +
    //   "&opera=" +
    //   opera +
    //   "&total=" +
    //   total +
    //   "&sub=" +
    //   sub +
    //   "&mora=" +
    //   mora +
    //   "&cumon=" +
    //   cumon +
    //   "&totalF=" +
    //   totalF +
    //   "&pendi=" +
    //   pendi +
    //   "&vencid=" +
    //   vencid +
    //   "& pres=" +
    //   presta,
    //   "ventana1",
    //   "width=300,height=300,scrollbars=NO"
    // );
  }
  //=============================================================================
  llamado2();
  setTimeout(
    function () {
      llamado2();
    },
    500,
    "JavaScript"
  );
  //llamado2();
  setTimeout(
    function () {
      limpiar2();
    },
    1500,
    "JavaScript"
  );
}
//pago cuotas
function pCuota() {
  if ($("#chkcuota").prop("checked")) {
    $("#txtcuotaP").removeAttr("disabled");
    $("#txtmontoNeto").attr("disabled", "disabled");
    $("#txtmontoNeto").val("");
    $("#txtmonCuo").val("");
  } else {
    $("#txtcuotaP").attr("disabled", "disabled");
    $("#txtcuotaP").val("");
    $("#txtmontoNeto").removeAttr("disabled");
    $("#txtmontoNeto").val("");
    $("#txtmonCuo").val("");
  }
  totalizar();
}
//por Mora
function pMora() {
  if ($("#chkmora").prop("checked")) {
    var more = $("#txtmoraCre").val();
    $("#txtmora").removeAttr("disabled");
    $("#txtmonmora").val("");
  } else {
    $("#txtmora").attr("disabled", "disabled");
    $("#txtmora").val("");
    $("#txtmonmora").val("");
  }
  totalizar();
}

//buscar cliente
///llmar
$("#llamar").click(function () {
  llamado();
});

//llamado
function llamado() {
  var codigo = $("#txtdni").val();
  $.post("listarBasic/prestamos.php", { id: codigo }).done(function (
    respuesta
  ) {
    $("#txtprestamos").html(respuesta);
  });
  limpiar2();
}
function llamado2() {
  var codigo = $("#txtdni").val();
  $.post("listarBasic/prestamos.php", { id: codigo }).done(function (
    respuesta
  ) {
    $("#txtprestamos").html(respuesta);
  });
}
//lista de prestamos filtrada
function llamaPresta() {
  var codi = $("#txtprestamos").val();
  $("#idpresta").val(codi);
  var id = $("#idpresta").val();
  const html_LinkVerHistorial = document.getElementById('linkVerHistorial');
  html_LinkVerHistorial.style.display = 'inline-block';
  ver_deuda(id);
}

function ver_deuda(codigo) {
  $.post(
    "listarBasic/cobro.php",
    { codigo: codigo },
    function (data) {
      if (data.plazo) {
        $("#txtFechaDesembolso").html(data.fechaDesembolso);
        $("#txtFechaFinalización").html(data.finCredit);
        $("#txtTotalAPagar").html(data.pendiH);

        // $("#txtcliente").html(data.nom);

        // $("#txtestado").html(data.estado);
        switch (data.status) {
          case 'ADELANTADO':
            $("#txtestado").html("<span style='color:blue;font-weight:bold;'>ADELANTADO</span>");

            break;
          case 'PUNTUAL':
            $("#txtestado").html("<span style='color:green;font-weight:bold;'>PUNTUAL<span>");

            break;
          case 'VENCIDO':
            $("#txtestado").html("<span style='color:black;font-weight:bold;'>VENCIDO</span>");

            break;
          case 'RETRAZADO':
            $("#txtestado").html("<span style='color:red;font-weight:bold;'>RETRASADO</span>");

            break;

          default:
            break;
        }

        //situacion de Cuotas
        $("#txttotalCuotas").val(data.plazo);
        $("#txtcuotaPendiente").val(data.pendiC);
        $("#txtcuotasVenci").val(data.venci);
        $("#txtdiaRetraso").val(data.atra);
        //deuda de cuotas Pendientes
        $("#txttotalpendiente").val(data.pendiT);
        $("#txtcuota").val(data.cuo);
        $("#txtpendiente").val(data.pendiH);
        var more = data.mora;
        $("#txtmoras").val(data.cadena);

        if (more != "") {
          more = parseFloat(more).toFixed(2);
        } else {
          more = 0;
        }
        $("#txtmoraCre").val(more);
        var res = "";
        var res1 = "";
        if (data.estado != "") {
          var asde = ".";
          var de = data.estado;
          var data = de.split(asde);
          res = data["0"];
          res1 = data["1"];
        }
        // $("#txtestado").val(res);
        $("#idPres").val(res1);
      } else {
        limpiar();
        swal("Cliente no registrado", "", "error");
      }
    },
    "json"
  );
  //limpiar3();
}
function limpiar() {
  //$("#idpresta").val('');
  $("#txttotalCuotas").val("");
  $("#txtcuotaPendiente").val("");
  $("#txtcuotasVenci").val("");
  $("#txtdiaRetraso").val("");
  //deuda de cuotas Pendientes
  $("#txttotalpendiente").val("");
  $("#txtcuota").val("");
  $("#txtpendiente").val("");
  $("#txtmoraCre").val("");
  $("#txtestado").val("");
}
//limpieza 2
function limpiar2() {
  $("#txtestado").html('');
  $("#txtFechaDesembolso").html('');
  $("#txtFechaFinalización").html('');
  $("#txtTotalAPagar").html('');
  limpiar();
  //$("#txtdni").val('');
  limpiar3();
}
function limpiar3() {
  $("#txtmontoNeto").val("");
  $("#txtcuotaP").val("");
  $("#txtmora").val("");
  $("#txttotal").val("");
  $("#txtmonCuo").val("");
  $("#txtmonmora").val("");
}
//generar el TOTAL
function totalizar() {
  var total = $("#txtcuotaPendiente").val();
  var montoN = $("#txtmontoNeto").val();
  if (montoN == "") {
    montoN = 0;
  }
  limiMonto(montoN);
  var mora1 = $("#txtmora").val();
  var mora = $("#txtmonmora").val();
  if (mora1 == "") {
    mora1 = 0;
    mora = 0;
  }
  limiMora(mora);
  var cuota = $("#txtmonCuo").val();
  if (cuota == "") {
    cuota = 0;
  }

  var total = (
    parseFloat(montoN) +
    parseFloat(mora) +
    parseFloat(cuota)
  ).toFixed(2);

  $("#txttotal").val(total);
}
//mostrar el monto por cuota
function cuotaM() {
  var cuotas = $("#txtcuotaP").val();
  limiCuota(cuotas);
  var identificador = $("#idPres").val();
  if (cuotas == "") {
    cuotas = 0;
  }
  $.post(
    "listarBasic/cuotas.php",
    { id: identificador, cantidad: cuotas },
    function (data) {
      if (data.cuo) {
        $("#txtmonCuo").val(data.cuo);
      } else {
        $("#txtmonCuo").val("");
      }
    },
    "json"
  );
  setTimeout(
    function () {
      totalizar();
    },
    100,
    "JavaScript"
  );
}
///cunado hace click
// $("#txtcuotaP").click(function () {
//   cuotaM();
// });
// $("#txtmora").click(function () {
//   montoMo();
// });
//mostarr motno por Mora
function montoMo() {
  var canti = $("#txtmora").val();
  //limiMora(canti);
  var more = $("#txtmoras").val();
  var ced;
  if (more != "") {
    ced = more.split(",");
  }
  mora = 0;
  if (canti != "") {
    canti = parseFloat(canti);
  } else {
    canti = 0;
  }
  if (canti > ced.length - 1) {
    swal("Ah superado el limite máximo de mora del cliente", "", "info");
    canti = ced.length - 1;
    $("#txtmora").val(canti);
  }

  //sacamos la mora
  for (var i = 0; i < canti; i++) {
    mora = mora + parseFloat(ced[i]);
  }
  $("#txtmonmora").val(mora);

  setTimeout(
    function () {
      totalizar();
    },
    100,
    "JavaScript"
  );
}
function reco(valor) {
  resul = 0;
  if (valor != "") {
    resul = parseFloat(valor);
  }
  return resul;
}

//limitar Cuotas
function limiCuota(canti) {
  var pendi = $("#txtcuotaPendiente").val();
  var venci = $("#txtcuotasVenci").val();
  if (pendi == "") {
    pendi = 0;
  }
  if (venci == "") {
    venci = 0;
  }
  var suma = parseFloat(pendi) + parseFloat(venci);
  if (canti > suma) {
    swal("Ah alcanzado el limite máximo de cuotas", "", "info");

    $("#txtcuotaP").val(suma);
    setTimeout(
      function () {
        totalizar();
      },
      100,
      "JavaScript"
    );
  }
}
//verificar el monto de total
function limiMonto(montoNeto) {
  var total = $("#txttotalpendiente").val();
  if (total != "") {
    total = parseFloat(total);
  } else {
    total = 0;
  }

  montoNeto = parseFloat(montoNeto);
  var vali = 0;
  if (montoNeto > total) {
    swal("Ah alcanzado el monto neto máximo del cliente", "", "info");
    $("#txtmontoNeto").val(total);
    setTimeout(
      function () {
        totalizar();
      },
      100,
      "JavaScript"
    );
  }
}
//verificacion de mora
function limiMora(mora) {
  var more = $("#txtcan").val();
  var can = $("#txtmor").val();
  more = parseFloat(more);
  mora = parseFloat(mora);
  if (more < mora) {
    swal("Ah superado el limite máximo de mora del cliente", "", "info");
    var final = parseFloat(can) * parseFloat(more);
    $("#txtmonmora").val(final);
    $("#txtmora").val(more);
    setTimeout(
      function () {
        totalizar();
      },
      100,
      "JavaScript"
    );
  }
}
