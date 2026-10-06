<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($_COOKIE['user1']))
  {
    $identiUsuario=$_COOKIE['user1'];
    $identiOficina=$_COOKIE['tofi'];
    //obtenemos la fecha de la caja abierta por el administrador de la tcaja_oficina
    $consulF=extraer("SELECT ini FROM tcaja_oficina where idO='$identiOficina' and fin is null");
    $row=mysqli_fetch_array($consulF);
    $fe=$row['ini'];
    //obtenemos los nombres y cuotas de los clientes con deuda

  }
  $dato=extraer("SELECT count(*)as data FROM tcaja_usuario where montoIni is not null and idU='$identiUsuario' and montofin is null");
  $f=mysqli_fetch_array($dato);
  if($f['data']=="1")
  {
    $consul=extraer("SELECT tp.idP as ide,tcg.imgC as img,concat(tcg.ap,' ',tcg.am,' ',tcg.nom) as nombre,SUM(CASE WHEN tpd.estado is null or tpd.estado='1' or tpd.estado='2' THEN tpd.cuota ELSE 0 END) - SUM(CASE WHEN tpd.estado='1' or tpd.estado ='2' THEN tpd.montoPagado ELSE 0 END) saldo FROM tclie_general tcg inner join tprestamo tp on tcg.idCG=tp.idCG inner join tpresta_detalle tpd on tp.idP=tpd.idP WHERE tcg.idU='$identiUsuario' and tpd.fechaProg <= '$fe' and tpd.estado is null or tpd.estado='2' group by tcg.idCG");
    while($ro=mysqli_fetch_array($consul))
    {
      ?>
      <div class="col-lg-3">
          <div class="contact-box center-version">
              <a data-toggle="tab" data-nombre="<?php echo $ro['nombre'];  ?>" href="#tab-2" onclick="verDeuda(this.id,<?php echo $fe; ?>)" id="<?php echo "juve1".$ro['ide']; ?>">
                  <img alt="image" class="img-circle" src="img2/clie/<?php echo $ro['img']; ?>">
                  <h3 class="m-b-xs"><?php echo $ro['nombre'];  ?></h3>

                  <address class="m-t-md">
                    <strong>Deuda:</strong> S/. <?php echo $ro['saldo'] ?><br>
                    <!--<strong>Movimiento.:</strong> <?php echo $movimiento ?><br>
                    <strong>Monto.:</strong> S/. <?php echo $cade[2] ?><br>
                    <strong>Fecha.:</strong> <?php echo $fecha1 ?><br>-->
                  </address>
              </a>
          </div>
      </div>

      <?php
    }
  }

 ?>
