function cuota()
{
  var ele=$('#lstmotivo').val();
  $.post( 'motivos/reporte/deposi_report.php',{moti:ele} ).done( function(respuesta)
  {
    $( '#reportes' ).html( respuesta );
  });
}
function imprimir_ahorro(nombreDiv){
    //  VentanaCentrada('./pdf/documentos/ver_ahorro.php?idahorro='+idahorro,'Ahorro','','1024','768','true');
    var contenido= document.getElementById(nombreDiv).innerHTML;
    var contenidoOriginal= document.body.innerHTML;

    document.body.innerHTML = contenido;

    window.print();

    document.body.innerHTML = contenidoOriginal;
}
