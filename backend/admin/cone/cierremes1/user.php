<?php
  include('../../conection/bdcredito.php');
  extract($_POST);
  $idO=$_COOKIE['tofi'];
  if($_COOKIE['tofi']=="")
  {
    $idO=$ofi;
  }
  ?>
    <option value="0" selected>-- Seleccione --</option>
  <?php
  $cs=extraer("SELECT idU as id,tipoU,concat(apU,' ',amU,' ',nomU) as dato FROM drag.tusuario where idO='$idO' order by tipoU asc");
  while ($r=mysqli_fetch_array($cs))
  {
    switch ($r['tipoU'])
    {
      case '1':
        $tipo="Gerente";
        break;
      case '2':
        $tipo="Administrador";
        break;
      case '3':
        $tipo="Operador";
        break;
      case '4':
        $tipo="Asesor";
        break;
    }
    if($r['tipoU']!=2){
    ?>
      <option value="<?php echo $r['id']; ?>"><?php echo $tipo." ".$r['dato']; ?></option>
    <?php }
  }
 ?>
