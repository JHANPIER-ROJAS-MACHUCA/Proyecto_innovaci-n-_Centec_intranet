
  function recorte(codi)
  {
    var arra;
    $.post('extra/dni.php', { txtdni: codi } ).done( function(respuesta)
    {
      arra=codi.split("|");
      return arra;
    });

  }
