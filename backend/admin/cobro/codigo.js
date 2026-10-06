function verJuve()
{
  //author:Juvenal Perez Ramos webmaster
  // enviamos una petición al servidor mediante AJAX enviando el id
  // introducido por el usuario mediante POST
  $.post("cobro/total.php", {"txtdni":$("#txtdni").val()}, function(data){

    // Si devuelve un apellido lo mostramos, si no, vaciamos la casilla
    if(data.nom)
      $("#txtnom").val(data.nom);
      else
      $("#txtnom").val("");
    if(data.ap)
      $("#txtap").val(data.ap);
      else
      $("#txtap").val("");
    if(data.am)
        $("#txtam").val(data.am);
        else
        $("#txtam").val("");

  },"json");
};
