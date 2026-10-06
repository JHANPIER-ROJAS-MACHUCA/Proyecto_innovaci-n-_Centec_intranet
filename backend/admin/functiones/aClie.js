$(document).ready(function() {
  //Ocultamos las listas//
 //$('li').css('display','none');
//Si le dan click a la caja azul, mostramos la lista 1//

$('#vincu').css('display','none');
/*$('#ver').click(function() {
    $('#vincu').toggle("slow");
});*/
});
var select = document.getElementById('ver');
select.addEventListener('change',
  function(){
    var ide = this.options[select.selectedIndex];
    if(ide.value=='2')
    {
      $('#vincu').toggle("slow");
    }
    else
    {
      $('#vincu').css('display','none');
    }
    //alert(ide.value + ': ' + ide.text)
    //$('#vincu').toggle("slow");
    //console.log(ide.value + ': ' + ide.text);
  });
