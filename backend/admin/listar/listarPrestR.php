
     <?php include('../conection/bdcredito.php');  ?>

<?php
//extract($_POST);

if($_COOKIE['tuser']=='7' || $_COOKIE['tuser']=='1' || $_COOKIE['tuser']=='5')
{
$consul=extraer("select * from tprestamo t,tclie_general c,tusuario u where t.idCG=c.idCG and c.idU=u.idU  order by idP desc");//and t.estado!='5'
}
else
{
$idO=$_COOKIE['tofi'];
$consul=extraer("select * from tprestamo t,tclie_general c,tusuario u where t.idCG=c.idCG and c.idU=u.idU and c.idO='$idO' and t.estado!='5' order by idP desc");
}
//juve
?>

<table class="table table-striped" id="listaTodosLosPrestamos" style="width:100%;" >
<thead>
<tr style="background-color:<?php echo $jua1['color']?>">
    <th style="display:none"></th>
    <th>DNI</th>
    <th>Cliente</th>
    <th>Monto propuesto</th>
    <th>Monto Aprobado</th>
    <th>Taza</th>
    <th>T. Pago</th>
    <th>Periodo</th>
    <th>Tipo</th>
    <th>F. Desembolso</th>
    <th>Usuario</th>
    <th>Estado</th>
    <?php
    if($_COOKIE['tuser']=='1' || $_COOKIE['tuser']=='2' || $_COOKIE['tuser']=='7' )
    {
     ?>
    <th>Pagara</th>
    <?php
    }
     ?>
    <th></th>
