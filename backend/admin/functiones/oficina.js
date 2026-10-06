$(function() {
  //load(1);
});
/*
function load(page){
  $.ajax({
    url:'listar/listarOficina.php',
    success: function(datos){
      $("#listarO").html(datos);
    }
  });
};*/
//usuarios
function guarsalir(page)
{
  //load(1);|||||||||||||||||||||||||||||||||||||||||||
    //$("#agreOfi").modal('hide');
};
//agregar usuario
/*$('#aOficina').on('submit',(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: 'add/agreOfi.php',
        data:formData,
        cache:false,
        contentType: false,
        processData: false

    });
    load(1);

    $('#txtdis').val('');
    $('#txtdirec').val('');
    $('#txttel').val('');
    $('#txtema').val('');
    $('#idoe').val('');

}));*/
//eliminar
function eliminar (id1)
{
  if (confirm("Desea Eliminar La oficina??, esto prodria generar problemas en el sistema..."))
  {
    $.ajax({
        type: "GET",
        url: "./adelete/deleteO.php",
        data: "id="+id1,
        success: function(datos){
        load(1);

        }
      });
    }
    load(1);
};
//editar_cliente
$('#agreOfi').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  $('#idoe').val(id)

  var depa = button.data('depa')
  $('#txtdepa').val(depa)
  var prov = button.data('prov')
  $('#txtprovi').val(prov)
  var dis = button.data('dis')
  $('#txtdis').val(dis)
  var direc = button.data('direc')
  $('#txtdirec').val(direc)
  var tele = button.data('tele')
  $('#txttel').val(tele)
  var ema = button.data('ema')
  $('#txtema').val(ema)

  //$('#img').val('img');
  if(id!=null)
  {
    $('#tituO').html('ACTUALIZAR OFICINA');
    $('#agreU').attr("type","hidden");

  }
  else {
      $('#img1').attr('src','img/1.jpg');
      $('#agreU2').val('Guardar y Salir');
      $('#tituO').val('NUEVA OFICINA');
      $('#agreU').attr("type","submit");
  }
});
//agregar responsable
     function je(ide)
     {
       var identificador=$('#'+ide)
       var pr = identificador.data('pro')
       $('#oprovi').val(pr)
       var ds = identificador.data('ds')
       $('#odis').val(ds)
       var dr = identificador.data('dr')
       $('#odire').val(dr)
       var usu = identificador.data('usu')
       $('#resp12').val(usu)
       var id = identificador.data('ido')
       $('#idddo').val(id)
     };

  //mostrar imagen de responsable
  /*   $('#idresponsa').change(function()
     {
      var ideuser12 = $(this).val();
      alert(ideuser12)
       if(ideuser12!="")
       {
         $.post( 'extra/imgresponsable.php', { iduser: ideuser12} ).done( function( respuesta )
         {
           $( '#imge12' ).attr("src",respuesta );
         })
       }
       else
       {
        $( '#imge12' ).attr("src","img2/sede/a1.png" );
       }
     });*/
