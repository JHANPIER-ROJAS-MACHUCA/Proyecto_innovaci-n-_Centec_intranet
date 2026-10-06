<table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
       <tr>
          <td style="width:50%;" class='midnight-blue'>PRESTAMO DE</td>
       </tr>
   <tr>
          <td style="width:50%;" >
     <?php
       echo "<br> Direccion	:";
       echo "<br> Teléfono	:";
       echo "<br> Email 		:";
     ?>

      </td>
       </tr>
   </table>
      <br>
   <table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
       <tr>
          <td style="width:25%;text-align:center" class='midnight-blue'>FECHA DESEMBOLSO</td>
           <td style="width:25%;text-align:center" class='midnight-blue'>MONTO APROBADO</td>
     <td style="width:25%;text-align:center" class='midnight-blue'>TAZA</td>
      <td style="width:25%;text-align:center" class='midnight-blue'>TIPO</td>
       </tr>
   <tr>
         <td style="width:25%;text-align:center">
        <?php
        $fe=preg_split("~-~",$fechaD);
         echo "$fe[2] / $fe[1] / $fe[0]";
         ?>
       </td>
          <td style="width:25%;text-align:right"><?php echo $S. $montoA;?></td>
     <td style="width:25%;text-align: center"><?php echo $taza." %";?></td>
     <td style="width:25%; text-align:center">
       <?php
       switch ($tipo) {
         case 1:
             $ti="TRANSPORTE";
           break;
         case 2:
             $ti="COMERCIO";
           break;
         case 3:
             $ti="PRENDATARIO";
           break;
       }

        echo $ti;?></td>
     </tr>
   </table>
 <br>
   <table cellspacing="0" style="width: 100%; text-align: left; font-size: 7pt;" border="0.1px">
       <tr>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Nº Cuota</th>
           <th style="width: 16%;text-align:center" class='midnight-blue'>Fecha Prog.</th>
           <th style="width: 15%;text-align:center" class='midnight-blue'>Cuota</th>
           <th style="width: 10%;text-align:center;" class='midnight-blue'>Monto Pag.</th>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Pago Mora</th>
           <th style="width: 13%;text-align:center" class='midnight-blue'>Fecha de pago</th>
           <th style="width: 15%;text-align:center" class='midnight-blue'>Saldo</th>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Firma</th>
       </tr>
<?php

$monto=($montoA+($montoA*($taza/100)));
$com="";

if($tipoPa==1)
{
  $com="1 days";
}
else if($tipoPa==2)
{
  $com="1 week";
}
else if($tipoPa==3)
{
  $com="2 week";
}
else if($tipoPa==4)
{
  $com="1 month";
}

date_default_timezone_set('america/lima');
$fecha= $fechaD;


$dinero=$pagoini;
?>
<tr>
<td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php ?></td>
<td class='<?php echo $clase;?>' style="width: 16%; text-align: right"><?php ?></td>
<td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php ;?></td>
<td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php ?></td>
<td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php ?></td>
<td class='<?php echo $clase;?>' style="width: 13%; text-align: right"><?php ?></td>
<td class='<?php echo $clase;?>' style="width: 15%;text-align: right "><?php echo $S." ".$monto;?></td>

<td class='<?php echo $clase;?>' style="width: 10%; text-align: center"></td>
<!--<td class='<?php echo $clase;?>' style="width: 8%; text-align: center"></td>
<td class='<?php echo $clase;?>' style="width: 8%; text-align: center"></td>-->
</tr>
<?php
function feriados($veri)
{
  $fec=preg_split("~/~",$veri);
  $fechi="$fec[2]/$fec[1]";
  $año=$fec[0];
  $sema=date("Y/m/d", easter_date($año));

  $santa=date("d/m",strtotime($sema."-"."3 days"));
  $santa2=date("d/m",strtotime($sema."-"."2 days"));
  $resul=1;
  if($santa==$fechi){$resul=2;}
  else if($santa2==$fechi){$resul=2;}
  else if('01/01'==$fechi){$resul=2;}
  else if('01/05'==$fechi){$resul=2;}
  else if('29/06'==$fechi){$resul=2;}
  else if('28/07'==$fechi){$resul=2;}
  else if('29/07'==$fechi){$resul=2;}
  else if('30/08'==$fechi){$resul=2;}
  else if('08/10'==$fechi){$resul=2;}
  else if('01/11'==$fechi){$resul=2;}
  else if('08/12'==$fechi){$resul=2;}
  else if('25/12'==$fechi){$resul=2;}
  return $resul;
}

for ($i=1; $i <=$cantidad; $i++)
{
  $fecha=date("Y/m/d",strtotime($fecha."+".$com));
  $dia=date("w", strtotime($fecha));
  if($dia=='0')
  {
    $i--;
  }
  else if(feriados($fecha)==1)
  {
    $monto-=$dinero;
    if($i==$cantidad)
    {
      $monto=0  ;
    }
    ?>
    <tr>
      <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $i;?></td>
      <td class='<?php echo $clase;?>' style="width: 13%; text-align: right"><?php echo $fecha;?></td>
      <td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php echo $S." ".$dinero;?></td>
      <td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php ?></td>
      <td class='<?php echo $clase;?>' style="width: 10%; text-align: right"><?php ?></td>
      <td class='<?php echo $clase;?>' style="width: 13%; text-align: center"><?php ?></td>
      <td class='<?php echo $clase;?>' style="width: 10%;text-align: right "><?php echo $S." ".$monto;?></td>
      <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"></td>
    </tr>
    <?php
  }
  else
  {
    $i--;
  }
}
?>

</table>
