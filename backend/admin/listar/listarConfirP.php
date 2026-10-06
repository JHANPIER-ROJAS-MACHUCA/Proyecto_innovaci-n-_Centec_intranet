<?php include('../conection/bdcredito.php');  ?>
<?php
include '../ajax/pagination2.php';
//require_once ("../conection/db.php");
//require_once ("../conection/conexion2.php");
$page = 1;
$per_page = 10; //how much records you want to show
$adjacents  = 4; //gap between pages after number of adjacents
$offset = ($page - 1) * $per_page;
?>
<?php
if ($_COOKIE['tuser'] == '1' or $_COOKIE['tuser'] == '2' or $_COOKIE['tuser'] == '5' or $_COOKIE['tuser'] == '7') {
  /* if($_COOKIE['tuser']=='7')
  {*/
  $consul = extraer("SELECT * FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO=o.idO order by idBille desc LIMIT $offset,$per_page");
  //$count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO=o.idO order by b.idBille desc");
  $count_query   = extraer("SELECT count(*) AS numrows FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO=o.idO order by b.idBille desc");
  /*  }
  else if ($_COOKIE['tuser']=='1' or $_COOKIE['tuser']=='2' or $_COOKIE['tuser']=='5') {
      $idO=$_COOKIE['tofi'];
      $consul=extraer("SELECT * FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO='$idO' and u.idO=o.idO order by idBille desc LIMIT $offset,$per_page");
       $count_query   = extraer("SELECT count(*) AS numrows FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO='$idO' and u.idO=o.idO order by b.idBille desc");
      // $count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM tbilletaje b,tusuario u,toficina o where b.idU=u.idU and u.idO='$idO' and u.idO=o.idO order by b.idBille desc");
  }*/
?>

  <?php
  if ($row2 = mysqli_fetch_array($count_query)) {
    $numrows = $row2['numrows'];
  } else {
    echo mysqli_error($con);
  }
  $total_pages = ceil($numrows / $per_page);
  ?>
  <table class="table table-striped">
    <thead>
      <tr style="background-color:<?php echo $jua1['color'] ?>; color: white;">
        <th>FECHA</th>
        <th>Usuario</th>
        <th>Oficina</th>
        <th>Total</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody class="buscar">
      <?php
      $finales = 0;
      while ($row = mysqli_fetch_array($consul)) {
        $finales++;
      ?>
        <tr>
          <td><?php echo $row['fecha'] ?></td>
          <td><?php echo $row['dniU'] . " " . $row['apU'] ?></td>
          <td><?php echo $row['direccion'] ?></td>
          <td><?php echo 'S/. ' . $row['total']; ?></td>
          <td>
            <?php
            if ($row['estado'] == 2) { ?>
              <button type="button" onclick="confirmar(<?php echo $row['idBille']; ?>)" class="btn btn-success btn-xs">Confirmar</button>
            <?php } else { ?>
              <button type="button" onclick="eliminar(<?php echo $row['idBille']; ?>)" class="btn btn-danger btn-xs">Eliminar</button>
            <?php } ?>
          </td>
        </tr>
      <?php
      }
      ?>
      <tr>
        <td colspan='6'>
          <?php
          $inicios = $offset + 1;
          $finales += $inicios - 1;
          echo "Mostrando $inicios al $finales de $numrows registros";
          echo paginate($page, $total_pages, $adjacents);
          ?>
        </td>
      </tr>
    </tbody>
  </table>

<?php
} else {

  echo "Restringido";
}
?>