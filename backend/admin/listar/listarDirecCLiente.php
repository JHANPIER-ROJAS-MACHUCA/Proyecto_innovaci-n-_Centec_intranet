<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  if(isset($ide))
  {
    $dta=extraer("select idCD as id,depa,provi,distri,anexo,direc,referencia from ta_depa td inner join ta_prov tp on td.iddepa=tp.iddepa inner join ta_dis tad on tp.idprov=tad.idprov inner join tclie_direccion tcd on tcd.iddis=tad.iddis where tcd.idCG='$ide' order by idCD desc");
    ?>
    <div class="col-sm-12 table-responsive">
      <table class="table table-striped">
          <thead>
            <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
              <th>
                DEPARTAMENTO
              </th>
              <th>
                PROVINCIA
              </th>
              <th>
                DISTRITO
              </th>
              <th>
                LUGAR
              </th>
              <th>
                DIRECCION
              </th>
              <th>
                REFERENCIA
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
              <td><?php echo $row['depa']; ?></td>
              <td><?php echo $row['provi']; ?></td>
              <td><?php echo $row['distri']; ?></td>
              <td><?php echo $row['anexo']; ?></td>
              <td><?php echo $row['direc']; ?></td>
              <td><?php echo $row['referencia']; ?></td>
              <td>  <?php //if ($row['critico']=="1" || $_COOKIE['tuser']=='7') {
                  ?>
                  <a class='btn btn-sm btn-default' title='Eliminar direccion' onclick="eli('<?php echo $row['id'];?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
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
