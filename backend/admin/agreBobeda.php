<?php
include('head.php');
if($_COOKIE['tuser']!='1' && $_COOKIE['tuser']!='7')
{
  echo "<script>location.href='index.php'</script>";
}
?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
          <div class="btn-group pull-right">
        </div>

      <h5 style="color:white">CAJA BOBEDA<small> <?php echo $comentaJuve ?></small></h5>
      </div>
      <div class="panel-body">
          <div id="detalleBobe">

          </div>
          <div id="detalleBobe1">

          </div>
      </div>
</div>
<?php include('footer.php'); ?>
<script src="functiones/bobeda.js"></script>
<script src="extra/swetalert.js"></script>
<script type="text/javascript">
window.addEventListener('load', function ()
{
      // $('#idCredito').attr('class','active');
       $('#CajaBode').attr('class', 'active ');
   });
</script>
