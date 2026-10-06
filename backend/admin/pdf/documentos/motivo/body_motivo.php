<table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
       <tr>
          <td style="width:50%;" class='midnight-blue'>AHORRO DE</td>
       </tr>
   <tr>
     <td style="width:50%;" >
       <?php
         $sql_cliente=mysqli_query($con,"select * from tclie_general where idCG='$id_cliente'");
         $rw_cliente=mysqli_fetch_array($sql_cliente);
         echo $rw_cliente['ap'];echo " ";echo $rw_cliente['am'];echo " ";echo $rw_cliente['nom'];
         echo "<br> Direccion	:";
         echo $rw_cliente['direc'];
         echo "<br> Teléfono		: ";
         echo $rw_cliente['cel'];
         echo "<br> Email 		  : ";
         echo $rw_cliente['correo'];
         echo "<br> Cuota de: ";
         echo "S/. 3.50";
       ?>

      </td>
   </tr>
</table>

   <table cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;" border="0.1px">
       <tr>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Nº Cuota</th>
           <th style="width: 15%;text-align:center" class='midnight-blue'>Fecha Prog.</th>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Firma</th>

           <th style="width: 2%"></th>

           <th style="width: 10%;text-align:center" class='midnight-blue'>Nº Cuota</th>
           <th style="width: 15%;text-align:center" class='midnight-blue'>Fecha Prog.</th>
           <th style="width: 10%;text-align:center" class='midnight-blue'>Firma</th>
       </tr>
          <?php
          $nums=1;
          $inicio="23/12/2019";
          $fecha=$inicio;
          $nums=1;
          $datos=array();
          $valor=1001;
          while ($nums <= $valor)
          {
            $com="1 days";
            $fe=preg_split("~/~",$fecha);
            $fecha="$fe[2]/$fe[1]/$fe[0]";
            $fecha=date("Y/m/d",strtotime($fecha."-".$com));
            $dia=date("w", strtotime($fecha));
            $fe1=preg_split("~/~",$fecha);

            $fecha="$fe1[2]/$fe1[1]/$fe1[0]";
            $nums++;
            if($dia=='0')
            {
              if($fe1[1]=='11')
              {
                $datos[$nums]=$fecha;
              }
              else
              {
                 $nums--;
              }
            }
            else
            {
              $datos[$nums]=$fecha;
            }
          }
          $veri=51;
          $cambio=101;
          $i=1;
          $r=51;
        while ($veri>1)
           {
             if ($veri%2==0)
             {
               $clase="clouds";
             } else
             {
               $clase="silver";
             }
             $fecha=$datos[$veri];
           ?>
            <tr>
              <td class='<?php echo $clase;?>' style="width: 12%; text-align: center"><?php echo $i;?></td>
              <td class='<?php echo $clase;?>' style="width: 20%; text-align: left"><?php echo $datos[$cambio];?></td>
              <td class='<?php echo $clase;?>' style="width: 15%; text-align: center"><?php ;?></td>

              <td style="width: 2%;border:none">&nbsp;</td>

              <td class='<?php echo $clase;?>' style="width: 12%; text-align: center"><?php echo $r;?></td>
              <td class='<?php echo $clase;?>' style="width: 20%; text-align: left"><?php echo $datos[$veri];?></td>
              <td class='<?php echo $clase;?>' style="width: 15%; text-align: center"></td>
            </tr>
           <?php
            $veri--;
            $i++;
            $r++;
            $cambio--;
            }
          ?>
 </table>
