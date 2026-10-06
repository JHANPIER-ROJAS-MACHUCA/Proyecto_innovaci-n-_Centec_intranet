<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <?php
    extract($_GET);
    include('conection/bdcredito.php');

      $jua=extraer("SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico FROM tdatos limit 1");
      $jua1=mysqli_fetch_array($jua);
      $comentaJuve=$jua1['comentario'];

     ?>
  </head>
  <body onload="window.print()">
        <div class="ticket">
            <?php //HEAD ?>
          <table >
            <tr>
              <td rowspan="2" colspan="2">
                <img src="titulo/img/<?php echo $jua1['logo']; ?>" alt="Logotipo" height="50px">
              </td>
              <td style="font-size:12px;font-weight:bold" colspan="7"><?php echo $jua1['nombreEmpresa']; ?></td>
            </tr>
            <tr>
              <td style="font-size:8px;font-weight:bold" colspan="7"><?php echo $jua1['subnombre'] ?></td>
            </tr>
          </table>
          <?php //========================================================== ?>
          <table  width="96%" style="border:none">
            <tr>
              <td style="text-align:center;font-size:9px;font-weight:bolt" colspan="9"><strong>BOUCHER</strong></td>
            </tr>
            <?php //datos de ciente ?>
            <tr >
              <td style="font-size:8px;text-align:left" width="22%"><strong>N° Operacion</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7">
                <strong>
                  <?php
                  $cant=strlen($opera);
                  $completo=10;
                  $cant=$completo-$cant;
                  for ($i=0; $i <$cant ; $i++)
                  {
                    $opera="0".$opera;
                  }
                  echo $opera;
                  ?>
                </strong></td>
            </tr>
            <tr >
              <td style="font-size:8px;text-align:left" width="20%"><strong>N° de Credito</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;font-weight:bold" colspan="7"><strong><?php echo $pres ?></strong></td>
            </tr>

            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Cliente</strong></td>
              <td style="font-size:10px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;font-weight:bold" colspan="7"><strong><?php echo $clie ?></strong></td>
            </tr>

            <?php //deuda ?>
            <tr>
              <td colspan="4" style="font-size:10px;text-align:right" width="46%"><strong>Sub Total</strong></td>
              <td width="2px" style="font-size:6px;text-align:center;">: S/.</td>
              <td colspan="4" style="font-size:10px;text-align:left;font-weight:bold" width="46%"><?php echo $sub ?></td>
            </tr>
            <tr>
              <td colspan="4" style="font-size:10px;text-align:right" width="46%"><strong>Mora</strong></td>
              <td width="2px" style="font-size:6px;text-align:center;">: S/.</td>
              <td colspan="4" style="font-size:10px;text-align:left;font-weight:bold" width="46%"><?php echo $mora ?></td>
            </tr>
            <?php //total ?>
            <tr>
              <td colspan="4" style="font-size:10px;text-align:right;font-weight:bold" width="46%">Total</td>
              <td width="2px" style="font-size:6px;text-align:center;font-weight:bold">: S/.</td>
              <td colspan="4" style="font-size:12px;text-align:left;font-weight:bold" width="46%"><?php echo $total ?></td>
            </tr>
            <?php //detalles ?>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Cuota Pag.</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7"><strong><?php if(!empty($cumon)){echo $cumon;}else{echo "--";}?></strong></td>
            </tr>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Cuota Pend.</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7"><strong><?php if(!empty($pendi)){echo $pendi;}else{echo "--";} ?></strong></td>
            </tr>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Cuota Venc.</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7"><strong><?php echo $vencid ?></strong></td>
            </tr>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Saldo</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7">S/.<strong><?php echo $totalF ?></strong></td>
            </tr>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Usuario</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:8px;text-align:left;;font-weight:bold" colspan="7"><strong><?php echo $usu ?></strong></td>
            </tr>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Fecha/Hora</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7">
                <strong> <?php date_default_timezone_set('america/lima');
                $datehora=date("d/m/Y H:i:s"); ?>
                <?php echo $datehora ?>
              </strong></td>
            </tr>
          <?php //FOOTER ?>
            <tr>
              <td style="font-size:10px;text-align:center;font-weight:bold;" colspan="9">Dir.: <strong><?php echo $dir ?></strong>, Tel: <strong><?php echo $tel ?></strong>,Email: <strong><?php echo $corre ?></strong></td>
            </tr>
            <tr>
              <td style="font-size:10px;text-align:center;font-weight:bold;" colspan="9"><strong>SU PUNTUALIDAD ES SU MEJOR GARANTIA PARA SU PROXIMO CREDITO</stong></td>
            </tr>
          </table>
        </div>
      </body>
    </html>
