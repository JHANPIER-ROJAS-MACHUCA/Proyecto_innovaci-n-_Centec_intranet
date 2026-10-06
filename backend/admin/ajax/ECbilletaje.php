<?php require_once('../conection/bdcredito.php'); ?>
<?php
extract($_POST);
if(isset($id))
{
  enviar("UPDATE tbilletaje SET estado='1' WHERE idBille=$id");
}
/*$id = $_GET['id'];
if ((isset($_GET['id'])) && ($_GET['id'] != ""))
{
$deleteSQL = sprintf("UPDATE tbilletaje SET estado='1' WHERE idBille=$id");
  $Result1 = mysqli_query( $conex, $deleteSQL) or die(mysqli_error($conex));
if($Result1==true)
{
  echo 1;
   }
 else
 {
  echo 0;
   }
}*/
?>
