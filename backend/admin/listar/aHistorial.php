<?php
include('../conection/bdcredito.php');
extract($_POST);
if(isset($idTip) && isset($idAH))
{
  $consul=extraer("SELECT idAd,tad.monto as monto,tad.fecha as fecha,tipo,motivo,tam.idam as id1,concat(apU,' ',amU,' ',nomU) as dato FROM tahorro_deta tad inner join tusuario tu on tad.idU=tu.idU inner join tahorro_motivo tam on tad.moti=tam.idam where idA=(select idA from tahorro where tipoA='$idTip' and id='$idAH') order by idAd desc");
  $consul2=extraer("select idA from tahorro where tipoA='$idTip' and id='$idAH'");
  $idA2 =mysqli_fetch_array($consul2);
  $idA= $idA2['idA'];
  ?>
  <table class="table table-striped">
      <thead>
      <tr>
          <th>Tipo</th>
          <th>Motivo</th>
          <th>Monto</th>
          <th>Fecha </th>
          <th>Usuario</th>
          <th> <button type="submit" class="btn btn-default"  title='Descargar Ahorro'  onclick="imprimir_ahorro('<?php echo $idA;?>');">
              <span class="glyphicon glyphicon-print"></span> Imprimir
            </button> </th>

      </tr>
      </thead>
      <tbody>
        <?php
          while($row =mysqli_fetch_array($consul))
          {
            $movimiento="";
            if($row['tipo']=='1')
            {
              $movimiento="Depósito";
            }
            else if($row['tipo']=='7')
            {
              $movimiento="AHORRO";
            }
            else if($row['tipo']=='8')
            {
              $movimiento="RETIRO";
            }
            else if($row['tipo']=='4')
            {
              $movimiento="Descuento";
            }
            else if($row['tipo']=='5')
            {
              $movimiento="Adelanto";
            }

            $fech=preg_split("~-~", $row['fecha']);
            $fecha1=$fech[2].' / '.$fech[1].' / '.$fech[0];
        ?>
      <tr>
        <td><?php echo $movimiento; ?></td>
        <td><?php echo $row['motivo'] ?></td>
        <td><?php echo 'S/. '.$row['monto']; ?></td>

        <td><?php echo   $fecha1 ?></td>
        <td><?php echo $row['dato']; ?></td>

        <td>
          <?php
            if($limitadorfinal==0)
            {
           ?>
          <a class='btn btn-sm btn-default' title='Eliminar Ahorro' onclick="eli('<?php echo $row['idAd'];?>','<?php echo $row['id1']; ?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
          <?php
            }
           ?>
        </td>
      </tr>
        <?php
          }
        ?>
    </tbody>
  </table>
  <?php
}


?>
