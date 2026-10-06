<?php
include('../conection/bdcredito.php');
extract($_POST);
?>
  <option value="-1" selected disabled>- - Seleccione - -</option>
<?php
if(isset($id))
{
  $ca=extraer("SELECT tp.idP as id,n_credito as cred,montoAprovado as monto FROM tprestamo tp inner join tpresta_detalle tpd on tp.idP=tpd.idP inner join tclie_general tc on tp.idCG=tc.idCG where tp.estado='4' and tc.idCG='$id' group by tp.idp");
  while ($r=mysqli_fetch_array($ca))
  {
    ?>
    <option value="<?php echo $r['id']; ?>"><?php echo "Credito N° ".$r['cred']."-> Monto:S/. ".$r['monto']; ?></option>
    <?php
  }
}

 ?>
