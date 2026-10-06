
<?php
require_once('../conection/bdcredito.php');
  $consul=extraer("select (select concat(Apu,' ',amU,' ',nomU,' /7/ ',if(tipo='1','Deposito','Designado'),' /7/ ',distri,' ',direccion,' /7/ ',fecha) from tcaja_bodega tcb inner join tusuario tu on tcb.idU=tu.idU inner join toficina tof on tcb.idO=tof.idO inner join ta_dis ta on tof.distrito=ta.iddis where estado='2' order by idBo desc limit 1) as info,SUM(CASE WHEN tipo = '1' and estado='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '2' and estado='2' THEN monto ELSE 0 END) as saldo from tcaja_bodega");
  $row =mysqli_fetch_array($consul);
  $dato= preg_split("~/7/~", $row['info']);
  $fe=preg_split("~-~",$dato['3']);
  $fecha=$fe['2'].'/'.$fe['1'].'/'.$fe['0'];
 ?>
<div class="col-lg-3">
    <div class="contact-box center-version">
        <a>
            <img alt="image" class="img-circle" src="img2/sede/bobeda.jpg">
            <h3 class="m-b-xs">Saldo: S/. <strong><?php echo $row['saldo'] ?></strong></h3>

            <address class="m-t-md">
              <strong>Información: </strong><br>
              <strong>Fecha: </strong> <?php echo $fecha; ?><br>
              <strong>Usuario: </strong> <?php echo $dato['0']; ?><br>
              <strong>Tipo: </strong>  <?php echo $dato['1']; ?><br>
              <strong>Oficina: </strong> <?php echo $dato['2']; ?><br>

            </address>
        </a>
    </div>
</div>
<div class="col-lg-4" style="border:1px solid #e7eaec">
    <form id="designar">
      <div class="form-group">
        <label>Tipo de Movimiento</label>
        <select class="form-control" name="tipo" id="tipo" required onchange="">
          <option value="-1" disabled selected>- - Seleccione tipo - -</option>
          <option value="1">Deposito</option>
          <option value="2">Designado</option>
        </select>
      </div>
      <div class="form-group">
        <label >Oficina</label>
        <select class="form-control select2_demo_3" id="Ofic" name="Ofic" required>
          <option value="" disabled selected>- - Seleccione - -</option>
          <?php
            $ofi=extraer("select concat(distri,' ',direccion) as dato ,idO as id from toficina tof inner join ta_dis ta on tof.distrito=ta.iddis");
            while($row =mysqli_fetch_array($ofi))
            {
           ?>
              <option value="<?php echo $row['id']; ?>"><?php echo $row['dato']; ?></option>
           <?php
            }
            ?>
        </select>
      </div>

      <div class="form-group">
          <label >Monto: S/.</label>
          <input type="text" class="form-control" name="monto" id="monto" placeholder='Ingrese el monto a designar' value="" onkeypress="return numi(event)" required>
        </div>
        <div class="form-group">
          <input type="reset" value="Cancelar" class="btn btn-sm btn-default">

          <input type="button" onclick='reload7()' name="" value="Agregar" class="btn btn-sm btn-info">
          <a class="btn btn-sm btn-info" onclick="hit()">Actualizar Historial</a>
        </div>
      </div>
    </form>
</div>
