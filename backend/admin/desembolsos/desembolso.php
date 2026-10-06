<?php
include '../../vendor/autoload.php';
include '../../src/Domain/Database/bootstrap.php';
include '../../app/models/Credit.php';
include '../../app/models/Transaction.php';

$fechaDe = $_POST['fechaDe'];
$fechaHasta = $_POST['fechaHasta'];
$userId = $_POST['userId'];

$desembolsos = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
  ->join('tcaja_usu_detal', 'tprestamo.idP', 'tcaja_usu_detal.conejo')
  ->join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->join('tusuario', 'tcaja_usuario.idU', 'tusuario.idU')
  ->where(function ($query) use ($fechaDe, $fechaHasta) {
    if ($fechaDe && $fechaHasta) {
      $query->whereBetween('tprestamo.fechaDesembolso', ['2022-06-01', '2022-06-30']);
    }
  })
  ->where(function ($query) use ($userId) {
    if ($userId) {
      $query->where('tusuario.idU', $userId);
    }
  })
  ->orderBy('tprestamo.fechaDesembolso', 'desc')
  ->get();

$total = 0;
$num = count($desembolsos);
?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="table-responsive">
  <table class="table table-striped dataTables-example">
    <thead>
      <tr style="color:#31708f">
        <th>Número</th>
        <th>Cliente</th>
        <th>Monto</th>
        <th>Fecha</th>
        <th>Usuario </th>
        <th>Estado </th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($desembolsos as $key => $desembolso) {
        if ($desembolso->estadodt === '2') {
          $total += $desembolso->total;
        }
      ?>
        <tr>
          <td><?php echo $key + 1 ?></td>
          <td><?php echo $desembolso->ap . ' ' . $desembolso->am . ' ' . $desembolso->nom ?></td>
          <td><?php echo $desembolso->total ?></td>
          <td><?php echo $desembolso->created_at ?></td>
          <td><?php echo $desembolso->apU . ' ' . $desembolso->amU . ' ' . $desembolso->nomU ?></td>
          <td style="color: <?php echo $desembolso->estadodt === '2' ? 'black' : 'red' ?>"><?php echo $desembolso->estadodt === '2' ? 'DESEMBOLSADO' : 'ANULADO' ?></td>
        </tr>
      <?php
      }
      ?>

    </tbody>
    <tfoot>
    </tfoot>
  </table>
</div>

<div>
  <div>TOTAL</div>
  <input class="form-control" type="text" value="<?php echo $total ?>">
</div>
<div>
  <div>NUMER</div>
  <input class="form-control" type="text" value="<?php echo $num ?>">
</div>

<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    var fecha = $('#txtfecha').val();

    $('.dataTables-example').DataTable({
      pageLength: 15,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        /*{ extend: 'copy'},
        {extend: 'csv'},*/
        {
          extend: 'excel',
          title: 'Desembolsos '
        },
        /*{extend: 'pdf', title: 'ExampleFile'},*/
        {
          extend: 'print',
          customize: function(win) {
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