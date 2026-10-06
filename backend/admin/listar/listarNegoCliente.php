<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  if(isset($ide))
  {
    $dta=extraer("SELECT idCN as id, direccion, tipo, tipoLocal, tipoNegocio, tiempo FROM tclie_negocio where idCG='$ide' order by idCN desc");
    ?>
    <div class="col-sm-12 table-responsive">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <th>
                DIRECCION
              </th>
              <th>
                TIPO
              </th>
              <th>
                TIPO DE LOCAL
              </th>
              <th>
                TIPO DE NEGOCIO
              </th>
              <th>
                TIEMPO
              </th>
              <th>

              </th>
            </tr>
          </thead>
          <tbody>
          <?php
          while ($row=mysqli_fetch_array($dta))
          {
            ?>
            <tr>
              <td><?php echo $row['direccion']; ?></td>
              <td><?php echo $row['tipo']; ?></td>
              <td><?php echo $row['tipoLocal']; ?></td>
              <td><?php echo $row['tipoNegocio']; ?></td>
              <td><?php echo $row['tiempo']; ?></td>
              <td>  <?php //if ($row['critico']=="1" || $_COOKIE['tuser']=='7') {
                  ?>
                  <a class='btn btn-sm btn-default' title='Eliminar negocio' onclick="eli1('<?php echo $row['id'];?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
                <?php
                //  }
                  ?>
              </td>
            </tr>
            <?php
          }
          ?>
          </tbody>
      </table>
    </div>
          <?php
  }
  ?>