</tr>
</thead>
<tbody >
  <?php
    $cone=1;
    function fecha($valor)
    {
      $resul="";
      if(!empty($valor))
      {
        $r=preg_split("~-~",$valor);
        $resul=$r[2]."/".$r[1]."/".$r[0];
      }
      return $resul;
    }
    while($row =mysqli_fetch_array($consul))
    {
      $movimiento="";
      if($row['estado']=='1')
      {
        $movimiento="Propuesto";
      }
      else if($row['estado']=='2')
      {
        $movimiento="Aprobado";
      }
      else if($row['estado']=='3')
      {
        $movimiento="Desaprobado";
      }
      else if($row['estado']=='4')
      {
        $movimiento="Desembolsado";
      }
      else if($row['estado']=='5')
      {
        $movimiento="Cancelado";
      }
      else if($row['estado']=='6')
      {
        $movimiento="Anulado";
      }

       $movimiento2="";
      if($row['tipoP']=='1')
      {
        $movimiento2="Transporte";
      }
      else if($row['tipoP']=='2')
      {
        $movimiento2="Comercio";
      }
      else if($row['tipoP']=='3')
      {
        $movimiento2="Prendatario";
      }
      else if($row['tipoP']=='4')
      {
        $movimiento2="Servicio";
      }
      $tpago="";
      switch ($row['pago']) {
        case 1:
          $tpago="DIARIO";
        break;
        case 2:
          $tpago="SEMANAL";
        break;
        case 3:
          $tpago="PAGO UNICO";
        break;
        case 4:
          $tpago="MENSUAL";
        break;
        case 5:
          $tpago="QUINCENAL";
        break;
      }
      $periodo=$row['plazo'];
  ?>
<tr>
  <td style="display:none"><?php echo $cone; ?></td>
  <td><?php echo $row['dni']; ?></td>
  <td><?php echo $row['ap']." ".$row['am']." ".$row['nom']; ?></td>
  <td><?php echo 'S/. '.$row['montoPropuesto']; ?></td>
  <td><?php echo 'S/. '.$row['montoAprovado']; ?></td>
  <td><?php echo $row['taza']." %"; ?></td>
  <td><?php echo $tpago; ?></td>
  <td><?php echo $periodo;?></td>
  <td><?php echo $movimiento2; ?></td>
  <td style="color:red;font-weight:bold"><?php echo fecha($row['fechaDesembolso']) ?></td>
  <td><?php echo $row['dniU']." ".$row['nomU']; ?></td>
  <td><?php echo $movimiento; ?> </td>
  <?php

  if($_COOKIE['tuser']=='1' || $_COOKIE['tuser']=='2'|| $_COOKIE['tuser']=='7')
  {
   ?>
  <td>
    <?php
        $cuota=$row['cuota'];
        $fechaDesen=$row['fechaDesembolso'];
        $fechate=$row['fechaTermino'];
        $title="Pagara en Oficina";
        $icone="fa fa-university";
        $colorear="red";
        if($row['tlocal']=="1")
        {
          $title="Pagara en Campo";
          $icone="fa fa-pied-piper-alt";
          $colorear="green";
        }
        ?>
        <input type="hidden" id="txtconte<?php echo $row['idP'];?>" name="" value="<?php echo $row['tlocal'] ?>">
        <a id="btncampo<?php echo $row['idP'];?>" class="btn btn-sm btn-default" title="<?php echo $title ?>" onclick="campero('<?php echo $row['idP'];?>')"><i id="btncampo2<?php echo $row['idP'];?>" class="<?php echo $icone; ?>" style="color:<?php echo $colorear; ?>"></i></a>
        <a href="#" onclick="cambiasoSo('<?php echo $row['idP'];?>','<?php echo $row['taza']; ?>','<?php echo $row['pago']; ?>','<?php echo $periodo; ?>','<?php echo $fechaDesen; ?>','<?php echo $fechate; ?>','<?php echo $row['montoAprovado']; ?>','<?php echo $cuota ?>')"  data-target="#cambiosPrestamos" class="btn btn-sm btn.default" data-toggle="modal" title="Cambios"><i class="fa fa-cogs" data-toggle="tooltip" ></i></a>

        <?php

      if($_COOKIE['tuser']=='1')
      {
        ?>
        <a href="#" class='btn btn-default' title='Descargar Prestamo' onclick="imprimir_prestamo('<?php echo $row['idP'];?>');"><i class="glyphicon glyphicon-download"></i></a>
        <?php
      }
     ?>
  </td>
<?php } ?>
  <td>
    <?php
      if($row['estado']=='1' && ($_COOKIE['tuser']=="7" || $_COOKIE['tuser']=="1" || $_COOKIE['tuser']=="2"))
      {
        $monto=$row['montoPropuesto'];
        $masco=0;
        if($monto>=1500)
        {
          if($_COOKIE['tuser']=='1')
          {
     ?>
            <a href="#"  data-target="#confirmarPrestamo" class="edit" data-toggle="modal" data-id='<?php echo $row['idP'];?>' data-montop='<?php echo $row['montoPropuesto'];?>' data-montoa='<?php echo $row['montoAprobado'];?>' data-taza='<?php echo $row['taza'];?>'><i class="fa fa-handshake-o" data-toggle="tooltip" title="Confirmar" ></i></a>
    <?php
          }
        }
        else
        {
          ?>
            <a href="#"  data-target="#confirmarPrestamo" class="edit" data-toggle="modal" data-id='<?php echo $row['idP'];?>' data-montop='<?php echo $row['montoPropuesto'];?>' data-montoa='<?php echo $row['montoAprobado'];?>' data-taza='<?php echo $row['taza'];?>'><i class="fa fa-handshake-o" data-toggle="tooltip" title="Confirmar" ></i></a>
          <?php
        }
      }
      if($row['estado']=='2')
      {
        if($limitadorfinal==0)
        {
        ?>
        <a class='btn btn-sm btn-default' title='Desembolsar' onclick="desembolsar('<?php echo $row['idP'];?>','<?php echo $row['idCG'] ?>');"><i class="fa fa-legal" style="color:red"></i></a>
       <?php
     }}
      if( $row['estado']=='4')
      {

     ?>

    <a href="#" class='btn btn-default' title='Descargar Prestamo' onclick="imprimir_prestamo('<?php echo $row['idP'];?>');"><i class="glyphicon glyphicon-download"></i></a>
    <a href="#" class='btn btn-default' title='Descargar Historial' onclick="imprimir_historial('<?php echo $row['idP'];?>');"><i class="fa fa-download"></i></a>
    <a class='btn btn-sm btn-default' title='Pagare' onclick="imprimir_pagare('<?php echo $row['idP'];?>')"><i class="fa fa-cc-visa" style="color:black"></i></a>
    <a class='btn btn-sm btn-default' title='Contrato' onclick="imprimir_contrato('<?php echo $row['idP'];?>')"><i class="fa fa-file-pdf-o" style="color:black"></i></a>

    <?php
    }

    if(($row['estado'] !="2" && $row['estado']!="3" && $_COOKIE['tuser']!='3') || $_COOKIE['tuser']=="7" || $_COOKIE['tuser']=="1" || $_COOKIE['tuser']=="2")
    {
      if($limitadorfinal==0)
      {
    ?>

    <a class='btn btn-sm btn-default' title='Denegar o Anular' onclick="denegar('<?php echo $row['idP'];?>')"><i class="glyphicon glyphicon-trash" style="color:red"></i></a>
    <?php
  }}
     ?>
  </td>
</tr>
  <?php
  $cone++;
    }
  ?>
</tbody>
</table>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script>
$(document).ready(function(){
  var fecha=$('#txtfecha').val();

    $('#listaTodosLosPrestamos').DataTable({
        pageLength: 10,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            /*{ extend: 'copy'},
            {extend: 'csv'},*/
            // {extend: 'excel', title: 'Cobros '+fecha},
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
?>
