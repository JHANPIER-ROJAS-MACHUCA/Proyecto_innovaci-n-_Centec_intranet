<?php

include('head.php');

use CrediSoporte\Domain\Models\Credit;


$page = is_numeric($request->page) ? $request->page : 1;
$buscar = $request->buscar;
$estado = $request->estado;
$fechaDe = $request->fechaDesemboldoDe;
$fechaHasta = $request->fechaDesemboldoHasta;

$limit = 25;
$offset = ($page * $limit) - $limit;

$queryCredit = Credit::query();
$queryCredit->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
  ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
  ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
  ->orderBy('tprestamo.idP', 'desc')
  ->when($buscar, function ($query) use ($buscar) {
    $query->where(function ($query) use ($buscar) {
      $query->whereRaw("CONCAT_WS(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) LIKE '%$buscar%'")
        ->orWhere('tclie_general.dni', 'like', $buscar . '%');
    });
  })
  ->when($fechaDe && $fechaHasta, function ($query) use ($fechaDe, $fechaHasta) {
    $query->whereBetween('fechaDesembolso', [$fechaDe, $fechaHasta]);
  })
  ->when($estado, function ($query) use ($estado) {
    $query->where('estado', $estado);
  })
  ->orderBy('tprestamo.idP', 'desc');

if ($_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '2' && $_COOKIE['tuser'] != '5') {
  // $idO = $_COOKIE['tofi'];
  $queryCredit
    // ->where('tclie_general.idO', $idO)
    ->where('tprestamo.estado', '!=', '5');
}

$total = $queryCredit->count();

$credits = $queryCredit
  ->offset($offset)
  ->limit($limit)
  ->get();

