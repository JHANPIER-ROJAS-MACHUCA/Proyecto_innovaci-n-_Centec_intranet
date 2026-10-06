<?php include('head.php') ?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
    </div>

    <h5 style="color:white">Reporte de Confirmacion de billetaje<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <div class="col-md-4">
        <form action="#" method="get" accept-charset="utf-8">
          Buscar : <span class="glyphicon glyphicon-search"></span>
          <input size="10" class="form-control" type="text" id="filtrar5" />
          <br>
          <div id="resultado"></div>
        </form>
      </div>
      <div class="col-md-8">
        <div class="pull-right">
          <button type="button" class="btn btn-default" onclick="actualizar()">
            <span class="glyphicon glyphicon-refresh"></span> Actualizar Pagina
          </button>
        </div>
      </div>
      <div id="resultados"></div>
      <div id="listarA">
      </div>

    </div>
  </div>
</div>

<?php include('footer.php'); ?>
<script src="js/confirP.js"></script>
<script type="text/javascript">
  function actualizar() {
    location.reload();
  }
</script>
<script type="text/javascript">
  window.addEventListener('load', function() {
    $.post('listar/listarConfirP.php').done(function(respuesta) {
      $('#listarA').html(respuesta);
    });
  });
</script>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<script>
  $(document).ready(function() {
    (function($) {
      $("#filtrar5").keyup(function() {
        var rex = new RegExp($(this).val(), 'i');
        $('.buscar tr').hide();
        $('.buscar tr').filter(function() {
          return rex.test($(this).text());
        }).show();
      })
    }(jQuery));
  });
</script>