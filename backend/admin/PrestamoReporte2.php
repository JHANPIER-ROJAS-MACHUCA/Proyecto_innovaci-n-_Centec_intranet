<?php include('head.php'); ?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
          <div class="btn-group pull-right">
        </div>
      <h5 style="color:white">Reporte de Prestamos<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
      </div>
      <link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
      <div class="panel-body">
            <div class="table-responsive">
              <div class="col-md-8">
                <div class="pull-right">
                  <button type="button" class="btn btn-default" onclick="actualizar()">
                    <span class="glyphicon glyphicon-refresh"></span> Actualizar Pagina
                  </button>
                </div>
              </div>
              <div id="resultados"></div>
              <div id="listarA"></div>
          </div>
        </div>
      </div>
        <?php
          include("modal/cambios.php");
          include("modal/editConfirmar.php");
        ?>

<?php include('footer.php'); ?>
<script src="js/prestamoR.js"></script>
<script>
     $(document).ready(function () {
     })
 </script>
  <script type="text/javascript">
  function actualizar(){location.reload();}
//Función para actualizar cada 4 segundos(4000 milisegundos)

</script>
<script type="text/javascript">
window.addEventListener('load', function ()
{
  $.post( 'listar/listarPrestR.php' ).done( function(respuesta)
  {
   $( '#listarA' ).html( respuesta );
  });
});
</script>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
 <script>
 $(document).ready(function(){
      (function ($) {
        $("#filtrar5").keyup(function(){

             var rex = new RegExp($(this).val(), 'i');
             $('.buscar tr').hide();
             $('.buscar tr').filter(function(){
              return rex.test($(this).text());
             }).show();
         })
    }(jQuery));
    });
    </script>
 <script type="text/javascript" src="js/VentanaCentrada.js"></script>
