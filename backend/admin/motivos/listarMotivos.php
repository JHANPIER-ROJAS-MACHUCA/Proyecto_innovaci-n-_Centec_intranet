<?php
  include("../conection/bdcredito.php");
  ?>
    <div class="col-sm-12 table-responsive">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <th>
                MOTIVO
              </th>
              <th>
                FECHAS
              </th>
              <th>
                MONTO DE CUOTA
              </th>

              <th>
                OPCIONES
              </th>

            </tr>
          </thead>
          <tbody>
            <?php
            $data=extraer("SELECT idam, motivo, if(fechas=1,'SI','NO') AS fecha, monto,estado FROM tahorro_motivo where tipoM='1' and estado='1' and monto is not null order by idam desc");
            while ($row=mysqli_fetch_array($data))
            {
              ?>
              <tr>
                <td><?php echo $row['motivo']; ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td><?php echo "S/. ".$row['monto']; ?></td>

                <td>
                  <a type="button" class="btn btn-primary" title="Editar Motivo" onclick="editarMotivo('<?php echo $row['idam']; ?>','<?php echo $row['motivo']; ?>','<?php echo $row['monto']; ?>')" ><i class="fa fa-gear"></i></a>
                  <?php
                    if($row["fecha"]=="SI")
                    {
                   ?>
                  <a type="button" class="btn btn-info" title="Configurar Motivo" onclick="confiMotivo(<?php echo $row['idam']; ?>)" ><i class="fa fa-gears"></i></a>
                  <?php
                      }
                   ?>
                  <a type="button" class="btn btn-danger" title="Eliminar Motivo" onclick="eliminarMotivo(<?php echo $row['idam']; ?>)" ><i class="fa fa-trash-o"></i></a>
                </td>
              </tr>
              <?php
            }
            ?>
            </tbody>
          </table>
          </div>
