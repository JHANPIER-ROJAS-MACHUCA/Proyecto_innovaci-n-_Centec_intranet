$(function() {

  $('#listaTipo').change(function()
	{
		var tipoa = $(this).val();
    //cambio de titulo
    if(tipoa=='2')
    {
        $('#tipoCambio').html('Usuarios');
        $('#tituloAhorro').html('AHORROS DE USUARIOS');
    }
    else
    {
        $('#tipoCambio').html('Clientes');
        $('#tituloAhorro').html('AHORROS DE CLIENTES');
    }
    //rellena lista con los datos de los tipos
    $('#listaAho1').val(null).trigger('change');
		$.post( 'extra/listar.php', { tipoA: tipoa} ).done( function( respuesta )
		{
			$( '#listaAho1' ).html( respuesta );
		});
    //muestra todos los usuarios o clientes que ahorran en div's
    $.post( 'listar/listarAhorro.php', { tipoA: tipoa} ).done( function(respuesta)
    {
      $( '#listarA' ).html( respuesta );
    });
	});
});

///mostrar motivos

//Cancelar
function canceli()
{
  $.post( 'listar/listarAhorro.php', { tipoA: $('#listaTipo').val()} ).done( function(respuesta)
  {
    $( '#listarA' ).html( respuesta );
  });
}

//busca al cliente o usuario
function busca()
{
  var identificador=$('#listaAho1').val();
  var tipo=$('#listaTipo').val();
  if(identificador!=null)
  {
    //se envia el id para recoger los datos
    $.post( 'listar/listarAhorro.php', { tipoA: tipo, ide:identificador } ).done( function(respuesta)
    {
      $( '#listarA' ).html( respuesta );
    });
    $('#envi').attr('href','#tab-2');
  }
};
//pasar id de lista a input ide
function envi()
{
  var codi=$('#listaAho1').val();
  var tipo=$('#listaTipo').val();
  if(codi!=null)
  {
   $('#Aid').val(codi);
   $('#Ati').val(tipo);
  }
  else
  {
    $('#Adi').val("no hay error");
  }
  thistori(tipo,codi);
}
//agregar Ahorro
$('#FAho').on('submit',(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: 'add/agreAhorro.php',
        data:formData,
        cache:false,
        contentType: false,
        processData: false

});
$('#Aobs').val('');
$('#Aid').val('');
$('#Ati').val('');
$('#Atipo').val('');
$('#Afecha').val('');
actualizar();

}));
//actualizar();
function actualizar()
{
  setTimeout(function()
  {
    var identificador=$('#listaAho1').val();
    var tipo=$('#listaTipo').val();
    if(identificador!=null)
    {
      //se envia el id para recoger los datos
      $.post( 'listar/listarAhorro.php', { tipoA: tipo, ide:identificador } ).done( function(respuesta)
      {
        $( '#listarA' ).html( respuesta );
      });
    }
    else
    {
      $.post( 'listar/listarAhorro.php', { tipoA: tipo} ).done( function(respuesta)
      {
        $( '#listarA' ).html( respuesta );
      });
    }
  },1000,"JavaScript");
};
//pasar nombre
function nombreA(id)
{
  var dato=$('#'+id);
  var nom=dato.data('nom');
  var iden=dato.data('id');
  var tipo=$('#listaTipo').val();
  var conte="";
  if(tipo=='1')
  {
    conte="Cliente: ";
  }
  else if(tipo=='2')
  {
    conte="Usuario: ";
  }
   $('#Aid').val(iden);
   $('#Ati').val(tipo);
   $('#tituloAhorro2').html(conte+' '+nom);
   thistori(tipo,iden);
}
function thistori(tipo,iden)
{
  setTimeout(function()
  {
    $.post( 'listar/aHistorial.php', { idTip: tipo, idAH:iden } ).done( function(respuesta)
    {
      $('#registro').html( respuesta );
    });
  },100,"JavaScript");

}
function eli(id,id2)
{
  var dato=$('#'+id);
  var nom=dato.data('nom');

  var person = prompt("Ingrese el motivo de la eliminación, por favor.");
  while(person=="")
     {
        person = prompt("Ingrese el motivo de la eliminación, por favor.");
     }
 if(person==undefined)
   {
     swal("Si va a eliminar algo, este seguro!!! para la proxima ves Att. Desarrollador 1","","warning");
   }
 else if(person!="")
   {
     $.post( 'adelete/eliDetaAho.php', {idEli:id,moti:person,tipo:id2} );
     swal("Eliminado con exito","","success");
     melas();
   }
}
function melas()
{
    //identificadores
    var tipo=$('#Ati').val();
    var iden=$('#Aid').val();

     setTimeout(function()
     {
       $.post( 'listar/aHistorial.php', { idTip: tipo, idAH:iden } ).done( function(respuesta)
        {
          $('#registro').html( respuesta );
        });
     },100,"JavaScript");
     //alert("no");

}
function imprimir_ahorro(idahorro){
      VentanaCentrada('./pdf/documentos/ver_ahorro.php?idahorro='+idahorro,'Ahorro','','1024','768','true');
    }
