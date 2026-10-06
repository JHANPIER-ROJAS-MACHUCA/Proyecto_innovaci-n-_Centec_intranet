<div class="panel-heading">
    <div class="btn-group pull-right">
  </div>
<h5 style="color:white">BILLETAJE <small> Desarrollador Juvenal Perez Ramos </small></h5>
</div>
<?php include('../../../conection/db7.php'); ?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="panel-body">
    <table class="table table-striped dataTables-example">
        <thead>
        <tr style="background-color:<?php echo $jua1['color']?>">
            <th>FECHA</th>
            <th>Usuario</th>
            <th>Oficina</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody >
          <?php
          $tipoU=$_COOKIE['co_tipo'];

          $consulta="";
          if($tipoU=="1")
          {
            //gerente
                $consulta=extraer("SELECT idBille as id,total,fecha,tb.idO,tb.estado as esta,datos,tof.direccion as dire FROM tbilletaje tb inner join tusuario tu on tb.idU=tu.idU inner join toficina tof on tb.idO=tof.idO order by idBille desc");
          }
          else
          {
            //administrador
            $idO=$_COOKIE['co_ido'];
            $consulta=extraer("SELECT idBille as id,total,fecha,tb.idO,tb.estado as esta,datos,tof.direccion as dire FROM tbilletaje tb inner join tusuario tu on tb.idU=tu.idU inner join toficina tof on tb.idO=tof.idO where tb.idO='$idO'");
          }
            while($r=mysqli_fetch_array($consulta))
            {
              $fe=preg_split("~-~",$r['fecha']);
              $fecha=$fe[2]."/".$fe[1]."/".$fe[0];
          ?>
            <tr id="billa<?php echo $r['id']?>">
                <td><?php echo $fecha ?></td>
                <td><?php echo $r['datos'] ?></td>
                <td><?php echo $r['dire']?></td>
                <td><?php echo 'S/. '.$r['total'] ?></td>
                <td>
                <?php
                if($r['esta']==2)
                { ?>
                    <a id="btn1<?php echo $r['id']?>" onclick="confirmar(<?php echo $r['id']; ?>)" class="btn btn-success btn-xs">Confirmar</a>
                    <a id="btn2<?php echo $r['id']?>" style="display:none" onclick="elimini(<?php echo $r['id']; ?>)" class="btn btn-danger btn-xs">Eliminar</a>
                <?php
                }
                else
                {?>
                      <button type="button" onclick="elimini(<?php echo $r['id']; ?>)" class="btn btn-danger btn-xs">Eliminar</button>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
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
              {extend: 'excel', title: 'Billetaje'},
              {extend: 'pdf', title: 'Billetaje'},
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
<script type="text/javascript">
  function confirmar(codi)
  {
    $.post('contenido/caja/admin/billeUpdate.php',{codigo:codi} );
    ocultar(codi);
  }
  function elimini(codi)
  {
    $('#billa'+codi).toggle();
    $.post('contenido/caja/admin/billeDelete.php',{codigo:codi} );
  }
  function ocultar(codi)
  {
     $('#btn1'+codi).toggle();
     $('#btn2'+codi).toggle();
  }
</script>
