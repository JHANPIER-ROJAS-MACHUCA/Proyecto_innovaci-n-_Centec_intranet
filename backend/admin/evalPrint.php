<?php include('conection/bdcredito.php'); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Evaluación</title>
        <script src="jquery/jquery.min.js"></script>
    <style type="text/css">
      .centraJ{
        text-align:center;
      }
      .centraJ1{
        text-align:right;
      }
      body{
        font-size: 10px;
      }
      .conbor{
        border: 1px solid;
      }
      .debajo{
        border-bottom: 1px solid;
      }
      .bor1{
        border:1px solid;
      }
      .bor2{
        border-bottom: 1px solid;
      }
    </style>
    <?php
    extract($_GET);
     ?>
  </head>
  <body onload="hora()">
    <?php//tabla ?>
    <table width="100%">
      <tr>
        <!--logo-->
        <td>
          <table cellspacing="0" style="width: 100%;" >
             <tr>
                 <td style="width: 20%; ">
                   <img style="width:40px;height:40px" src="titulo/img/<?php echo $jua1['logo'] ?>"><br>
                 </td>
                 <td style="width: 80%; color: #34495e;font-size:8px;text-align:left;font-family: Arial">
                     <span style="color: #34495e;font-size:10px;font-weight:bold">
                       CENTRO DE TECNOLOGIA
                     </span>
                     <br>
                     <span style="color: #34495e;font-size:10px;font-weight:bold">
                       Y CREDITOS DEL PERU
                     </span>
                 </td>
             </tr>
         </table>
        </td>
        <!--titulo-->
        <td align="center" colspan="2">
          <label>HOJA DE EVALUACIÓN DE CREDIDIARIO</label>
        </td>
        <td>
          &nbsp;
        </td>
        <td>
          <label>Hora Eval: &nbsp; <br><span id="hora" style="font-size:12px"></span> Hrs.</label>
        </td>
      </tr>

      <tr>
        <td colspan="5" height="5px">&nbsp;
        </td>
      </tr>

      <tr>
        <td>
          <label style="font-size:15px"><strong>I.- ACTIVO A CORTO PLAZO</strong></label>
        </td>
        <td class="centraJ"><label>1ra Evaluación</label></td>
        <td class="centraJ"><label>2da Evaluación</label></td>
        <td class="centraJ">&nbsp;</td>
        <td class="centraJ"><label>3ra Evaluación</label></td>
      </tr>
      <!--1-->
      <tr>
        <td>
          <label>Fecha</label>
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d1; ?></label>
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d2; ?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d3; ?></label>
        </td>
      </tr>
      <!--2-->
      <tr>
        <td>
          <label>Disponible</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d4; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d5; ?></label>
        </td>
        <td class="centraJ1">
          &nbsp;
        </td>
        <td class="centraJ bor2">
          <label><?php echo $d6; ?></label>
        </td>
      </tr>
      <!--3-->
      <tr>
        <td>
          <label>Cuentas por Cobrar</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d7; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d8; ?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d9;?></label>
        </td>
      </tr>
      <!--4-->
      <tr>
        <td>
          <label>Inventario</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d10; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d11; ?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d12; ?></label>
        </td>
      </tr>
      <!--5-->
      <tr>
        <td>
          <label>TOTAL ACT. C. PLAZO</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d13;?></label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d14;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d15;?></label>
        </td>
      </tr>
      <!--6-->
      <tr>
        <td>
          <label>Inv. Descripción</label>
        </td>
        <td class="centraJ">
          <label>Monto</label>
        </td>
        <td class="centraJ">
          <label>Monto</label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ">
          <label>Monto</label>
        </td>
      </tr>
      <!--7-->
      <tr>
        <td class="bor2">
          <label><?php echo $d16; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d17; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d18 ?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d19; ?></label>
        </td>
      </tr>
      <!--8-->
      <tr>
        <td class="bor2">
          <label><?php echo $d20; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d21; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d22; ?></label>
        </td>
        <td class="centraJ1">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d23; ?></label>
        </td>
      </tr>
      <!--9-->
      <tr>
        <td class="bor2">
          <label><?php echo $d24; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d25; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d26; ?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d27; ?></label>
        </td>
      </tr>
      <!--10-->
      <tr>
        <td class="bor2">
          <label><?php echo $d28; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d29; ?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d30; ?></label>
        </td>
        <td class="centraJ">
        &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d31; ?></label>
        </td>
      </tr>
      <!--11-->
      <tr>
        <td>
          <label>Total Inventario</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d32; ?></label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d33; ?></label>
        </td>
        <td class="centraJ">
        &nbsp;
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d34; ?></label>
        </td>
      </tr>
      <!--ventas-->
      <tr>
        <td colspan="5">
        <label style="font-size:15px"><strong>II.- VENTAS</strong></label>
        </td>
      </tr>

      <tr>
        <td>
          <label>Ventas Diarias Estimadas</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d35;?></label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d36;?></label>
        </td>
        <td class="centraJ">
        &nbsp;
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d37;?></label>
        </td>
      </tr>

      <tr>
        <td colspan="5">
          <label>Ventas Declaradas</label>
        </td>

      </tr>

      <tr>
        <td style="text-align:right">
          <label>Bueno</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d38;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d39;?></label>
        </td>
        <td class="centraJ">
        &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d40;?></label>
        </td>
      </tr>

      <tr>
        <td style="text-align:right">
          <label>Malo</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d41;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d42;?></label>
        </td>
        <td class="centraJ">
        &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d43;?></label>
        </td>
      </tr>

      <tr>
        <td style="text-align:right">
          <label>Estimación </label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d44;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d45;?></label>
        </td>
        <td class="centraJ1">
        &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d46;?></label>
        </td>
      </tr>
      <!--costo mercado / producto -->

      <tr>
        <td colspan="5">
        <label style="font-size:15px"><strong>III.- COSTO MERC/PROD</strong></label>
        </td>
      </tr>
      <!--primera eva-->
      <!--1-->
      <tr>
        <td>
          <label>1ra Evaluación</label>
        </td>
        <td class="centraJ">
          <label>Precio de Compra</label>
        </td>
        <td class="centraJ">
          <label>Precio de Venta</label>
        </td>
        <td class="centraJ">
          <label>C.R.</label>
        </td>
        <td class="centraJ">
          <label>Promedio</label>
        </td>
      </tr>
      <!--2-->
      <tr>
        <td class="bor2">
          <label><?php echo $d47;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d48;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d49;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d50;?></label>
        </td>
        <td class="centraJ bor1" style="vertical-align:middle" rowspan="4">
          <label><?php echo $d51;?></label>
        </td>
      </tr>
      <!--3-->
      <tr>
        <td class="bor2">
          <label><?php echo $d52;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d53;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d54;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d55;?></label>
        </td>
      </tr>
      <!--4-->
      <tr>
        <td class="bor2">
          <label><?php echo $d56;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d57;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d58;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d59;?></label>
        </td>
      </tr>
      <!--5-->
      <tr>
        <td class="bor2">
          <label><?php echo $d60;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d61;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d62;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d63;?></label>
        </td>
      </tr>
      <!--segunda eva-->
      <!--1-->
      <tr>
        <td>
          <label>2da Evaluación</label>
        </td>
        <td class="centraJ">
          <label>Precio de Compra</label>
        </td>
        <td class="centraJ">
          <label>Precio de Venta</label>
        </td>
        <td class="centraJ">
          <label>C.R.</label>
        </td>
        <td class="centraJ">
          <label>Promedio</label>
        </td>
      </tr>
      <!--2-->
      <tr>
        <td class="bor2">
          <label><?php echo $d64;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d65;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d66;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d67;?></label>
        </td>
        <td class="centraJ bor1" style="vertical-align:middle" rowspan="4">
          <label><?php echo $d68;?></label>
        </td>
      </tr>
      <!--3-->
      <tr>
        <td class="bor2">
          <label><?php echo $d69;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d70;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d71;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d72;?></label>
        </td>
      </tr>
      <!--4-->
      <tr>
        <td class="bor2">
          <label><?php echo $d73;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d74;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d75;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d76;?></label>
        </td>
      </tr>
      <!--5-->
      <tr>
        <td class="bor2">
          <label><?php echo $d77;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d78;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d79;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d80;?></label>
        </td>
      </tr>
      <!--tercera eva-->
      <!--1-->
      <tr>
        <td>
          <label>3ra Evaluación</label>
        </td>
        <td class="centraJ">
          <label>Precio de Compra</label>
        </td>
        <td class="centraJ">
          <label>Precio de Venta</label>
        </td>
        <td class="centraJ">
          <label>C.R.</label>
        </td>
        <td class="centraJ">
          <label>Promedio</label>
        </td>
      </tr>
      <!--2-->
      <tr>
        <td class="bor2">
          <label><?php echo $d81;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d82;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d83;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d84;?></label>
        </td>
        <td class="centraJ bor1" style="vertical-align:middle" rowspan="4">
          <label><?php echo $d85;?></label>
        </td>
      </tr>
      <!--3-->
      <tr>
        <td class="bor2">
          <label><?php echo $d86;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d87;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d88;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d89;?></label>
        </td>
      </tr>
      <!--4-->
      <tr>
        <td class="bor2">
          <label><?php echo $d90;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d91;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d92;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d93;?></label>
        </td>
      </tr>
      <!--5-->
      <tr>
        <td class="bor2">
          <label><?php echo $d94;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d95;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d96;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d97;?></label>
        </td>
      </tr>
      <!--/////OTROS INGRESOS-->
      <tr>
        <td colspan="5">
        <label style="font-size:15px"><strong>IV.- OTROS INGRESOS</strong></label>
        </td>
      </tr>

      <tr>
        <td>
          <label>1ra Evaluación</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d98;?></label>
        </td>
        <td colspan="3" class="bor2">
          <label><?php echo $d99;?></label>
        </td>
      </tr>

      <tr>
        <td>
          <label>2da Evaluación</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d100;?></label>
        </td>
        <td colspan="3" class="bor2">
          <label><?php echo $d101;?></label>
        </td>
      </tr>

      <tr>
        <td>
          <label>3ra Evaluación</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d102;?></label>
        </td>
        <td colspan="3" class="bor2">
          <label><?php echo $d103;?></label>
        </td>
      </tr>


      <!--resumen-->
      <tr>
        <td colspan="5">
          <hr style="border: 0 ; border-top: 4px double #2c3e50; width: 90%;">
        </td>
      </tr>
      <tr>
        <td colspan="5" class="centraJ">
          <label style="font-size:15px"><strong>RESUMEN ECONOMICO DE LA ACTIVIDAD</strong></label>
        </td>
      </tr>
      <!--1-->
      <tr>
        <td>
          <label>Fecha</label>
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d104;?></label>
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d105;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ bor1">
          <label><?php echo $d106;?></label>
        </td>
      </tr>
      <!--2-->
      <tr>
        <td>
          <label>VENTA ESTIMADA</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d107;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d108;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d109;?></label>
        </td>
      </tr>
      <!--3-->
      <tr>
        <td>
          <label>OTROS INGRESOS</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d110;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d111;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d112;?></label>
        </td>
      </tr>
      <!--4-->
      <tr>
        <td>
          <label>TOTAL DE INGRESOS</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d113;?></label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d114;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d115;?></label>
        </td>
      </tr>
      <!--5-->
      <tr>
        <td>
          <label>COSTO MERC/PROD</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d116;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d117;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d118;?></label>
        </td>
      </tr>
      <!--6-->
      <tr>
        <td>
          <label>UTILIDAD BRUTA</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d119;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d120;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d121;?></label>
        </td>
      </tr>
      <!--7-->
      <tr>
        <td>
          <label>% COBERTURA - OTROS COSTOS</label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d122;?></label>
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d123;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor2">
          <label><?php echo $d124;?></label>
        </td>
      </tr>
      <!--8-->
      <tr>
        <td>
          <label>EXCEDENTE DEL CLIENTE</label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d125;?></label>
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d126;?></label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ1 bor1">
          <label><?php echo $d127;?></label>
        </td>
      </tr>
      <tr>
        <td colspan="5" height="40px">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td>
          <label>Firma del Evaluador</label>
        </td>
        <td class="centraJ">
          <label>_______________________</label>
        </td>
        <td class="centraJ">
          <label>_______________________</label>
        </td>
        <td class="centraJ">
          &nbsp;
        </td>
        <td class="centraJ">
          <label>_______________________</label>
        </td>
      </tr>

    </table>
  </body>
  <script type="text/javascript">

    window.onload = function() {
        window.print();
      }
    </script>
</script>
</html>
