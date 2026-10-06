<?php
include('../../conection/bdcredito.php');
extract($_POST);
if(isset($moti))
{
  $consul=extraer("select @id:=ta.idA as id,concat(ap,' ',am,' ',nom) as dato,tam.motivo,direc,cel,
(select sum(monto) from tahorro_deta where idA=@id and moti='$moti' and tipo='7') as saldo
from tahorro ta inner join tclie_general tc on ta.id=tc.idCG inner join tahorro_deta tad on ta.idA=tad.idA
inner join tahorro_motivo tam on tad.moti=tam.idam where tad.moti='$moti' group by ta.id");

  ?>
  <table class="table table-striped dataTables-example">
      <thead>
      <tr>
          <th>PARTICIPANTE</th>
          <th>MOTIVO</th>
          <th>DIRECCION</th>
          <th>CELULAR</th>
          <th>MONTO</th>
          <!--<th> <button type="submit" class="btn btn-default"  title='Descargar Lista'  onclick="imprimir_ahorro('reportes');">
              <span class="glyphicon glyphicon-print"></span> Imprimir
            </button> </th>
          -->
      </tr>
      </thead>
      <tbody>
        <?php
          while($row =mysqli_fetch_array($consul))
          {
        ?>
      <tr>
        <td><?php echo $row['dato']; ?></td>
        <td><?php echo $row['motivo'] ?></td>
        <td><?php echo $row['direc'] ?></td>
        <td><?php echo $row['cel'] ?></td>
        <td><?php echo 'S/. '.$row['saldo']; ?></td>
        <!--<td>

        </td>-->
      </tr>
        <?php
          }
        ?>
    </tbody>
  </table>
  <script>
      $(document).ready(function(){
        var fecha=$('#txtfecha').val();

          $('.dataTables-example').DataTable({
              pageLength: 15,
              responsive: true,
              dom: '<"html5buttons"B>lTfgitp',
              buttons: [
                  /*{ extend: 'copy'},
                  {extend: 'csv'},*/
                  {extend: 'excel', title: 'Depositos '},
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
  <?php
}


?>
