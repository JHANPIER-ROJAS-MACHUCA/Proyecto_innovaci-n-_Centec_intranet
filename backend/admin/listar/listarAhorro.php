<?php
include('../conection/bdcredito.php');
date_default_timezone_set('america/lima');
$f= date("d/m/Y ");
extract($_POST);
if(!isset($tipoA))
{
   /*$usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
(SELECT SUM(CASE WHEN tipo = '1' or tipo='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '3' or tipo='4' THEN monto ELSE 0 END) SALDO
FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
 inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' group by ta.idA");*/

 $usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
(SELECT SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) SALDO
FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' group by ta.idA");
}
else
{
    if($tipoA=='2')
    {
     if(isset($ide))
      {
      /* $usus=extraer("select @id:=ta.idA as id,concat('img2/user/',img) as img,concat(apU,' ',amU,' ',nomU)as datos,
        tu.idU as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
        (SELECT SUM(CASE WHEN tipo = '1' or tipo='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '3' or tipo='4' or tipo='5' THEN monto ELSE 0 END) SALDO
        FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tusuario tu on ta.id=tu.idU
         inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='2' and id='$ide' group by ta.idA");*/

         $usus=extraer("select @id:=ta.idA as id,concat('img2/user/',img) as img,concat(apU,' ',amU,' ',nomU)as datos,
          tu.idU as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
          (SELECT SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) SALDO
          FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tusuario tu on ta.id=tu.idU
           inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='2' and id='$ide' group by ta.idA");
      }
      else
      {
        /*$usus=extraer("select @id:=ta.idA as id,concat('img2/user/',img) as img,concat(apU,' ',amU,' ',nomU)as datos,
        tu.idU as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
        (SELECT SUM(CASE WHEN tipo = '1' or tipo='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '3' or tipo='4' or tipo='5' THEN monto ELSE 0 END) SALDO
        FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tusuario tu on ta.id=tu.idU
         inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='2' group by ta.idA");*/
         $usus=extraer("select @id:=ta.idA as id,concat('img2/user/',img) as img,concat(apU,' ',amU,' ',nomU)as datos,
         tu.idU as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
         (SELECT SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) SALDO
         FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tusuario tu on ta.id=tu.idU
          inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='2' group by ta.idA");
      }
    }
    else if($tipoA=='1')
    {
    if(isset($ide))
      {
        /*$usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
        idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
        (SELECT SUM(CASE WHEN tipo = '1' or tipo='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '3' or tipo='4' or tipo='5' THEN monto ELSE 0 END) SALDO
        FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
         inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' and id='$ide' group by ta.idA");*/

         $usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
         idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
         (SELECT SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) SALDO
         FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
          inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' and id='$ide' group by ta.idA");
      }
      else
      {
      /*  $usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
        idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
        (SELECT SUM(CASE WHEN tipo = '1' or tipo='2' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '3' or tipo='4' or tipo='5' THEN monto ELSE 0 END) SALDO
        FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
         inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' group by ta.idA");
         */
         $usus=extraer("select @id:=ta.idA as id,concat('img2/clie/',imgC) as img,concat(ap,' ',am,' ',nom)as datos,
         idCG as identi,(select concat(fecha,',',tipo,',',monto) from tahorro_deta where idA=@id order by idAd desc limit 1) as fecha,
         (SELECT SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) SALDO
         FROM tahorro_deta where idA=@id) as saldo from tahorro ta inner join tclie_general tc on ta.id=tc.idCG
          inner join tahorro_deta tad on ta.idA=tad.idA where ta.tipoA='1' group by ta.idA");
      }
    }
}
?>
<div class="tabs-container">
  <link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">AHORROS DE CLIENTES </a></li>

        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <div class="panel-body">
      <?php
        while($row =mysqli_fetch_array($usus))
        {
          $cade=preg_split("~,~", $row['fecha']);
          $movimiento="";
              if($cade[1]=='1')
              {
                $movimiento="Depósito";
              }
              else if($cade[1]=='7')
              {
                $movimiento="Ahorro";
              }
              else if($cade[1]=='8')
              {
                $movimiento="Retiro";
              }
              else if($cade[1]=='4')
              {
                $movimiento="Descuento";
              }
              else if($cade[1]=='5')
              {
                $movimiento="Adelanto";
              }
              $fech=preg_split("~-~", $cade[0]);
              $fecha1=$fech[2].' / '.$fech[1].' / '.$fech[0];
        ?>
        <div class="col-lg-3">
            <div class="contact-box center-version">
                <a data-toggle="tab" href="#tab-2" onclick="nombreA(this.id)" id="<?php echo "juve1".$row['id']; ?>"
                  data-nom='<?php echo $row['datos'] ;?>'
                  data-id="<?php echo $row['identi']; ?>">
                    <img alt="image" class="img-circle" src="<?php echo $row['img']; ?>">
                    <h3 class="m-b-xs"><?php echo $row['datos'];  ?></h3>

                    <address class="m-t-md">
                      <strong>Saldo:</strong> S/. <?php echo $row['saldo'] ?><br>
                      <strong>Movimiento.:</strong> <?php echo $movimiento ?><br>
                      <strong>Monto.:</strong> S/. <?php echo $cade[2] ?><br>
                      <strong>Fecha.:</strong> <?php echo $fecha1 ?><br>
                    </address>
                </a>
            </div>
        </div>
        <?php
        }
        ?>
      </div>
    </div>
    <div id="tab-2" class="tab-pane">
              <div class="panel-body">
                  <fieldset class="form-horizontal">
                    <div class="form-group">
                      <label class="col-sm-5 control" id="tituloAhorro2"></label>
                    </div>
                    <form id="FAho">
                      <div class="form-group">
                        <label class="col-sm-1 control-label">Tipo:</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="Atipo" required>
                              <option value="" disabled selected>- - Seleccione - -</option>
                            <!--  <option value="1">Deposito</option>-->
                              <option value="7">Ahorro</option>
                              <option value="8">Retiro</option>
                              <!--<option value="4">Descuento</option>
                              <option value="5">Adelanto</option>-->
                          </select>
                        </div>

                        <label class="col-sm-1 control-label">Motivo:</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="lstmotivo" id="lstmotivo" required  onChange="cuota()">
                              <option value="-1" disabled selected>- - Seleccione - -</option>
                              <?php
                                $mot=extraer("SELECT  idam, motivo, monto FROM tahorro_motivo where tipoM='1' and monto is not null");
                                 while($row =mysqli_fetch_array($mot))
                                 {
                                   ?>
                                     <option value="<?php echo $row['idam']."/". $row['monto'] ?>"> <?php echo $row['motivo']?></option>
                                   <?php
                                 }
                               ?>
                          </select>
                        </div>
                          <label class="col-sm-1 control-label">Monto:</label>
                          <div class="col-sm-3">
                            <input type="text" class="form-control" onkeypress="return numi(event)" name="Amonto" id="Amonto" required>
                            <input type="hidden" name="Aid" id="Aid" value="">
                            <input type="hidden" name="Ati" id="Ati" value="">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-1 control-label">Fecha.:</label>
                        <div class="col-sm-3">
                          <input type="text" value="<?php echo $f; ?>" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="Afecha" id="Afecha" required>
                        </div>
                        <div class="col-sm-3">
                          <?php
                            if($limitadorfinal==0)
                            {
                           ?>
                          <input type="submit"  id="btnguardar" class="btn btn-info btn-flat " name="" value="Guardar">
                            &nbsp;
                            <?php
                            }
                             ?>
                          <a data-toggle="tab" onclick="canceli()" href="#tab-1" class="btn btn-default btn-flat">Cancelar</a>
                        </div>
                      </div>
                    </form>
                    <div class="ibox-content table-responsive" id="registro">
                    </div>
                  </fieldset>
                </div>
              </div>
<!--<script src="../functiones/oficina.js">
</script>-->
<script type="text/javascript">
$(document).ready(function(){
        $("#idresponsa").select2({
              minimumResultsForSearch: 5,
              placeholder: "- - Seleccione Responsable - -",
              allowClear: false,
              width: '100%',
          });
  });
  function cuota()
  {
    var ele=$('#lstmotivo').val();
    var precio = ele.split("/");
    $('#Amonto').val(precio[1]);
  }
</script>
        </div>
    </div>
  </div>
</div>
 <script src="functiones/ahorro.js"></script>

 <script src="fecha/moment.js"></script>
 <script src="fecha/bootstrap-material-datetimepicker.js"></script>
 <script>
        $('.datepicker').bootstrapMaterialDatePicker({
            weekStart: 0,
            time: false,
            format: 'DD/MM/YYYY'
        });
 </script>
