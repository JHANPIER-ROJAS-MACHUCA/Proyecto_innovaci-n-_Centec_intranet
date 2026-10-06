<?php include('head.php');
if($_COOKIE['tuser']!='1' && $_COOKIE['tuser']!='7')
{
  echo "<script>location.href='index.php'</script>";
}
?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
      <div class="btn-group pull-right">
        <a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreOfi' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nueva Oficina</a>

    </div>

    <h5 style="color:white">Reporte de Oficinas <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" id="crack">
    <!--<form class="form-horizontal" role="form" id="datos_cotizacion">-->
          <div class="form-group row">
            <label for="q" class="col-md-2 control-label">OFICINAS</label>
          </div>
    <!--</form>-->
      <div id="listarO">

      </div>

    </div>
</div>
<script src="extra/min.js"></script>
<script src="extra/juvedni.js"></script>
<script src="moduloOfi/codigo.js">

</script>

<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>

<?php include('modal/agreOficina.php'); ?>
<script type="text/javascript">

window.addEventListener('load', function () {

      // $('#cfig').attr('class', 'active ');
       $('#oficinasctcp').attr('class', 'active ');
   });
</script>
