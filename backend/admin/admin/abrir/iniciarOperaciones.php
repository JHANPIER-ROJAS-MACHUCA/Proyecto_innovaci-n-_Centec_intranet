<?php
include('../../../../conection/db7.php');
extract($_POST);
function ordenFecha($dato)
{
  $f=preg_split("~/~",$dato);
  $fecha=$f[2]."-".$f[1]."-".$f[0];
  return $fecha;
}

$fecha=ordenFecha($fecha);
 //usuario ?>
<div class="col-md-4">
  <?php //ide de transferencia ?>

  <label>Usuario:</label>
  <select required class="form-control" id="UsuariOPE" name="UsuariOPE">
    <option value="-1" disabled selected>- - Seleccionar - -</option>
    <?php
    $ido=$_COOKIE['co_ido'];
    if(isset($ido))
    {
      //$identiOficina=$_COOKIE['tofi'];
      $usOpe=extraer("SELECT idU as id,datos as dato FROM tusuario where idO='$ido' and tipo!='1' and tipo!='2' order by datos asc");
      while($row=mysqli_fetch_array($usOpe))
      {
        $iden=$row['id'];
        $cas=extraer("SELECT count(*) as dato from tcaja_usuario where idU='$iden' and montofin is null and fecha='$fecha'");
        $rq=mysqli_fetch_array($cas);
        if($rq['dato']=="1")
        {
    ?>
        <option value="<?php echo $row['id'] ?>" ><?php echo $row['dato']; ?></option>
    <?php
        }
      }
    }
     ?>
  </select>
</div>
<?php //monto ?>
<div class="col-md-2">
  <label>Monto:</label>
  <div class="input-group margin">
    <span class="input-group-btn" disabled>
      <a class="btn btn-info" style="font-weight:bold">S/. </a>
    </span>
    <input autocomplete="off" onkeypress="return numero(event)" onkeyup="veriDiner()" title="Monto a Designar" required maxlength="7" class="form-control" style="text-align:right" placeholder="0.00" type="text" name="MontOPE" id="MontOPE" value="">
  </div>
</div>

<div class="col-md-3">
  <div class="" style="padding-top:22px">
    <a class="btn btn-md btn-info" title="Guardar" onclick="darDesignacion()"><i class="fa fa-save"></i> Guardar</a>
    <a class="btn btn-md btn-danger" title="Limpiar" onclick="limpiarCampos()"> Limpiar</a>
  </div>
</div>
