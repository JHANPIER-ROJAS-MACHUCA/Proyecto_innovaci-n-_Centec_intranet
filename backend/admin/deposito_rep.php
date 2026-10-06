<?php include('head.php');
if($_COOKIE['tuser']!='1' && $_COOKIE['tuser']!='7' && $_COOKIE['tuser']!='2')
{
  echo "<script>location.href='index.php'</script>";
}
?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
      <div class="btn-group pull-right">
    </div>

    <h5 style="color:white">Reportes <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" id="crack">
      <div id="">
        <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">REPORTES DEPOSITOS  </a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                          <div class="row form-group">

                            <div class="col-xs-6">
                              <label class="control-label">Motivo:</label>
                              <div class="input-group margin">
                              <select class="form-control" name="lstmotivo" id="lstmotivo" onChange="cuota()">
                                  <option value="-1" disabled selected>- - Seleccione - -</option>
                                  <?php
                                    $mot=extraer("SELECT  idam, motivo, monto FROM tahorro_motivo where monto is not null");
                                     while($row =mysqli_fetch_array($mot))
                                     {
                                       ?>
                                         <option value="<?php echo $row['idam'] ?>"> <?php echo $row['motivo']?></option>
                                       <?php
                                     }
                                   ?>
                              </select>
                              <span class="input-group-btn">
                                <a class="btn btn-info btn-flat " runat="server" title="Añadir trabajador" ><i class="fa fa-search-plus"></i></a>
                                </span>
                              </div>
                            </div>

                          </div>
                          <div class="col-xs-12">
                              <div class="contact-box center-version" id="reportes">

                              </div>
                          </div>
                        </div>
                      </div>
                </div>
        </div>
      </div>

    </div>
</div>
<script src="extra/min.js"></script>
<script src="motivos/reporte/report.js"></script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>


<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>
