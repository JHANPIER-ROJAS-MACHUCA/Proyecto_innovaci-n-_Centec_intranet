$(function() {
  load(1);
});

function load(page){
  $.ajax({
    url:'listar/listarUsuario.php',
    success: function(datos){
      $("#listarU").html(datos);

    }
  });
};
//usuarios
function guarsalir(page)
{
  load(1);
    $("#agreUser").modal('hide');
};
//agregar usuario
$('#aUsuario').on('submit',(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: 'add/agreUsuario.php',
        data:formData,
        cache:false,
        contentType: false,
        processData: false

    });
    load(1);
    $('#idue').val('');
    $('#txtdni').val('');
    $('#txtap').val('');
    $('#txtam').val('');
    $('#txtnom').val('');
    $('#txtcel').val('');
    $('#txtema').val('');
    $('#txtdirec').val('');
    $('#txttipo').val('');
    $('#txtofi1').val('');
    $('#img1').attr('src','img/1.jpg');
    //$('#img').val('');
}));
//eliminar
function eliminar (id1)
{
  if (confirm("Desea Eliminar al Usuario??..."))
  {
    $.ajax({
        type: "GET",
        url: "./adelete/deleteU.php",
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
$('#agreUser').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  $('#idue').val(id)
  var dni=button.data('dni')
  $('#txtdni').val(dni)
  var ap=button.data('ap')
  $('#txtap').val(ap)
  var am=button.data('am')
  $('#txtam').val(am)
  var nom=button.data('nom')
  $('#txtnom').val(nom)
  var direc=button.data('direc')
  $('#txtdirec').val(direc)
  var ema=button.data('ema')
  $('#txtema').val(ema)
  var cel=button.data('cel')
  $('#txtcel').val(cel)
  var tipo=button.data('tipo')
  $('#txttipo').val(tipo)
  var ofi=button.data('ofi')
  $('#txtofi1').val('ofi');
  var estado=button.data('estado')
  $('#txtestado').val('estado');
  //var img=button.data('img')
  //$('#img').val('img');
  if(id!=null)
  {
    $('#img1').attr("src","img2/user/"+img);
    $('#agreU2').val('Actualizar y Salir');
    $('#tituU').html('ACTUALIZAR USUARIO');
    $('#agreU').attr("type","hidden");

  }
  else {
      $('#img1').attr('src','img/1.jpg');
      $('#agreU2').val('Guardar y Salir');
      $('#tituU').html('NUEVO USUARIO');
      $('#agreU').attr("type","submit");
  }
});
