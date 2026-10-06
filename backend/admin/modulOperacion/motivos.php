<?php
  include('../conection/bdcredito.php');
    //ver los motivos

    ?>
      <link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover dataTables-example" >
        <thead >
          <tr>
            <th style="background:<?php echo $jua1['color']?> ">MOTIVO</th>
            <th style="background:<?php echo $jua1['color']?> ">TIPO</th>
            <th style="background:<?php echo $jua1['color']?> "></th>
          </tr>
          </thead>
          <tbody>
            <?php
            $consul=extraer("SELECT idam as id,motivo,tipoM,fechas FROM tahorro_motivo where estado='1' and monto is null order by idam desc");
              while ($r=mysqli_fetch_array($consul))
              {
                $tipo="INGRESO";
                IF($r['tipoM']==2)
                {
                  $tipo="EGRESO";
                }
               ?>
              <tr id="moti<?php echo $r['id']?>">
                <td><?php echo $r['motivo'] ?></td>
                <td><?php echo $tipo ?></td>
                <td>
                  <?php if($r['fechas']==0){ ?>
                  <a class="btn btn-primary" title="Eliminar Motivo" onclick="editarMoti('<?php echo $r['id'] ?>','<?php echo $r['motivo'] ?>','<?php echo $r['tipoM'] ?>')"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-danger" title="Eliminar Motivo" onclick="eliminarMotivo(<?php echo $r['id'] ?>)"><i class="fa fa-trash"></i></a>
                <?php } ?>
                </td>
              </tr>
            <?php
              }
            ?>
           </tbody>
          </table>
  </div>
   <script src="../js/plugins/dataTables/datatables.min.js"></script>
   <script>
                             $(document).ready(function(){
                                 $('.dataTables-example').DataTable({
                                     pageLength: 10,
                                     responsive: true,
                                     dom: '<"html5buttons"B>lTfgitp',
                                     buttons: [
                                         /*{ extend: 'copy'},
                                         {extend: 'csv'},*/
                                         {extend: 'excel', title: 'Tipos de Operaciones'},/*
                                         {extend: 'pdf', title: 'ExampleFile'},*/
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
