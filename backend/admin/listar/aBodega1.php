<div class="col-lg-12 table-responsive" style="height: 250px;overflow: auto">
  <table class="table table-striped">
      <thead>
      <tr>
          <th>FECHA</th>
          <th>OFICINA</th>
          <th>MONTO</th>
          <th>TIPO </th>
          <th>ESTADO</th>
          <th></th>
      </tr>
      </thead>
      <tbody>
        <?php
        require_once('../conection/bdcredito.php');
          $his=extraer("SELECT idBo,fecha,monto,concat(Apu,' ',amU,' ',nomU) as datos,if(tipo='1','Deposito','Designado') as tipo,estado, estado as critico,@id:=tcb.idO as id,(select concat(distri,' ',direccion) as fe from toficina tof inner join ta_dis ta on tof.distrito=ta.iddis where idO=@id) as direc FROM tcaja_bodega tcb inner join tusuario tu on tcb.idU=tu.idU order by fecha desc,idBo desc");

          while($row=mysqli_fetch_array($his))
          {
            $fech=preg_split("~-~", $row['fecha']);
            $fecha1=$fech[2].' / '.$fech[1].' / '.$fech[0];
            $moti="";
            switch ($row['estado'])
                {
                case 1:
                    $moti="Por Confirmar";
                    break;
                case 2:
                    $moti="Confirmado";
                    break;
                case 3:
                    $moti="Rechazado";
                    break;
                case 4:
                    $moti="En espera";
                    break;
                }
          ?>
          <tr>
            <td><?php echo $fecha1; ?></td>
            <td><?php echo $row['direc'] ?></td>
            <td>S/. <?php echo $row['monto'] ?></td>
            <td><?php echo $row['tipo'] ?></td>
            <td><?php echo $moti; ?></td>
            <td>
              <?php
              if ($row['estado']=="1" && $row['id']=="" )
              {
                ?>
                <a class='btn btn-sm btn-default' title='Confirmar' onclick="Confi('<?php echo $row['idBo'];?>')"><i class="fa fa-send" style="color:green"></i></a>
                <?php
              }
              if ($row['critico']=="1" || $_COOKIE['tuser']=='7') {
                ?>
                <a class='btn btn-sm btn-default' title='Eliminar Ahorro' onclick="elimi('<?php echo $row['idBo'];?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
              <?php
                }
                ?>
              </td>
          </tr>
          <?php
          }
           ?>
        <tr>
        </tr>
      </tbody>
  </table>
</div>
