<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  if(isset($id))
  {
  ?>
    <div class="col-sm-12 table-responsive">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <th>
                TIPO DE PRESTAMO
              </th>
              <th>
                MONTO PROPUESTO
              </th>
              <th>
                MONTO APROBADO
              </th>
              <th>
                TAZA
              </th>
              <th>
                PAGO
              </th>
              <th>
                CUOTAS
              </th>
              <th>
                CUOTA
              </th>
              <th>
                MORA
              </th>
              <th>
                ESTADO
              </th>
            </tr>
          </thead>
          <tbody>
          <?php
          $data=extraer("SELECT idP as id,idV as iv,idDOC as ad,tipoP ,montoPropuesto as mp,montoAprovado as ma,cuota,taza ,pago,plazo  ,mora as mr,estado FROM tprestamo where idCG='$id' order by idP desc");
          while ($row=mysqli_fetch_array($data))
          {
            switch ($row['tipoP'])
            {
              case 1:
                $tipoP="TRANSPORTE";
                break;
              case 2:
                $tipoP="COMERCIO";
                break;
              case 3:
                $tipoP="PRENDATARIO";
                break;
              case 4:
                $tipoP="SERVICIO";
                break;
            }
            switch ($row['pago']) {
              case 1:
                $pago="DIARIO";
                break;
              case 2:
                  $pago="SEMANAL";
                break;
              case 3:
                    $pago="PAGO UNICO";
                break;
              case 4:
                  $pago="MENSUAL";
              break;
            }
            ?>
            <tr>
              <td><?php echo $tipoP; ?></td>
              <td><?php echo "S/. ".$row['mp']; ?></td>
              <td><?php echo "S/. ".$row['ma']; ?></td>
              <td><?php echo $row['taza']."%"; ?></td>
              <td><?php echo $pago;?></td>
              <td><?php echo $row['plazo']; ?></td>
              <td><?php echo "S/. ".$row['cuota']; ?></td>
              <td><?php echo "S/. ".$row['mr']; ?></td>
              <td>
                <?php
                switch ($row['estado']) {
                  case '1':
                      echo "PROPUESTO";
                    break;
                  case '2':
                      echo "APROVADO";
                    break;
                  case '3':
                    echo "DESAPROBADO";
                    break;
                  case '4':
                    echo "DESEMBOLSADO";
                    break;
                  case '5':
                    echo "CANCELADO";
                    break;
                  case '6':
                    echo "ANULADO";
                    break;
                }
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
