<?php include('head2.php');
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
      <div id="juve">
        <div class="tabs-container" >
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">REPORTES DEUDORES</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                          <div class="col-xs-12">
                              <div id="reportes">
                                <?php
                                $consul=extraer("select dni,concat(ap,' ',am,' ',nom)as dato,direc as dir,cel,@id:=tp.idP as id,tp.montoAprovado as mont, taza,@monto:=(round((montoAprovado*((taza+100)/100)),2))as suma,round((@monto-(SELECT sum(if(montoPagado is null,0,montoPagado))  FROM tpresta_detalle where idP=@id)),2)as saldo from tprestamo tp inner join tclie_general tc on tp.idCG=tc.idCG  where tp.estado='4' order by saldo desc");

                                ?>
                                <table class="table dataTables-example"  style="width: 100%; font-size:10px">
                                    <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>CLIENTE</th>
                                        <th>DIRECCION</th>
                                        <th>CELULAR</th>
                                        <th>PRESTAMO DE:</th>
                                        <th>TAZA</th>
                                        <th>PENDIENTE</th>
                                        <!--<th> <button type="submit" class="btn btn-default"  title='Descargar Lista'  onclick="imprimir_ahorro('juve');">
                                            <span class="glyphicon glyphicon-print"></span> Imprimir
                                          </button> </th>-->

                                    </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                        while($row =mysqli_fetch_array($consul))
                                        {
                                      ?>
                                    <tr>
                                      <td><?php echo $row['dni']; ?></td>
                                      <td><?php echo $row['dato']; ?></td>
                                      <td><?php echo $row['dir']; ?></td>
                                      <td><?php echo $row['cel']; ?></td>
                                      <td><?php echo $row['mont']; ?></td>
                                      <td><?php echo $row['taza'].' %'; ?></td>
                                      <td><?php echo $row['saldo']; ?></td>
                                      <!--<td>
                                      </td>-->
                                    </tr>
                                      <?php
                                        }
                                      ?>
                                  </tbody>
                                </table>
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
<script >
function imprimir_ahorro(nombreDiv){
    //  VentanaCentrada('./pdf/documentos/ver_ahorro.php?idahorro='+idahorro,'Ahorro','','1024','768','true');
    var contenido= document.getElementById(nombreDiv).innerHTML;
    var contenidoOriginal= document.body.innerHTML;

    document.body.innerHTML = contenido;

    window.print();

    document.body.innerHTML = contenidoOriginal;
    }
</script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
    $(document).ready(function(){

        $('.dataTables-example').DataTable({
            pageLength: 15,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                /*{ extend: 'copy'},
                {extend: 'csv'},*/
                {extend: 'excel', title: 'Deudores '},
                /*{extend: 'pdf', title: 'ExampleFile'},*/
                {extend: 'print',
                 customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                }
                }
            ]
        });
    });
</script>

<!--<script src="functiones/poder.js">-->
<?php include('footer.php'); ?>
