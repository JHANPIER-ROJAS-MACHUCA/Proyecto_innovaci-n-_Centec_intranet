$(document).ready(function()
{
//  swal("Recuerde que la información agregada, no se podra editar, tenga cuidado ya que solo se podra eliminar");
  inicio();
  hit();
  //setInterval(hit, 5000);
});
function inicio()
{
  $.post( 'listar/aBodega.php').done( function( respuesta )
  {
    $( '#detalleBobe' ).html( respuesta );
  });
}
//caragamos el historial de transferencias
function hit()
{
  $.post( 'listar/aBodega1.php').done( function( respuesta )
  {
    $( '#detalleBobe1' ).html( respuesta );
  });
}
//eliminamos un registro
function elimi(id)
{
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
     $.post( 'adelete/deleteCajaBodega.php', {idEli:id,moti:person} );

     swal("Eliminado con exito","","success");

   }
   setTimeout(function()
   {
     hit();
   },100,"JavaScript");
};
/*========================*/
function Confi(id)
{
   $.post( 'add/confiBo.php', {id:id} );
   swal("Monto  Confirmado","","info");
   setTimeout(function()
     {
       inicio();
       hit();
     },100,"JavaScript");
}
//agregar nueva operacion
function reload7()
{
  //
  var a1=$('#tipo').val();
  var a2=$('#Ofic').val();
  var a3=$('#monto').val();
  //var a=$().val();
  if(a1!="-1" && a3!="")
  {
    if(a1==1)
    {
      $.post( 'add/agreBobeda.php', {monto:a3,Ofic:0,tipo:a1} );
    }
    else
    {
      $.post( 'add/agreBobeda.php', {monto:a3,Ofic:a2,tipo:a1} );
    }

     $('#Ofic').val('');
     $('#monto').val('');
     $('#tipo').val("");
     setTimeout(function()
     {
       inicio();
       hit();
     },100,"JavaScript");
  }
  else
  {
    swal ( "Complete toda la información","", "info");
  }
};
