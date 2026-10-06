<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Models\Credit;

$credit = Credit::with('installments')->find($_POST['id']);

if (!$credit) {
  echo "";
  die();
}

// array de resumen de deuda
$installmentSummary = $credit->installmentSummary();

// resumen de otras moras y dias de atraso
$creditSummary = $credit->creditSummary();
?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>
        FECHA
      </th>
      <th>
        MONTO
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody style="font-size:10px">
    <?php
    foreach ($credit->installments as $installment) {
      $id = (string) $installment->idPD;
      $protoKey = array_search($id, array_column($installmentSummary, 'id'));
      $proto = $installmentSummary[$protoKey];

      // si la cuota no tiene mora
      if ($proto->deuda_mora <= 0) {
        continue;
      }
    ?>
      <tr id="tr-<?php echo $installment->idPD ?>">
        <td style="text-align:center"><?php echo $installment->fechaProg; ?></td>
        <td><?php echo "S/. " . $proto->deuda_mora ?></td>
        <td><button onclick="handleClick(<?php echo $installment->idPD ?>, <?php echo $proto->deuda_mora ?>)">Condonar</button></td>
      </tr>
    <?php } ?>
    <?php if ($creditSummary->deuda_otras_moras > 0) { ?>
      <tr id="trOtrasMoras">
        <td style="text-align: center;"><?php echo $creditSummary->dias_atrasados ?> dias.</td>
        <td><span id="textDeudaTotalMoras">S/. <?php echo $creditSummary->deuda_otras_moras ?></span></td>
        <td>
          <input type="hidden" value="<?php echo $creditSummary->deuda_otras_moras ?>" id="deudaTotalMoras" />
          <input type="number" id="otrasMoras">
          <button onclick="condonarOtrasMoras(<?php echo $credit->idP ?>)">Condonar</button>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<script>
  function handleClick(installmentId, amount) {
    fetch(`../app/api/condonarMora.php?installmentId=${installmentId}&amount=${amount}`)
      .then(response => response.json())
      .then(data => {
        if (data.success === true) {
          document.getElementById('tr-' + installmentId).remove();
        }
      });
  }

  function condonarOtrasMoras(installmentId) {
    const input = document.getElementById('otrasMoras');
    var amount = parseFloat(input.value);

    const a = document.getElementById('deudaTotalMoras');
    const newValue = parseFloat(a.value - amount);

    if (!amount) {
      alert('El monto a condonar debe ser mayor a S/. ' + 0);
      return;
    }

    if (amount > a.value) {
      alert('El monto a condonar debe ser menor o igual a S/. ' + a.value);
      return;
    }

    fetch(`../app/api/condonarOtrasMoras.php?installmentId=${installmentId}&amount=${amount}`)
      .then(response => response.json())
      .then(data => {
        if (data.success === true) {
          if (newValue <= 0) {
            document.getElementById('trOtrasMoras').remove();
            return;
          }

          a.value = newValue;
          document.getElementById('textDeudaTotalMoras').innerHTML = "S/. " + newValue;
        }
      });
  }
</script>