<table class="pricing_table_wdg"   cellspacing="0" style="width: 100%; text-align: left; font-size: 6pt;">
            <thead >
              <tr>
                <th style="width:100%;text-align: center;">CREDITOS</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width:100%;">Nº Operacion:<strong> 000<?php echo $opera;//"$idPrestdet"; ?></strong><br>
                  cliente:<strong> <?php echo $cliente; ?></strong>
                </td>
              </tr>
              <tr style="text-align: center;">
                <td style="width:100%; text-align: center;">
                 <table class="pricing_table_wdg2" cellspacing="0" style="width: 100%; text-align: center; font-size: 6pt;">
                    <tr>
                      <th style="width: 30%"> </th>
                      <td style="text-align: left">SUB TOTAL:</td>
                      <td style="width: 10%"></td>
                      <td style="text-align: right"><?php echo $S." ".$sub; ?></td>
                      <td></td>
                    </tr>
                    <tr>
                      <th style="width: 30%"> </th>
                      <td style="text-align: left">MORA:</td>
                      <td style="width: 10%"></td>
                      <td style="text-align: right"><?php echo $S." ".$mora; ?></td>
                      <td></td>
                    </tr>
                    <tr>
                      <th><br></th>
                    </tr>
                    <tr>
                      <th style="width: 30%"></th>
                      <th style="text-align: left">TOTAL:</th>
                      <th style="width: 10%"></th>
                      <th style="text-align: right"><?php echo $S." ".$total;//"$montoP"; ?></th>
                      <th></th>
                    </tr>
                 </table>
                </td>
              </tr>
              <tr>
                 <?php date_default_timezone_set('america/lima');
            $datehora=date("Y-m-d H:i:s"); ?>
                <td style="width:100%;">Cuota Pag: <?php echo $cuo; ?><br>Cuota Pend: <?php echo $pendi;//"$pend"; ?><br>Cuota Venc: <?php echo $venci; ?><br>saldo: <?php echo $S." ".$totalF; ?><br>Usuario: <strong><?php echo "$datosU"; ?></strong><br>Fecha/Hora: <?php echo "$datehora"; ?></td>
              </tr>

            </tbody>
          </table>
