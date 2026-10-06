<?php
include('head.php');
?>
<?php //situacion de cuotas
//include('conection/bdcredito.php');
$consul=extraer("SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico,abre,ruc,dnir,representante,direccion,partida FROM tdatos limit 1");
$r=mysqli_fetch_array($consul);
$icono="titulo/img/1.jpg";
if($r["logo"]!="")
{
  $icono="titulo/img/".$r["logo"];
}
?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
      <div class="btn-group pull-right">
    </div>
  <h5 style="color:white">CONFIGURACION<small style="color:black"> <?php echo $r['comentario'] ?></small></h5>
  </div>
  <div class="panel-body">
    <?php //monto designado por la bobeda ?>

          <div class="tabs-container">
            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">DETALLE</a></li>
            </ul>
            <div class="tab-content">
              <div id="tab-1" class="tab-pane active">
                  <div class="panel-body">
                    <div class="form-group row">
                      <form class="" action="titulo/add.php" enctype="multipart/form-data" method="post">
                        <div class="col-sm-6">
                          <div class="col-md-12">
                            <label >TITULO</label>
                            <input class="form-control" type="text" name="txttitulo"  value="<?php echo $r['titulo'] ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">RUC</label>
                            <input class="form-control" type="text" name="txtruc" maxlength="11" onkeypress="return numero(event)" value="<?php echo $r['ruc']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">NOMBRE DE LA EMPRESA</label>
                            <input class="form-control" type="text" name="txtnombre" value="<?php echo $r['nombreEmpresa']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">TIPO DE EMPRESA</label>
                            <input class="form-control" type="text" placeholder="S.A.C E.I.R.l S.A.A .." name="txttipo" value="<?php echo $r['siglas'] ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">ABREVIATURA</label>
                            <input class="form-control" type="text" name="txtabre" value="<?php echo $r['abre'] ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">SUB NOMBRE</label>
                            <input class="form-control" type="text"  name="txtsub" value="<?php echo $r['subnombre']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">COMENTARIO</label>
                            <input class="form-control" type="text"  name="txtcomentario" value="<?php echo $r['comentario']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">COLOR</label>
                            <input class="form-control" type="color"  name="txtcolor" value="<?php echo $jua1['color'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6" >
                          <div class="col-md-12">
                            <label  for="">DNI REPRESENTANTE LEGAL</label>
                            <input class="form-control" type="text" name="txtdni" maxlength="8" onkeypress="return numero(event)" value="<?php echo $r['dnir']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">NOMBRE DEL REPRESENTANTE</label>
                            <input class="form-control" type="text" name="txtdatos" value="<?php echo $r['representante']; ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">PARTIDA REGISTRAL</label>
                            <input class="form-control" type="text" name="txtpartida" value="<?php echo $r['partida'] ?>">
                          </div>
                          <div class="col-md-12">
                            <label  for="">DIRECCION LEGAL</label>
                            <input class="form-control" type="text" name="txtdireccion" value="<?php echo $r['direccion'] ?>">
                          </div>

                          <div class="col-md-12">
                            <label>LOGO DE LA EMPRESA:</label>
                            <div class="col-sm-9 size-detail">
                                <div class="row">
                                    <div class="col-sm-12">
                                      <div Id="preview">
                                      </div>
                                      <img Id="img1" name="img1" src="<?php echo $icono;?>" style="color:" CssClass="imageslide" width="300px" height="300px"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 contentupload">
                                      <input type="file" name="img" ID="img" style="color:blue">
                                      <div class="js">
                                              <span>Elegir una fotografía en formato JPG&hellip;</span></label>
                                      </div>
                                    </div>
                                </div>
                          </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <input type="submit" class="btn btn-info" name="" value="Guardar">
                          <input type="reset" class="btn btn-danger" name="" value="Limpiar">
                        </div>
                      </form>
                    </div>
                  </div>
              </div>
            </div>
         </div>
    </div>
    <script>

    function readURL(input) {
    if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
    $('#img1').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
    }
    }
    $("#img").change(function() {
    readURL(this);
    });
    </script>
<?php
include('footer.php');
 ?>
