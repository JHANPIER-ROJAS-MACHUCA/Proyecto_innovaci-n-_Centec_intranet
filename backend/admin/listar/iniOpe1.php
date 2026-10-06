<?php
include('../conection/bdcredito.php');
extract($_POST);
 ?>
<div class="col-md-12 table-responsive">
  <table class="table table-striped">
      <thead>
        <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
          <th>
            USUARIO
          </th>
          <th>
            MONTO
          </th>
          <th>
            TIPO
          </th>
          <th>
            ESTADO
          </th>
          <th>
            OPCIONES
          </th>
        </tr>
      </thead>
      <tbody>
        <?php
        extract($_POST);
        if(isset($_COOKIE['tofi']))
        {
          $identiOficina=$_COOKIE['tofi'];
          //$registro=extraer("SELECT  idCOD, concat(apU,' ',amU,' ',nomU) as dato, tcod.monto as monto, tipo, habilitacion, estado FROM tcaja_ofi_deta tcod inner join tcaja_oficina tco on tcod.idCO=tco.idCO inner join tusuario tu on tcod.idU=tu.idU where tcod.idO='$identiOficina' and tcod.idCO='$ideNece' order by idCOD desc");
          $registro=extraer("SELECT tca.idCAD as id,tco.idCO,tcu.idCA,tca.monto as monto,tca.tipo as tipo,tca.habilitacion as habi,concat(apU,' ',amU,' ',nomU) as datos FROM tcaja_oficina tco inner join tcaja_usuario tcu on tco.idCO=tcu.idCO inner join tcaja_usu_detal tca on tcu.idCA=tca.idCA inner join tusuario tus on tcu.idU=tus.idU where tco.idO='$identiOficina' and tco.idCO=(select idCO from tcaja_oficina where idO='$identiOficina' order by idCO desc limit $identiOficina)  and (tca.tipo='$identiOficina' or tca.tipo='2') and tca.monto is not null order by tca.idCAD desc");
          while ($row=mysqli_fetch_array($registro))
          {
            $tipoUSOPE="";
            //TIPO DE ACCION
            switch ($row['tipo'])
            {
              case '1':
                  $tipoUSOPE="DESIGNADO";
                break;
              case '2':
                  $tipoUSOPE="DESEMBOLSO";
                break;
            }
            //ESTADO
            $tipoEstado="";
            switch ($row['habi'])
            {
              case '1':
                $tipoEstado="POR CONFIRMAR";
                break;
              case '2':
                $tipoEstado="EN ESPERA";
                break;
              case '3':
                $tipoEstado="EXTORNADO";
                break;
              case '4':
                $tipoEstado="CONFIRMADO";
                break;
            }
         ?>
        <tr>
          <td><?php echo $row['datos']?></td>
          <td><?php echo "S/. ".$row['monto']?></td>
          <td><?php echo $tipoUSOPE;?></td>
          <td><?php echo $tipoEstado;?></td>
          <td>
            <?php
                if($row['habi']=='1' || $row['habi']=='2' || $row['habi']=='3' || $_COOKIE['tuser']=='7')
                {
             ?>
             <a class="btn btn-sm btn-danger" onclick="Elimi(<?php echo $row['id']; ?>)">Eliminar</a></td>
            <?php
                }
             ?>
        </tr>
        <?php
         }
        }
        ?>
      </tbody>
  </table>
</div>
