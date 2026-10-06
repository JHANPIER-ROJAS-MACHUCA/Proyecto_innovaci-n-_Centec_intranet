<?php
session_start();
 ?>
<?php

//include ('head.php');
include ('include/headlink.php');
include ('include/headbody.php');
 ?>
<?php
 $idCG = 1;
 ?>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1"> Datos</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-2"> Direcciones</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-3"> Negocios</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-4"> Prestamos</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="panel-body">
      <!--tab1-->
      <?php require_once('conection/conex.php'); ?>
<?php
$query_usuario= "SELECT *FROM tclie_general  where idCG=$idCG ";
$usuario= mysqli_query($conex, $query_usuario) or die(mysqli_error());
$row_usuario= mysqli_fetch_assoc($usuario);
$imgC=$row_usuario['imgC'];
$sexo=$row_usuario['sexo'];
?>

   <div class="row m-b-lg m-t-lg">
                <div class="col-md-6">

                    <div class="profile-image">
                      
                          <a class="fun"  data-toggle="modal" data-target="#fotoC<?php echo $row_usuario['idCG']; ?>">
                            <?php
                                if ($imgC=="") {

                                    if ($sexo=="M") {
                                        # code...?>
                                     <img alt="image" class="img-circle" src="img/profile.jpg">
                                    <?php
                                    }else if ($sexo=="F") {
                                        # code...?>
                                     <img alt="image" class="img-circle" src="img/profile2.jpg">
                                    <?php
                                    }

                                }else  {
                                    ?>
                                        <img  src="img2/clie/<?php echo $row_usuario['imgC'] ?>" class="img-circle circle-border m-b-md" alt="profile" >
                                     <?php
                                }
                             ?>


            </a>

                    </div>

         <?php include("modal/fotoC.php");?>

                    <div class="profile-info">


                                 <div class="table-responsive">
                              <table class="table small m-b-xs">
                        <tbody>
                        <tr>
                            <td>
                                <strong>142</strong> Projects
                            </td>
                            <td>
                                <strong>22</strong> Followers
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <strong>61</strong> Comments
                            </td>
                            <td>
                                <strong>54</strong> Articles
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>154</strong> Tags
                            </td>
                            <td>
                                <strong>32</strong> Friends
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
<div class="table-responsive">
                    <small>Sales in last 24h</small>
                    <h2 class="no-margins">206 480</h2>
                    <div id="sparkline1">

                    </div>
                  </div>

                    </div>
                </div>
                <div class="col-md-6">
                     <style>
    .contenedor{margin:60px auto;width:960px;font-family:sans-serif;font-size:15px}
    table {width:100%;box-shadow:0 0 10px #ddd;text-align:left}
    th {padding:5px;background:#fff;color:#555}
    td {padding:5px;border:solid #fff;border-width:0 0 1px;}
        .editable span{display:block;}
        .editable span:hover {background:url(images/edit.png) 90% 50% no-repeat;cursor:pointer}
        td input{height:24px;width:200px;border:1px solid #fff;padding:0 5px;margin:0;border-radius:6px;vertical-align:middle}
        a.enlace{display:inline-block;width:24px;height:24px;margin:0 0 0 5px;overflow:hidden;text-indent:-999em;vertical-align:middle}
            .guardar{background:url(images/save.png) 0 0 no-repeat}
            .cancelar{background:url(images/cancel.png) 0 0 no-repeat}
    .mensaje{display:block;text-align:center;margin:0 0 20px 0}
        .ok{display:block;padding:10px;text-align:center;background:green;color:#fff}
        .ko{display:block;padding:10px;text-align:center;background:red;color:#fff}
    </style>

                    <div class="table-responsive">
                    <div class="mensaje"></div>
                    <table class="editinplace">
                    </table>
                    </div>
                </div>


            </div>



    <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script>
    $(document).ready(function()
    {
        /* OBTENEMOS TABLA */
         /* var idcli = $("#idcli").val();*/
         var idcli = '<?php echo $idCG;?>';
        $.ajax({
            type: "GET",
            /*url: "editinplace.php?idcli=<?php echo $idcli ?>"*/
            url: "editinplace.php?tabla=1",
            data: {idcli:idcli}

        })
        .done(function(json) {
            json = $.parseJSON(json)
            for(var i=0;i<json.length;i++)
            {
                $('.editinplace').append(

                  "<tr width='20'><th>Cod.</th><td class='id'>"+json[i].id+"</td></tr><tr><th>Nombre</th><td class='editable' data-campo='nom'><span>"+json[i].nom+"</span></td></tr><tr><th>Apellidos</th><td class='editable' data-campo='ap'><span>"+json[i].ap+"</span></td></tr><tr><th>E-mail</th><td class='editable' data-campo='correo'><span>"+json[i].correo+"</span></td></tr><tr><th>Tel&eacute;fono</th><td class='editable' data-campo='telefono'><span>"+json[i].telefono+"</span></td></tr><tr><th>celular</th><td class='editable' data-campo='cel'><span>"+json[i].cel+"</span></td></tr><tr><th>Direccion</th><td class='editable' data-campo='direc'><span>"+json[i].direc+"</span></td></tr>");
            }
        });
        var td,campo,valor,id;
        $(document).on("click","td.editable span",function(e)
        {
            e.preventDefault();
            $("td:not(.id)").removeClass("editable");
            td=$(this).closest("td");
            campo=$(this).closest("td").data("campo");
            valor=$(this).text();
            id=$(this).closest("tr").find(".id").text();
            td.text("").html("<input type='text' name='"+campo+"' value='"+valor+"'><a class='enlace guardar' href='#'>Guardar</a><a class='enlace cancelar' href='#'>Cancelar</a>");
        });
        $(document).on("click",".cancelar",function(e)
        {
            e.preventDefault();
            td.html("<span>"+valor+"</span>");
            $("td:not(.id)").addClass("editable");
        });
        $(document).on("click",".guardar",function(e)
        {
            $(".mensaje").html("<img src='images/loading.gif'>");
            e.preventDefault();
            nuevovalor=$(this).closest("td").find("input").val();
            if(nuevovalor.trim()!="")
            {
                $.ajax({
                    type: "POST",
                    url: "editinplace.php",
                    data: { campo: campo, valor: nuevovalor, id:id }
                })
                .done(function( msg ) {
                    $(".mensaje").html(msg);
                    td.html("<span>"+nuevovalor+"</span>");
                    $("td:not(.id)").addClass("editable");
                    setTimeout(function() {$('.ok,.ko').fadeOut('fast');}, 3000);
                });
            }
            else $(".mensaje").html("<p class='ko'>Debes ingresar un valor</p>");
        });
    });
    </script>
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
        var pageTracker = _gat._getTracker("UA-266167-20");
        pageTracker._setDomainName(".martiniglesias.eu");
        pageTracker._trackPageview();
    } catch(err) {}</script>
    <!--FIN-->

                                    </div>
                                </div>

<!--fintab1-->
                                <div id="tab-2" class="tab-pane">
                                    <div class="panel-body">
<!--tab2-->

                                       <?php
require_once 'direccion_entidad.php';
require_once 'direccion_model.php';

// Logica
$alm2 = new Alumno2();
$model2 = new AlumnoModel2();

if(isset($_REQUEST['action2']))
{
    switch($_REQUEST['action2'])
    {
        case 'actualizar2':
            $alm2->__SET('idCD',         $_REQUEST['idCD']);
            $alm2->__SET('idCG',         $_REQUEST['idCG']);
            $alm2->__SET('iddis',    $_REQUEST['txtdis']);
            $alm2->__SET('anexo',         $_REQUEST['anexo']);
            $alm2->__SET('direc',    $_REQUEST['direc']);
            $alm2->__SET('referencia',  $_REQUEST['referencia']);

            $model2->Actualizar2($alm2);
          //header('Location: profile.php?idCG=$idCG');
        // header('Location: profile.php');
         //echo "<META HTTP-EQUIV=Refresh CONTENT=1;URL=profile.php?idCG=$idCG>";
            break;

        case 'registrar2':
             $alm2->__SET('idCG',         $_REQUEST['idCG']);
            $alm2->__SET('iddis',    $_REQUEST['txtdis']);
            $alm2->__SET('anexo',         $_REQUEST['anexo']);
            $alm2->__SET('direc',    $_REQUEST['direc']);
            $alm2->__SET('referencia',  $_REQUEST['referencia']);

            $model2->Registrar2($alm2);
          //  header('Location: profile.php?#tab-3');
           // header('Location: profile.php');

            break;

        case 'eliminar2':
            $model2->Eliminar2($_REQUEST['idCD']);
            //header('Location: profile.php');
       // header('Location: profile.php?idCG=$idCG#tab-3');

            break;

        case 'editar2':
            $alm2 = $model2->Obtener2($_REQUEST['idCD']);
            break;
    }
}

?>
                <form autocomplete="off" action="?action2=<?php echo $alm2->idCD > 0 ? 'actualizar2' : 'registrar2'; ?>" method="post" class="pure-form pure-form-stacked" style="margin-bottom:30px;">
                    <input type="hidden" name="idCD" value="<?php echo $alm2->__GET('idCD'); ?>" />
                    <div class="table-responsive">
                    <table class="tbl-qa table table-bordered" style="width:100%;">
                        <tr>

                            <th width="10%" style="text-align:left;">Region</th>
                             <td>


                                <select  class="form-control m-b" name="txtdepa" id="txtdepa" required>
                                    <option value="">--Seleccione--</option>
                                </select>
                            </td>
                             <th style="text-align:left;">Provincia</th>
                            <td>
                            <select class="form-control m-b" name="txtprovi" id="txtprovi" required>
                                    <option value="">--Seleccione--</option>
                            </select>

                            </td>
                            <th style="text-align:left;">Distrito</th>
                              <td>
                            <select class="form-control m-b" name="txtdis" id="txtdis" required>
                                    <option value="">--Seleccione--</option>
                             </select>
                            </td>
                               <th style="text-align:left;">IDCD</th>
                                  <td><input readonly class="form-control" type="text" name="iddis" value="<?php echo $alm2->__GET('iddis'); ?>" style="width:100%;" /></td>
                        </tr>
                        <script src="extra/buscador.js"></script>
                     <tr>

                            <th style="text-align:left;">Anexo</th>
                            <td><input class="form-control" type="text" name="anexo" value="<?php echo $alm2->__GET('anexo'); ?>" style="width:100%;" /></td>
                               <th style="text-align:left;">Direccion</th>
                            <td><input class="form-control" type="text" name="direc" value="<?php echo $alm2->__GET('direc'); ?>" style="width:100%;" /></td>
                               <th style="text-align:left;">Referencia</th>
                            <td><input class="form-control" type="text" name="referencia" value="<?php echo $alm2->__GET('referencia'); ?>" style="width:100%;" /></td>
                            <th style="text-align:left;">IDCG</th>
                                  <td><input readonly class="form-control" type="text" name="idCG" value=" <?php if (($alm2->__GET('idCG'))=="") { echo "$idCG";}else{echo $alm2->__GET('idCG');}?>" style="width:100%;" /></td>
                     </tr>

                        <tr>
                            <td colspan="2">
                                <button type="submit" class="btn btn-primary" style="width:50%;">Guardar</button>
                                 <a class="btn btn-info" href="profile.php?idCG=<?php echo $idCG ?>">Nuevo</a>
                            </td>
                        </tr>
                    </table>
                   </div>
                </form>
     <div class="table-responsive">
                <table class="tbl-qa table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:left;">idCG</th>
                            <th style="text-align:left;">distrito</th>
                            <th style="text-align:left;">Provincia</th>
                            <th style="text-align:left;">Departamento</th>
                            <th style="text-align:left;">anexo</th>
                            <th style="text-align:left;">Direccion</th>
                            <th style="text-align:left;">Referencia</th>

                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <?php foreach($model2->Listar2($idCG) as $r): ?>
                        <tr>
                            <td><?php echo $r->__GET('idCG'); ?></td>
                            <td><?php echo $r->__GET('distri'); ?></td>
                            <td><?php echo $r->__GET('provi'); ?></td>
                            <td><?php echo $r->__GET('depa'); ?></td>
                            <td><?php echo $r->__GET('anexo'); ?></td>
                            <td><?php echo $r->__GET('direc'); ?></td>
                            <td><?php echo $r->__GET('referencia'); ?></td>

                            <td>
                                <a href="?action2=editar2&idCD=<?php echo $r->idCD; ?>&idCG=<?php echo $r->idCG; ?>">Editar</a>
                            </td>
                            <td>
                                <a href="?action2=eliminar2&idCD=<?php echo $r->idCD; ?>&idCG=<?php echo $r->idCG; ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                </div>
                <!--fin-->

<!-- fin-->

                                    </div>
                                </div>

                                <div id="tab-3" class="tab-pane">
                                    <div class="panel-body">


  <!--
    tab3-->
                                       <?php
require_once 'negocio_entidad.php';
require_once 'negocio_model.php';

// Logica
$alm = new Alumno();
$model = new AlumnoModel();

if(isset($_REQUEST['action']))
{
    switch($_REQUEST['action'])
    {
        case 'actualizar':
            $alm->__SET('idCN',         $_REQUEST['idCN']);
            $alm->__SET('idCG',         $_REQUEST['idCG']);
            $alm->__SET('direccion',    $_REQUEST['direccion']);
            $alm->__SET('tipo',         $_REQUEST['tipo']);
            $alm->__SET('tipoLocal',    $_REQUEST['tipoLocal']);
            $alm->__SET('tipoNegocio',  $_REQUEST['tipoNegocio']);
             $alm->__SET('tiempo',      $_REQUEST['tiempo']);
            $model->Actualizar($alm);
           //header('Location: profile.php?#tab-3');
         // header('Location: profile.php');
            break;

        case 'registrar':
            $alm->__SET('idCG',         $_REQUEST['idCG']);
            $alm->__SET('direccion',    $_REQUEST['direccion']);
            $alm->__SET('tipo',         $_REQUEST['tipo']);
            $alm->__SET('tipoLocal',    $_REQUEST['tipoLocal']);
            $alm->__SET('tipoNegocio',  $_REQUEST['tipoNegocio']);
            $alm->__SET('tiempo',         $_REQUEST['tiempo']);

            $model->Registrar($alm);
          //  header('Location: profile.php?#tab-3');
           // header('Location: profile.php');

            break;

        case 'eliminar':
            $model->Eliminar($_REQUEST['idCN']);
            //header('Location: profile.php');
       // header('Location: profile.php?idCG=$idCG#tab-3');

            break;

        case 'editar':
            $alm = $model->Obtener($_REQUEST['idCN']);
            break;
    }
}

?>
                <form autocomplete="off" action="?action=<?php echo $alm->idCN > 0 ? 'actualizar' : 'registrar'; ?>" method="post" class="pure-form pure-form-stacked" style="margin-bottom:30px;">
                    <input type="hidden" name="idCN" value="<?php echo $alm->__GET('idCN'); ?>" />
                         <div class="table-responsive">
                    <table class="tbl-qa table table-bordered" style="width:100%;">
                        <tr>
                            <th style="text-align:left;">Direccion</th>
                            <td><input class="form-control" type="text" name="direccion" value="<?php echo $alm->__GET('direccion'); ?>" style="width:100%;" /></td>
                            <th style="text-align:left;">Tipo</th>
                             <td>
                                <select class="form-control" name="tipo" style="width:100%;">
                                    <option value="ambulante" <?php echo $alm->__GET('tipo') == 'ambulante' ? 'selected' : ''; ?>>ambulante</option>
                                    <option value="puesto mercado" <?php echo $alm->__GET('tipo') == 'puesto mercado' ? 'selected' : ''; ?>>puesto mercado</option>
                                      <option value="establecimiento" <?php echo $alm->__GET('tipo') == 'establecimiento' ? 'selected' : ''; ?>>establecimiento</option>
                                </select>
                            </td>
                             <th style="text-align:left;">Tipo Local</th>
                            <td>
                                <select class="form-control" name="tipoLocal" style="width:100%;">
                                    <option value="alquilado" <?php echo $alm->__GET('tipoLocal') == 'alquilado' ? 'selected' : ''; ?>>Alquilado</option>
                                    <option value="propio" <?php echo $alm->__GET('tipoLocal') == 'propio' ? 'selected' : ''; ?>>Propio</option>
                                </select>
                            </td>

                        </tr>
                     <tr>
                                 <th style="text-align:left;">Tipo Negocio</th>
                              <td>
                                <select class="form-control" name="tipoNegocio" style="width:100%;">
                                    <option value="comercio" <?php echo $alm->__GET('tipoNegocio') == 'comercio' ? 'selected' : ''; ?>>Comercio</option>
                                    <option value="servicios" <?php echo $alm->__GET('tipoNegocio') == 'servicios' ? 'selected' : ''; ?>>Servicios</option>
                                      <option value="produccion" <?php echo $alm->__GET('tipoNegocio') == 'produccion' ? 'selected' : ''; ?>>Produccion</option>
                                </select>
                            </td>
                            <th style="text-align:left;">Tiempo</th>
                            <td><input class="form-control" type="text" name="tiempo" value="<?php echo $alm->__GET('tiempo'); ?>" style="width:100%;" /></td>
                            <th style="text-align:left;">IDCG</th>


                                  <td><input readonly class="form-control" type="text" name="idCG" value=" <?php if (($alm->__GET('idCG'))=="") { echo "$idCG";}else{echo $alm->__GET('idCG');}?>" style="width:100%;" /></td>
                     </tr>

                        <tr>
                            <td colspan="2">
                                <button type="submit" class="btn btn-primary" style="width:50%;">Guardar</button>
                                 <a class="btn btn-info" href="profile.php?idCG=<?php echo $idCG ?>">Nuevo</a>
                            </td>
                        </tr>
                    </table>
                </div>
                </form>
     <div class="table-responsive">
                <table class="tbl-qa table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:left;">idCG</th>
                            <th style="text-align:left;">Direccion</th>
                            <th style="text-align:left;">Tipo</th>
                            <th style="text-align:left;">TipoLocal</th>
                            <th style="text-align:left;">TipoNegocio</th>
                            <th style="text-align:left;">Tiempo</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <?php foreach($model->Listar($idCG) as $r): ?>
                        <tr>
                            <td><?php echo $r->__GET('idCG'); ?></td>
                            <td><?php echo $r->__GET('direccion'); ?></td>
                            <td><?php echo $r->__GET('tipo'); ?></td>
                             <td><?php echo $r->__GET('tipoLocal'); ?></td>
                            <td><?php echo $r->__GET('tipoNegocio'); ?></td>
                             <td><?php echo $r->__GET('tiempo'); ?></td>
                            <td>
                                <a href="?action=editar&idCN=<?php echo $r->idCN; ?>&idCG=<?php echo $r->idCG; ?>">Editar</a>
                            </td>
                            <td>
                                <a href="?action=eliminar&idCN=<?php echo $r->idCN; ?>&idCG=<?php echo $r->idCG; ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                  </div>
                <!--fin-->
                                    </div>
                                </div>
                                <!--tab3-->

<div id="tab-4" class="tab-pane">
    <div class="panel-body">
        <!--BODY-->

            <div class="row">
                <div class="col-sm-5">
                    <div class="ibox">
                        <div class="ibox-content">
                            <span class="text-muted small pull-right">Last modification: <i class="fa fa-clock-o"></i> 2:10 pm - 18.02.2019</span>
                            <div class="clients-list">
                            <ul class="nav nav-tabs">
                                <span class="pull-right small text-muted">1406 Elements</span>

                                <li class="active"><a data-toggle="tab" href="#tab-22"><i class="fa fa-briefcase"></i> Historial</a></li>
                            </ul>
                            <div class="tab-content">

                                <div id="tab-22"  class="tab-pane active">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <!--tab22-->
 <?php
 $_SESSION['idCG']  = $idCG;
 ?>  <!--tab22-->
     <?php echo "$idCG"; ?>
<div class="panel panel-info">
      <div class="panel-heading">
          <div class="btn-group pull-right">
            <a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agrePrest' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Prestamo</a>
        </div>
    <h5 style="color:white">Reporte de Prestamos<small> </small></h5>
      </div>
      <div class="panel-body">
        <form class="form-horizontal" role="form" id="datos_cotizacion">
              <div class="form-group row">
                <!--<label for="q" class="col-md-2 control-label">Dni o Nombres</label>-->
                <div class="col-md-5">
                    <!--
                  <input type="text" class="form-control" id="q" placeholder="Dni o Nombres" onkeyup='load(1);'>
              -->
                </div>
                <div class="col-md-3">
                    <!--
                  <button type="button" class="btn btn-default" onclick='load(1);'>
                    <span class="glyphicon glyphicon-search" ></span> Buscar</button>
                -->
                  <span id="loader"></span>
                </div>
              </div>
        </form>
          <div id="listarP">
          </div>
        </div>
     </div>
<?php include('modal/agrePrestamo.php'); ?>
<!--tab22-->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="ibox ">

                        <div class="ibox-content">
                            <div class="tab-content">
                                <div id="company-2" class="tab-pane">
                                    <div class="m-b-lg">
                                        <h2>P</h2>

                                        <p>

                                        </p>
                                        <div>
                                            <small>Active project completion with: 22%</small>
                                            <div class="progress progress-mini">
                                                <div style="width: 22%;" class="progress-bar"></div>
                                            </div>
                                        </div>
                                    </div>
                                      <!--mdal -->
                                      <div class="clients-list">
                            <ul class="nav nav-tabs">
                                <span class="pull-right small text-muted">1406 Elements</span>
                                <li class="active"><a data-toggle="tab" href="#tab-44"><i class="fa fa-user"></i> Prestamo</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-55"><i class="fa fa-briefcase"></i> Documentos</a></li>
                                 <li class=""><a data-toggle="tab" href="#tab-66"><i class="fa fa-briefcase"></i> Vinculacion</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-44" class="tab-pane active">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                         idPrestamo
                                              <input style="width:35px;height:25px" class="form-control" readonly type="text" name="txtidP" id="txtidP" value="">
                                              idCliente

                                        </div>
                                    </div>
                                </div>
                                <div id="tab-55" class="tab-pane">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            tab5
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-66" class="tab-pane">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                             <input style="width:35px;height:25px" class="form-control" readonly type="text" name="txtidP6" id="txtidP6" value="">
                                                  <input style="width:35px;height:25px"  class="form-control" readonly type="text" name="txtidCG2" id="txtidCG2" value="">
                                            <!--cliente--><br>
            <div id="listarV">
          </div>

                                    </div>
                                </div>
                            </div>

                            </div>
                                  <!--  <div class="client-detail">
                                        <div class="full-height-scroll">

                                            <strong>Last activity</strong>

                                            <ul class="list-group clear-list">
                                                <li class="list-group-item fist-item">
                                                    <span class="pull-right"> <span class="label label-warning">WAITING</span> </span>
                                                    Aldus PageMaker
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="pull-right"><span class="label label-primary">NEW</span> </span>
                                                    Lorem Ipsum, you need to be sure
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="pull-right"> <span class="label label-danger">BLOCKED</span> </span>
                                                    The generated Lorem Ipsum
                                                </li>
                                            </ul>
                                            <strong>Notes</strong>
                                            <p>
                                                Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.
                                            </p>
                                            <hr/>
                                            <strong>Timeline activity</strong>
                                            <div id="vertical-timeline" class="vertical-container dark-timeline">
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon gray-bg">
                                                        <i class="fa fa-coffee"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>Conference on the sales results for the previous year.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 2:10 pm - 12.06.2014 </span>
                                                    </div>
                                                </div>
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon gray-bg">
                                                        <i class="fa fa-briefcase"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>Many desktop publishing packages and web page editors now use Lorem.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 4:20 pm - 10.05.2014 </span>
                                                    </div>
                                                </div>
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon gray-bg">
                                                        <i class="fa fa-bolt"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>There are many variations of passages of Lorem Ipsum available.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 06:10 pm - 11.03.2014 </span>
                                                    </div>
                                                </div>
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon navy-bg">
                                                        <i class="fa fa-warning"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>The generated Lorem Ipsum is therefore.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 02:50 pm - 03.10.2014 </span>
                                                    </div>
                                                </div>
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon gray-bg">
                                                        <i class="fa fa-coffee"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>Conference on the sales results for the previous year.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 2:10 pm - 12.06.2014 </span>
                                                    </div>
                                                </div>
                                                <div class="vertical-timeline-block">
                                                    <div class="vertical-timeline-icon gray-bg">
                                                        <i class="fa fa-briefcase"></i>
                                                    </div>
                                                    <div class="vertical-timeline-content">
                                                        <p>Many desktop publishing packages and web page editors now use Lorem.
                                                        </p>
                                                        <span class="vertical-date small text-muted"> 4:20 pm - 10.05.2014 </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

 <!--BODY-->
</div>
 </div>
                    </div>
                </div>
            </div>
        </div>
<?php
//include ('footer.php');
include ('include/footer2.php');
 ?>
 <script type="text/javascript">
  function actualizar2(){location.reload();}
//Función para actualizar cada 4 segundos(4000 milisegundos)

</script>
<script src="../js/fileinput.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/fileinput.min.css">
    <!--estadistica -->
    <script src="../js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script>
        $(document).ready(function() {


            $("#sparkline1").sparkline([34, 43, 43, 35, 44, 32, 44, 48], {
                type: 'line',
                width: '100%',
                height: '50',
                lineColor: '#1ab394',
                fillColor: "transparent"
            });


        });
    </script>
    <script type="text/javascript" src="js/VentanaCentrada.js"></script>
    <!-- -->
