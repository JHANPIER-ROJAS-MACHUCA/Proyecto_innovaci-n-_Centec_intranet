<?php
  include("../conection/bdcredito.php");
  extract($_POST);
    $consulta="SELECT idex, cod, motivo, estado, fecha,concat(apU,' ',amU,' ',nomU) as dato FROM textorno tox inner join tusuario tu on tox.idU=tu.idU where estado='2' order by idex desc";

    $dta=extraer($consulta);
    ?>
    <div class="col-sm-12 table-responsive" style="overflow:scroll;height:350px">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <th>
                COD
              </th>
              <th>
                USUARIO
              </th>
              <th>
                MOTIVO
              </th>
              <th>
                FECHA
              </th>
              <th>

              </th>
            </tr>
          </thead>
          <tbody> 
          <?php
          while ($row=mysqli_fetch_array($dta))
          {
            $fe=preg_split('~-~',$row['fecha']);
            $fecha=$fe[2]." / ".$fe[1]." / ".$fe[0];
            ?>
            <tr>
              <td><?php echo $row['cod'] ?></td>
              <td><?php echo $row['dato'] ?></td>
              <td><?php echo $row['motivo']; ?></td>
              <td><?php echo $fecha?></td>
              <td><a  title="Extornar operación" onclick="confiSoli(<?php echo $row['cod']?>,<?php echo $row['idex'] ?>)"><i class="fa fa-rocket" style="color:red;font-size:25px"><i/></a></td>
            </tr>
            <?php
          }
          ?>
          </tbody>
      </table>
    </div>
          <?php

  ?>
