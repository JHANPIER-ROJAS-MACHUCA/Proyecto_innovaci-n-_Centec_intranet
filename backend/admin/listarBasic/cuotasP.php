<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($ide))
  {
    //identificamos el prestamo
    $id=str_replace("juve1","",$ide);
    $dat=preg_split("~/~", $fecha);
    //ordenamos la fecha
    $fe=$dat[2].'-'.$dat[1].'-'.$dat[0];
    ?>

                <div class="col-md-12 table-responsive">
                  <table class="table table-striped">
                      <thead>
                        <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
                          <th style="text-align:center">
                            FECHA PROGRAMADA
                          </th>
                          <th style="text-align:center">
                            CUOTA
                          </th>
                          <th style="text-align:center">
                            MORA
                          </th>
                          <th style="text-align:center">
                            MONTO POR PAGAR
                          </th>
                          <th style="text-align:center">
                            OPCIONES
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                <?php
                //generamos la consulta suprema por el programador Juve Del Rio
                $defi=extraer("SELECT mora from tprestamo where idP='$id'");
                $ro=mysqli_fetch_array($defi);
                $more=$ro['mora'];
                $consul=extraer("select idPD,fechaProg as fe,(if(estado='2' or estado is null,cuota+'0.5',cuota) - IFNULL(montoPagado,0)) as saldo,(cuota - IFNULL(montoPagado,0)) as saldo1,if(estado='2' or estado is null,'$more',0) as mora from tpresta_detalle where idP='$id' and fechaProg<'$fe' and estado is null or estado='2' union select idPD,fechaProg as fe,(cuota- IFNULL(montoPagado,0)) as saldo,(cuota - IFNULL(montoPagado,0)) as saldo1,(0) as mora from tpresta_detalle where idP='$id' and fechaProg='$fe' and (estado is null or estado='2')");
                while ($row=mysqli_fetch_array($consul))    {
                  ?>
                  <tr>
                    <td style="text-align:center">
                      <?php echo $row['fe']; ?>
                    </td>
                    <td style="text-align:center">
                      <?php echo "S/. ".$row['saldo1']; ?>
                    </td>
                    <td style="text-align:center">
                      <?php echo "S/. ".number_format($row['mora'],2); ?>
                    </td>
                    <td style="text-align:center">
                      <?php echo "S/. ".number_format($row['saldo'],2); ?>
                    </td>
                    <td style="text-align:center">
                      <a class="btn btn-sm btn-success" onclick="pagarCuota(<?php echo $row['idPD'] ?>,<?php echo $row['mora']; ?>,<?php echo $row['saldo1'] ?>)">PAGAR</a>
                         <a href="#" class='btn btn-default' title='Descargar Boucher' onclick="imprimir_boucher('<?php echo $row['idPD'];?>');"><i class="fa fa-print"></i></a>
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
