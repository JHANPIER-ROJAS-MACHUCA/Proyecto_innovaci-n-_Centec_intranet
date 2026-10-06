$(function() {
  load(1);
});
function load(page){
  $.ajax({
    url:'listar/listarPrestamo.php',
    success: function(datos){
      $("#listarP").html(datos);
    }
  });
};
//usuarios
function guarsalir(page)
{
  load(1);
    $("#agrePrest").modal('hide');
};
//agregar usuario
$('#aPrestamo').on('submit',(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    //var idCG = '<?php echo $idCG;?>';
    $.ajax({
        type:'POST',
        url: 'add/agrePrestamo.php',
       // data: {idCG:idCG}
        data:formData,
        cache:false,
        contentType: false,
        processData: false
    });
    load(1);
    $('#idpe').val('');
    $('#txtidCG').val('');
    $('#txtmontop').val('');
    $('#txtmontoa').val('');
    $('#txtcuota').val('');
    $('#txttaza').val('');
    $('#txtfechad').val('');
    $('#txtpago').val('');
    $('#txtplazo').val('');
    $('#txtncredito').val('');
    $('#txtncuota').val('');
    $('#txtdp').val('');
    $('#txtestado').val('');
    $('#txtfechat').val('');
    $('#txttip').val('');
    $('#txtmontod').val('');

}));
//eliminar
function eliminar (id1)
{
  if (confirm("Desea Eliminar el prestamo??..."))
  {
    $.ajax({
        type: "GET",
        url: "./adelete/deleteP.php",
        data: "id="+id1,
        success: function(datos)
        {
          load(1);
        }
    });
  }
    load(1);
};
//editar_cliente
$('#agrePrest').on('show.bs.modal',function (event){
  var button = $(event.relatedTarget)
  var id = button.data('id')
  $('#idpe').val(id)
  // var idCG = button.data('idCG')
  //$('#txtidCG').val(idCG)
  var montop=button.data('montop')
  $('#txtmontop').val(montop)
  var montoa=button.data('montoa')
  $('#txtmontoa').val(montoa)
  var cuot=button.data('cuot')
  $('#txtcuota').val(cuot)
  var taz=button.data('taz')
  $('#txttaza').val(taz)
  var fechad=button.data('fechad')
  $('#txtfechad').val(fechad)
  var pag=button.data('pag')
  $('#txtpago').val(pag)
  var plaz=button.data('plaz')
  $('#txtplazo').val(plaz)
  var ncredito=button.data('ncredito')
  $('#txtncredito').val(ncredito)
  var ncuota=button.data('ncuota')
  $('#txtncuota').val(ncuota)
  var diasp=button.data('diasp')
  $('#txtdp').val(diasp)
  var estad=button.data('estad')
  $('#txtestado').val(estad)
  var fechat=button.data('fechat')
  $('#txtfechat').val(fechat)
  var tipo=button.data('tipo')
  $('#txttip').val(tipo)
  var montod=button.data('montod')
  $('#txtmontod').val(montod);
   /*alert("id " + id + " montop"+montop+"montoa"+montoa+"cuota"+cuot+"taz"
    +taz+"fechad"+fechad+"pago"+pag+"plaz"+plaz+"ncredito"+ncredito+"ncuota"+ncuota+"diaspas"+diaspas+"estad"
    +estad+"fechat"+fechat+"tipo"+tipo+"montod"+montod);
    */
  if(id!=null)
  {
    $('#agreP2').val('Actualizar y Salir');
    $('#tituP').html('ACTUALIZAR PRESTAMO');
    $('#agreP').attr("type","hidden");
  }
  else {
      $('#agreP2').val('Guardar y Salir');
      $('#tituP').html('NUEVO PRESTAMO');
      $('#agreP').attr("type","submit");
  }
});
function idP(id)
{
  var dato=$('#'+id);
   var idp=dato.data('idp');
  var idcg=dato.data('idcg');
   $('#txtidP').val(idp);
   $('#txtidCG').val(idcg);
    $('#txtidP2').val(idp);
    $('#txtidP3').val(idp);
    $('#txtidP4').val(idp);
    $('#txtidP5').val(idp);
     $('#txtidP6').val(idp);
   $('#txtidCG2').val(idcg);
  if(idp!=null)
    {
      //se envia el id para recoger los datos
      $.post( 'listarVinculacion.php', { idP: idp } ).done( function(respuesta)
      {
        $( '#listarV' ).html( respuesta );
      });
    }

};
function imprimir_prestamo(idprestamo){
      VentanaCentrada('./pdf/documentos/ver_prestamo.php?idprestamo='+idprestamo,'Prestamo','','1024','768','true');
    }
