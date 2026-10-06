<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <?php
      include('conection/bdcredito.php');
      extract($_GET);
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
              <td style="font-size:8px;font-weight:bold" colspan="7"><?php echo $jua1['subnombre']; ?></td>
            </tr>
          </table>
          <?php //========================================================== ?>
          <?php
            $consulta=extraer("SELECT idCAD as id,montofin,total,comentario,concat(tu.apU,' ',tu.amU,' ',tu.nomU) as dato, tco.ini,tcud.tipo,tam.motivo as moti FROM tcaja_usu_detal tcud inner join tcaja_usuario tc on tcud.idCA=tc.idCA inner join tusuario tu on tc.idU=tu.idU inner join tcaja_oficina tco on tc.idCO=tco.idCO inner join tahorro_motivo tam on tam.idam=tcud.tipo where idCAD='$code'");
            $rac=mysqli_fetch_array($consulta);
           ?>
          <table  width="96%" style="border:none">
            <tr>
              <td style="text-align:center;font-size:9px;font-weight:bolt" colspan="9">
                <strong>
                    <?php
                      if($type=="1")
                      {
                        echo "RECIBO DE INGRESOS";
                      }
                      else if($type=="2")
                      {
                        echo "RECIBO DE EGRESOS";
                      }
                     ?>
                </strong>
              </td>
            </tr>
            <?php //datos de ciente ?>
            <tr >
              <td style="font-size:8px;text-align:left" width="22%"><strong>N° Operacion</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7">
                <strong>
                  <?php
                  $cant=strlen($code);
                  $completo=10;
                  $cant=$completo-$cant;
                  for ($i=0; $i <$cant ; $i++)
                  {
                    $code="0".$code;
                  }
                  echo $code;
                  ?>
                </strong></td>
            </tr>
            <tr >
              <td style="font-size:8px;text-align:left" width="20%"><strong>Tipo</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;font-weight:bold" colspan="7"><strong><?php echo $rac['moti']; ?></strong></td>
            </tr>

            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>MONTO</strong></td>
              <td style="font-size:10px;text-align:center;font-weight:bold" width="2%">:</td>
              <td style="font-size:10px;text-align:left;font-weight:bold" colspan="7">S/.<strong><?php echo $rac['total']; ?></strong></td>
            </tr>


            <?php //detalles ?>
            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>DESCRIPCION</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:10px;text-align:left;;font-weight:bold" colspan="7"><strong><?php echo $rac['comentario'];?></strong></td>
            </tr>

            <tr>
              <td style="font-size:8px;text-align:left" width="20%"><strong>Usuario</strong></td>
              <td style="font-size:8px;text-align:center;font-weight:bold" width="5%">:</td>
              <td style="font-size:8px;text-align:left;;font-weight:bold" colspan="7"><strong><?php echo $rac['dato'] ?></strong></td>
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
          <?php
          if(isset($_COOKIE['tofi']))
          {
            $ido=$_COOKIE['tofi'];
            $cas=extraer("SELECT direccion,telefono,correo FROM toficina where idO='$ido'");
            $ca1=mysqli_fetch_array($cas);
            $dir=$ca1['direccion'];
            $tel=$ca1['telefono'];
            $corre=$ca1['correo'];
          }
           ?>
            <tr>
              <td style="font-size:10px;text-align:center;font-weight:bold;" colspan="9">Dir.: <strong><?php echo $dir ?></strong>, Tel: <strong><?php echo $tel ?></strong>,Email: <strong><?php echo $corre ?></strong></td>
            </tr>
          </table>
        </div>
      </body>
    </html>