$numberPages = ceil($total / $limit);

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
    </div>
    <h5 style="color:white">Reporte de Prestamos <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div>
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" id="formFiltros">
        <div style="display: flex; align-items: end;">
          <div style="margin-right: 20px;">
            <label for="">Inicio Fecha de desembolso</label>
            <input class="form-control" type="date" name="fechaDesemboldoDe" value="<?php echo $fechaDe ?>">
          </div>
          <div style="margin-right: 20px;">
            <label for="">Fin Fecha de desembolso</label>
            <input class="form-control" type="date" name="fechaDesemboldoHasta" value="<?php echo $fechaHasta ?>">
          </div>
          <div style="margin-right: 20px;">
            <label for="">Estado</label>
            <select class="form-control" name="estado">
              <option value="">Todos</option>
              <option value="1" <?php echo $estado == '1' ? 'selected' : '' ?>>Propuesto</option>
              <option value="2" <?php echo $estado == '2' ? 'selected' : '' ?>>Aprobado</option>
              <option value="3" <?php echo $estado == '3' ? 'selected' : '' ?>>Desaprobado</option>
              <option value="4" <?php echo $estado == '4' ? 'selected' : '' ?>>Desembolsado</option>
              <option value="5" <?php echo $estado == '5' ? 'selected' : '' ?>>Cancelado</option>
              <option value="6" <?php echo $estado == '6' ? 'selected' : '' ?>>Anulado</option>
            </select>
          </div>
          <div style="margin-right: 20px;">
            <label for="">Cliente</label>
            <input class="form-control" type="search" name="buscar" value="<?php echo $buscar ?>">
          </div>
          <div style="margin-right: 20px;">
            <button type="submit" class="btn btn-primary" style="margin-right: 10px;">BUSCAR</button>
            <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn btn-success">MOSTRAR TODOS</a>
          </div>
        </div>
      </form>
    </div>

    <div style="margin-top: 40px;">Número de registros encontrados <span style="font-weight: bold;"><?php echo $total ?></span></div>
    <div class="table-responsive" style="margin-top: 10px;">
      <table class="table table-striped" id="listaTodosLosPrestamos" style="width:100%;">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Prestamo</th>
            <th>Tasa</th>
            <th>T. Pago</th>
            <th>Periodo</th>
            <th>Tipo</th>
            <th>F. Desembolso</th>
            <th>Cartera</th>
            <th>Estado</th>
            <th>PDF</th>
            <?php if (
              $request->user()->tipoU === '1' ||
              $request->user()->tipoU === '2' ||
              $request->user()->tipoU === '3' ||
              $request->user()->tipoU === '7'
            ) { ?>
              <th>Acción</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($credits as $credit) { ?>
            <tr>
              <td>
                <div><?php echo $credit->dni ?></div>
                <div><?php echo $credit->ap . " " . $credit->am . " " . $credit->nom ?></div>
              </td>
              <td style="text-align: right;"><?php echo number_format($credit->capital / 10, 2) ?></td>
              <td><?php echo round($credit->interest_rate, 2) . "%"; ?></td>
              <td><?php echo $credit->paymentPeriodToString() ?></td>
              <td><?php echo $credit->number_installments ?></td>
              <td><?php echo $credit->name ?></td>
              <td style="color:red;font-weight:bold">
                <?php echo $credit->fechaDesembolso ? date('d/m/Y', strtotime($credit->fechaDesembolso)) : '' ?>
              </td>
              <td style="white-space: nowrap;"><?php echo $credit->apU ?></td>
              <td><?php echo $credit->statusToString() ?></td>
              <td>
                <?php if ($credit->estado === '4' || $credit->estado === '5') { ?>

                  <a href="#" class="btn btn-default" onclick="imprimir_detalle_pago('<?php echo $credit->idP ?>')" title="Historial de pago">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                      <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
                      <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
                      <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z" />
                    </svg>
                  </a>
                  <a href="#" class='btn btn-default' title='Descargar Prestamo' onclick="imprimir_prestamo('<?php echo $credit->idP ?>');"><i class="glyphicon glyphicon-download"></i></a>
                  <a class='btn btn-sm btn-default' title='Voucher' onclick="imprimir_voucher('<?php echo $credit->idP ?>')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z" />
                      <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z" />
                    </svg>
                  </a>
                  <a class='btn btn-sm btn-default' title='letra' onclick="imprimir_letra('<?php echo $credit->idP ?>')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-text" viewBox="0 0 16 16">
                      <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z" />
                      <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z" />
                    </svg>
                  </a>
                  <a class='btn btn-sm btn-default' title='Contrato' onclick="imprimir_contrato_nuevo('<?php echo $credit->idP ?>')">
                    <i class="fa fa-file-pdf-o" style="color:black"></i>
                  </a>

                  <!-- <button class="btn btn-sm btn-default" title="Pagare" onclick="imprimirPagare(<?php echo $credit->idP ?>)">
                    <i class="fa fa-flag" aria-hidden="true"></i>
                  </button> -->

                  <button class="btn btn-sm btn-default" title="Aviso cobranza" onclick="imprimirAvisoCobranza(<?php echo $credit->idP ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-megaphone" viewBox="0 0 16 16">
                      <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-.214c-2.162-1.241-4.49-1.843-6.912-2.083l.405 2.712A1 1 0 0 1 5.51 15.1h-.548a1 1 0 0 1-.916-.599l-1.85-3.49a68.14 68.14 0 0 0-.202-.003A2.014 2.014 0 0 1 0 9V7a2.02 2.02 0 0 1 1.992-2.013 74.663 74.663 0 0 0 2.483-.075c3.043-.154 6.148-.849 8.525-2.199V2.5zm1 0v11a.5.5 0 0 0 1 0v-11a.5.5 0 0 0-1 0zm-1 1.35c-2.344 1.205-5.209 1.842-8 2.033v4.233c.18.01.359.022.537.036 2.568.189 5.093.744 7.463 1.993V3.85zm-9 6.215v-4.13a95.09 95.09 0 0 1-1.992.052A1.02 1.02 0 0 0 1 7v2c0 .55.448 1.002 1.006 1.009A60.49 60.49 0 0 1 4 10.065zm-.657.975 1.609 3.037.01.024h.548l-.002-.014-.443-2.966a68.019 68.019 0 0 0-1.722-.082z" />
                    </svg>
                  </button>
                <?php } ?>
              </td>

              <?php if (
                $request->user()->tipoU === '1' ||
                $request->user()->tipoU === '2' ||
                $request->user()->tipoU === '3' ||
                $request->user()->tipoU === '7'
              ) { ?>
                <td>
                  <?php if ($credit->estado !== '6') { ?>
                    <?php if (($request->user()->tipoU === '1' || $request->user()->tipoU === '2') && $credit->refinanced) { ?>
                      <button class="btn btn-xs btn-default" onclick="toggleRefinanciado(<?php echo $credit->idP ?>)" title="Cambiar estado a no refinanciado">
                        <img src="../public/resource/refinanciar_color.png" />
                      </button>
                    <?php } else if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2') { ?>
                      <button class="btn btn-xs btn-default" onclick="toggleRefinanciado(<?php echo $credit->idP ?>)" title="Cambiar estado a refinanciado">
                        <img src="../public/resource/refinanciar_black.png" />
                      </button>
                    <?php } ?>

                    <?php if ($credit->estado === '1' || $credit->estado === '2' || $credit->estado === '4') { ?>

                      <input type="hidden" id="txtconte<?php echo $credit->idP ?>" value="<?php echo $credit->tlocal ?>">
                      <?php if ($credit->tlocal === '1') { ?>
                        <a id="btncampo<?php echo $credit->idP ?>" class="btn btn-sm btn-default" title="Pagara en campo" onclick="campero('<?php echo $credit->idP ?>')">
                          <i id="btncampo2<?php echo $credit->idP ?>" class="fa fa-pied-piper-alt" style="color: green;"></i>
                        </a>
                      <?php } else { ?>
                        <a id="btncampo<?php echo $credit->idP ?>" class="btn btn-sm btn-default" title="Pagara en Oficina" onclick="campero('<?php echo $credit->idP ?>')">
                          <i id="btncampo2<?php echo $credit->idP ?>" class="fa fa-university" style="color: red;"></i>
                        </a>
                      <?php } ?>

                    <?php } ?>

                    <?php if (
                      ($credit->estado === "4") &&
                      ($request->user()->tipoU === "1")
                    ) { ?>
                      <a href="<?php echo $_ENV['SERVER2'] ?>/credits/<?php echo $credit->idP ?>" class="btn btn-sm btn-default" title="Cambios">
                        <i class="fa fa-cogs" data-toggle="tooltip"></i>
                      </a>
                    <?php } ?>

                    <?php if ($credit->estado === '1') { ?>
                      <a href="#" data-target="#confirmarPrestamo" class="btn btn-sm btn-default" data-toggle="modal" data-id='<?php echo $credit->idP ?>' data-montop='<?php echo $credit->capital / 10 ?>' data-montoa='<?php echo $credit->capital / 10 ?>' data-taza='<?php echo $credit->interest_rate ?>'>
                        <i class="fa fa-handshake-o" data-toggle="tooltip" title="Confirmar"></i>
                      </a>
                    <?php } ?>

                    <?php if ($credit->estado === '2') { ?>
                      <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modalDesembolsar<?php echo $credit->idP ?>">
                        <i class="fa fa-legal" style="color:red"></i>
                      </button>
                    <?php } ?>

                    <?php if ($credit->estado === '1' || $credit->estado === '2' || $credit->estado === '3' || $credit->estado === '4') { ?>
                      <a class='btn btn-sm btn-default' title='Denegar o Anular' onclick="denegar('<?php echo $credit->idP ?>')">
                        <i class="glyphicon glyphicon-trash" style="color:#EC7063"></i>
                      </a>
                    <?php } ?>

                    <!-- Modal desembolsar -->
                    <div class="modal fade" id="modalDesembolsar<?php echo $credit->idP ?>" tabindex="-1" role="dialog" aria-labelledby="modalDesembolsarLabel">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalDesembolsarLabel">DESEMBOLSO</h4>
                          </div>
                          <div class="modal-body">
                            <table style="width: 100%;">
                              <tbody>
                                <tr style="height: 30px;">
                                  <td><b>CLIENTE:</b></td>
                                  <td colspan="3"><?php echo $credit->ap . ' ' . $credit->am . ' ' . $credit->nom ?></td>
                                </tr>
                                <tr style="height: 30px;">
                                  <td><b>MONTO:</b></td>
                                  <td><?php echo number_format($credit->capital / 10, 2) ?></td>
                                  <td><b>TASA:</b></td>
                                  <td><?php echo $credit->interest_rate ?>%</td>
                                </tr>
                                <tr style="height: 30px;">
                                  <td><b>PERIODO:</b></td>
                                  <td><?php echo $credit->number_installments ?></td>
                                  <td><b>CUOTA:</b></td>
                                  <td>
                                    <?php
                                    if ($credit->number_installments > 0) {
                                      $interest = ceil($credit->capital * $credit->interest_rate / 100);
                                      $total = ($credit->capital + $interest) / $credit->number_installments;
                                      echo number_format($total / 10, 2);
                                    } else {
                                      echo "0.00";
                                    }
                                    ?>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
                            <button type="button" class="btn btn-primary" onclick="desembolsar('<?php echo $credit->idP ?>','<?php echo $credit->idCG ?>', '<?php echo $_ENV['API_PATH'] ?>', '<?php echo $request->user()->idU ?>', '<?php echo $_COOKIE['tofi'] ?? null ?>');">CONFIRMAR</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </td>
              <?php } ?>

            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div style="text-align: right;">
      <?php for ($i = 0; $i < $numberPages; $i++) { ?>
        <button class="btn btn-sm <?php echo $page == $i + 1 ? 'btn-primary' : 'btn-secundary' ?>" name="page" value="<?php echo $i + 1 ?>" form="formFiltros"><?php echo $i + 1 ?></button>
      <?php } ?>
    </div>
  </div>
</div>

<?php
include "modal/editConfirmar.php";
?>

<script>
  function imprimirPagare(creditId) {
    VentanaCentrada('../app/pdf/pagare.php?creditId=' + creditId, 'Pagare', '', '1024', '768', 'true');
  }

  function imprimirAvisoCobranza(creditId) {
    VentanaCentrada('../app/pdf/avisoCobranza.php?creditId=' + creditId, 'Pagare', '', '1024', '768', 'true');
  }

  let desembolsando = false;

  function desembolsar(id, idc, api_path, auth_id, office_id) {
    if (desembolsando) {
      console.log('ya se esta desembolsando');
      return;
    }

    desembolsando = true;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('auth_id', auth_id);
    formData.append('office_id', office_id);

    fetch(api_path + '/credits/' + id + '/disburse', {
        // fetch(api_path + '../app/api/desembolsar.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        swal({
          title: data.message,
          icon: data.success ? 'success' : 'error'
        }).then(_ => {
          if (data.success) {
            window.location.reload();
          }
        });
      })
      .finally(_ => {
        desembolsando = false;
      });
  }

  let editandoCredito = false;

  function editarCredito(event, creditId) {
    event.preventDefault();

    if (editandoCredito) {
      return;
    }

    swal({
      title: `Se realizaran cambios en las condiciones del credito N° ${creditId}`,
      icon: "info",
      buttons: ["Cancelar", "Continuar con el cambio"],
    }).then(value => {
      if (value) {
        editandoCredito = true;

        const data = {
          creditId,
          tasa: document.getElementById('txtporcenta').value,
          fechaDesembolso: document.getElementById('txtfechaDesembolso').value,
          prestamo: document.getElementById('txtmonto').value,
          tipoPago: document.getElementById('lstpago').value,
          fechaInicio: document.getElementById('txtfechaPago').value,
          plazo: document.getElementById('txtplazom').value
        }

        fetch(`../app/api/editarCredito.php`, {
            method: 'post',
            body: JSON.stringify(data),
            headers: {
              'Content-Type': 'application/json'
            },
          })
          .then(response => response.json())
          .then(data => {
            swal({
              title: data.message,
              icon: data.success ? 'success' : 'error'
            });
          })
          .finally(_ => {
            editandoCredito = false;
          });
      }
    });
  }

  function toggleRefinanciado(creditId) {
    fetch(`./../app/api/toggleRefinanciado.php?creditId=${creditId}`)
      // .then(response => response.json())
      .then(_ => {
        location.reload();
      })
  }
</script>
<?php include('footer.php'); ?>
<script src="js/prestamoR.js"></script>
<script type="text/javascript" src="js/VentanaCentrada.js"></script>