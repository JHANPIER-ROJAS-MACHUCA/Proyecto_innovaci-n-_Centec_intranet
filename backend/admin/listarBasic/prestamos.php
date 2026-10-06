<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  $p=extraer("SELECT idP,concat('credito ',n_credito,'»»',montoAprovado,' plazo ',plazo) as info FROM tprestamo where idCG='$id' and estado='4'");
  echo '<option value="" disabled selected>--Seleccione Prestamo--</option>';
  while ($r=mysqli_fetch_array($p))
  {
  ?>
    <option value="<?php echo $r['idP'] ?>"><?php echo $r['info']; ?></option>
  <?php
  }
   ?>
 
