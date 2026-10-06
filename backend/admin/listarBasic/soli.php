<?php
  include("../conection/bdcredito.php");
  extract($_POST);
    if($_COOKIE['tuser']=='5' || $_COOKIE['tuser']=='7' || $_COOKIE['tuser']=='1' || $_COOKIE['tuser']=='2')
    {
        $consulta="SELECT idex, cod, motivo, estado, idU,fecha FROM textorno order by idex desc";
    }
    else
    {
        $idU=$_COOKIE['user1'];
        $consulta="SELECT idex, cod, motivo, estado, idU,fecha FROM textorno where idU='$idU' order by idex desc";
    }

    $dta=extraer($consulta);
    ?>
    <div class="col-sm-12 table-responsive">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <!--<th>
                USUARIO
              </th>-->
              <th>
                COD
              </th>
              <th>
                MOTIVO
              </th>
              <th>
                ESTADO
              </th>
              <th>

              </th>
            </tr>
          </thead>
          <tbody>
          <?php
          while ($row=mysqli_fetch_array($dta))
          {
            $pto="";
            switch ($row['estado']) {
              case 1:
                $pto="APROBADO";
                break;
              case 2:
                $pto="PENDIENTE";
                break;
              case 3:
                $pto="NO APROVADO";
                break;
            }
            ?>
            <tr>
              <!--<td><?php echo $row['idU'] ?></td>-->
              <td><?php echo $row['cod'] ?></td>
              <td><?php echo $row['motivo'] ?></td>
              <td><?php echo $pto; ?></td>
              <?php if($row['estado']=='2'){ ?>
              <td><a  title="Eliminar solicitud" onclick="elimini(<?php echo $row['idex'] ?>)"><i class="fa fa-rocket" style="color:red;font-size:25px"><i/></a></td>
              <?php } ?>
            </tr>
            <?php
          }
          ?>
          </tbody>
      </table>
    </div>
