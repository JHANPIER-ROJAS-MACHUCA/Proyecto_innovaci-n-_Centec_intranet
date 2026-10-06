<?php
include("head.php");
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<style media="screen">
  .juve17 {
    padding-top: 5px;
  }
</style>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>

    <h5 style="color:white">Cobro de Crédito<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" x-data="app">
    <div class="tabs-container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1"><strong>DATOS DEL CLIENTE</strong></a></li>
        <!--<li class=""><a data-toggle="tab" href="#tab-2">Direcciones</a></li>
           <li class=""><a data-toggle="tab" href="#tab-3">Negocios</a></li>
           <li class=""><a data-toggle="tab" href="#tab-4">Prestamos</a></li>-->
      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
          <div class="panel-body">
            <fieldset class="form-horizontal">
              <div class="form-group">
                <div class="col-md-6 col-sm-6">
                  <div class="input-group margin" style="padding-top:10px">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">DNI </a>
                    </span>
                    <!--<input autocomplete="off" accesskey="q" onkeypress="return numero(event)" title="DNI del cliente" maxlength="8" class="form-control" style="text-align:right" placeholder="# Dni" type="text" name="txtdni" id="txtdni" value="">-->
                    <select class="select2_demo_3 form-control" id="txtdni" x-ref="select_customer_id">
                      <option value="" disabled selected>-- Seleccione--</option>
                      <?php
                      if ($_COOKIE['tuser'] == '3' || $_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '5' || $_COOKIE['tuser'] == '2') {
                        $ususa = extraer("SELECT clientes.dni,concat(clientes.ap,' ',clientes.am,' ',clientes.nom) as dato,clientes.idCG 
                        FROM tclie_general clientes 
                        INNER JOIN tprestamo creditos ON creditos.idCG=clientes.idCG 
                        WHERE creditos.estado=4
                        group by clientes.idCG
                        order by clientes.ap asc");
                        // $ususa = extraer("SELECT dni,concat(ap,' ',am,' ',nom) as dato,idCG FROM tclie_general order by ap asc");
                      } else {
                        $identi = $_COOKIE['user1'];
                        $ususa = extraer("SELECT clientes. dni,concat(clientes.ap,' ',clientes.am,' ',clientes.nom) as dato,clientes.idCG 
                        FROM tclie_general clientes
                        INNER JOIN tprestamo creditos ON creditos.idCG=clientes.idCG
                        WHERE creditos.estado=4
                        AND clientes.idU='$identi'
                        group by clientes.idCG
                        order by clientes.ap asc");
                        // $ususa = extraer("SELECT dni,concat(ap,' ',am,' ',nom) as dato,idCG FROM tclie_general where idU='$identi' order by ap asc");
                      }
                      while ($row = mysqli_fetch_array($ususa)) {
                      ?>
                        <option value="<?php echo $row['idCG'] ?>"> <?php echo $row['dni'] . " | " . $row['dato'] ?></option>
                      <?php
                      }
                      ?>

                    </select>
                    <input type="hidden" class="form-control" id="idPres" value="">
                    <span class="input-group-btn">
                      <a class="btn btn-info" accesskey="a" style="font-weight:bold" @click="loadDataCredits">
                        <i class="fa fa-search"></i>
                      </a>
                    </span>
                  </div>
                </div>
                <?php //estado 
                ?>
                <div class="col-md-5 col-sm-5">
                  <div class="input-group margin" style="padding-top:10px">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;;background:#23c6c8">ESTADO </a>
                    </span>
                    <div style="font-size: 25px;width: 100%;height: 35px;background: #E0E0E0; padding: 0 10px;text-align: center;font-weight: bold;">
                      <span :style="{color: colorStatus()}" x-text="data && data.estado"></span>
                    </div>
                  </div>
                </div>
                <?php //prestamos 
                ?>
                <div class="col-md-5">
                  <div class="input-group margin" style="padding-top:10px">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">Prestamo </a>
                    </span>
                    <select class="form-control" name="txtprestamos" @change="handleSelectCredit" id="txtprestamos">
                      <option value="" selected disabled>--Seleccione Prestamo--</option>
                      <template x-for="credit in credits">
                        <option :value="credit.idP" x-text="'Credito ' + credit.n_credito + '»»' + (credit.capital / 10) + ' plazo ' + credit.number_installments"></option>
                      </template>
                    </select>
                  </div>
                </div>
                <div class="col-md-7">
                  <div x-show="data && data.id">
                    <div style="display: flex; align-items: center; height: 100%; margin-top: 10px;">
                      <button @click="showPaymentHistory" style="margin-right: 5px;padding: 5px 15px;">Historial de pagos</button>
                      <button @click="showPaymentDetails" style="margin-right: 5px;padding: 5px 15px;">Detalle de pagos</button>
                      <button @click="showCreditCancellation" style="margin-right: 5px; padding: 5px 15px;">Cancelar credito</button>
                      <!-- <button @click="openJustifyNonPayment" style="padding: 5px 15px;" x-text="data && data.justifyNonPayment ? 'Editar justificación' : 'Justificar no pago'"></button> -->
                    </div>

                    <!-- <template x-if="justifyNonPayment">
                      <div @keyup.escape.document="justifyNonPayment = null" style="position: fixed; inset: 0; z-index: 3000; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
                        <div style="width: 100%; max-width: 500px; background-color: white; border-radius: 5px;">
                          <div style="padding: 20px 20px 0;">
                            <textarea rows="3" class="form-control" x-model="justifyNonPayment.description"></textarea>
                          </div>
                          <div style="display: flex; padding: 20px;">
                            <button class="btn btn-danger" x-show="justifyNonPayment.id" @click="deleteJustificationNonPayment">Eliminar</button>
                            <button class="btn btn-secundary" style="margin-left: auto;" @click="justifyNonPayment = null">Cerrar</button>
                            <button class="btn btn-primary" style="margin-left: 10px;" @click="handleSubmitCreateJustifyNonPayment">Guardar</button>
                          </div>
                        </div>
                      </div>
                    </template> -->

                    <template x-if="modalCreditCancelation">
                      <div @keyup.escape.document="closeCreditCancellation" style="display: flex; justify-content: center; align-items: center; position: fixed; z-index: 3000; inset: 0; background: rgba(0,0,0,0.5); padding: 20px;">
                        <div style="display: flex; flex-direction: column; width: 100%; max-width: 500px; max-height: 100%; background: white; border-radius: 10px;">
                          <div style="display: flex; justify-content: space-between;padding: 10px 20px;">
                            <h3>Cancelar crédito</h3>
                            <button @click="closeCreditCancellation" style="display: flex; justify-content: center; align-items: center; border: 1px solid #EAECEE;background: transparent; width: 30px; height: 30px; padding: 0;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                              </svg>
                            </button>
                          </div>

                          <div style="flex: 1 1 auto; overflow-y: auto; padding: 10px 20px;">
                            <div><span style="font-weight: bold;">Cliente:</span> <span x-text="data && data.customer_name"></span></div>
                            <div><span style="font-weight: bold;">Cuenta:</span> <span x-text="data && data.id"></span></div>
                            <div style="margin-bottom: 20px;"><span style="font-weight: bold;">F. Desembolso:</span> <span x-text="data && data.disburment_date"></span></div>

                            <h4>Datos del crédito</h4>
                            <div class="row">
                              <div class="col-sm-4">
                                <label>Capital:</label>
                                <input class="form-control" type="text" x-bind:value="data && data.capital / 10" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Utilidad:</label>
                                <input class="form-control" type="text" x-bind:value="data && data.interest / 10" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Deuda:</label>
                                <input class="form-control" type="text" x-bind:value="data && (data.capital + data.interest) / 10" disabled>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-sm-4">
                                <label>Abono capital:</label>
                                <input class="form-control" type="text" x-bind:value="data && (data.capital - data.capital_debt) / 10" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Abono utilidad:</label>
                                <input class="form-control" type="text" x-bind:value="data && (data.interest - data.interest_debt) / 10" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Abono total:</label>
                                <input class="form-control" type="text" x-bind:value="data && (data.capital + data.interest - data.capital_debt - data.interest_debt) / 10" disabled>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-sm-4">
                                <label>Pendiente capital</label>
                                <input class="form-control" type="text" x-bind:value="data && data.capital_debt / 10" style="color: blue; font-weight: bold;" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Pendiente utilidad</label>
                                <input class="form-control" type="text" x-bind:value="data && data.interest_debt / 10" style="color: blue; font-weight: bold;" disabled>
                              </div>
                              <div class="col-sm-4">
                                <label>Mora</label>
                                <input class="form-control" type="text" x-bind:value="data && data.penalty_debt / 10" style="color: blue; font-weight: bold;" disabled>
                              </div>
                            </div>

                            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                              <label style="margin-bottom: 0;">TOTAL PENDIENTE</label>
                              <input type="text" class="form-control" x-bind:value="data && (data.capital_debt + data.interest_debt + data.penalty_debt) / 10" style="width: 100px;" disabled>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px;">
                              <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 10px;">
                                <div>
                                  <label>
                                    <input type="radio" value="none" x-model="discount_type">
                                    Sin descuento
                                  </label>
                                </div>
                                <div>
                                  <label>
                                    <input type="radio" value="amount" x-model="discount_type">
                                    S/.
                                  </label>
                                </div>
                                <div>
                                  <label>
                                    <input type="radio" value="percentage" x-model="discount_type">
                                    %
                                  </label>
                                </div>
                              </div>
                              <div>
                                <input type="text" class="form-control" placeholder="0" x-model="discount_value" :disabled="discount_type == 'none'" style="width: 100px;">
                              </div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 5px;">
                              <label style="margin-bottom: 0;">DESCUENTO TOTAL:</label>
                              <input type="text" class="form-control" :value="discount() / 10" style="width: 100px;" disabled>
                            </div>
                          </div>

                          <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
                            <div>
                              <span>TOTAL A PAGAR:</span>
                              <span style="font-weight: bold; font-size: 18px;">S/</span>
                              <span style="font-weight: bold; font-size: 18px;" x-text="totalAPagar() / 10"></span>
                            </div>
                            <div>
                              <button class="btn btn-secundary" @click="closeCreditCancellation">Cerrar</button>
                              <button class="btn btn-primary" @click="handleSubmitCancelCredit">Cancelar credito</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-4">
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">Fecha desembolso </a>
                    </span>
                    <div style="background: #E0E0E0;height: 33px; display: flex;justify-content: center; align-items: center;">
                      <div id="txtFechaDesembolso" style="font-size: 20px;text-align: center;font-weight: bold;" x-text="data && data.disburment_date"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group margin">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">Fecha Finalización </a>
                    </span>
                    <div style="background: #E0E0E0;height: 33px; display: flex;justify-content: center; align-items: center;">
                      <div id="txtFechaFinalización" style="font-size: 20px;text-align: center;font-weight: bold;" x-text="data && data.expiration_date"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div style="display: flex; justify-items: center;">
                    <div class="input-group" style="width: 100%;">
                      <span class="input-group-btn" disabled>
                        <a class="btn btn-info" disabled style="font-weight:bold;background:#23c6c8">Total a pagar </a>
                      </span>
                      <div style="background: #E0E0E0;height: 33px; display: flex;justify-content: center; align-items: center;">
                        <div id="txtTotalAPagar" style="font-size: 20px;text-align: center;font-weight: bold;" x-text="getTotalParaEstarAlDia() / 10"></div>
                      </div>
                    </div>
                    <button class="btn btn-danger" style="white-space:nowrap; height: 33px;" @click="nivelarPago">Pagar todo</button>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="tabs-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-a1"><strong>DATOS DEL COBRO</strong></a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="tab-a1" class="tab-pane active">
                      <div class="panel-body">
                        <fieldset class="form-horizontal">
                          <div class="form-group">
                            <?php //situacion de cuotas 
                            ?>
                            <div class="col-md-4">
                              <div class="tabs-container">
                                <label for="">Situación de cuotas</label>
                                <ul class="nav nav-tabs">
                                </ul>
                                <div class="tab-content">
                                  <div class="tab-pane active">
                                    <div class="panel-body">
                                      <fieldset class="form-horizontal">
                                        <div class="form-group">
                                          <?php //total de cuotas 
                                          ?>
                                          <div class="col-sm-12">
                                            <?php //cuotas 
                                            ?>
                                            <div class="col-sm-8 juve17">
                                              <label for="">Total de Cuotas</label>
                                            </div>
                                            <div class="col-sm-4 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white" id="txttotalCuotas" disabled placeholder="00" :value="data && data.installments.length">
                                            </div>
                                            <?php //pendientes 
                                            ?>
                                            <div class="col-sm-8 juve17">
                                              <label for="">N° de Cuotas Pendientes</label>
                                            </div>
                                            <div class="col-sm-4 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white" id="txtcuotaPendiente" placeholder="00" :value="getNumeroCuotasPendientes()">
                                            </div>
                                            <?php //vencidas 
                                            ?>
                                            <div class="col-sm-8 juve17">
                                              <label for="">N° de Cuotas Vencidas</label>
                                            </div>
                                            <div class="col-sm-4 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white;color:red;font-weight:bold" id="txtcuotasVenci" placeholder="00" :value="getNumeroCuotasVencidas()">
                                            </div>
                                            <?php //dias de atraso 
                                            ?>

                                            <div class="col-sm-8 juve17">
                                              <label for="">Días de atraso del Crédito</label>
                                            </div>
                                            <div class="col-sm-4 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white;font-weight:bold" id="txtdiaRetraso" placeholder="00" :value="getDiasDeAtrasoDelCredito()" :style="(getDiasDeAtrasoDelCredito() <= 0) ? {color: 'blue'} : {color: 'red'}">
                                            </div>
                                          </div>
                                        </div>
                                      </fieldset>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php //deuda de cuotas pendientes 
                            ?>
                            <div class="col-md-4">
                              <div class="tabs-container">
                                <label for="">Deuda de Cuotas Pendientes</label>
                                <ul class="nav nav-tabs">
                                </ul>
                                <div class="tab-content">
                                  <div class="tab-pane active">
                                    <div class="panel-body">
                                      <fieldset class="form-horizontal">
                                        <div class="form-group">
                                          <?php //total de cuotas 
                                          ?>
                                          <div class="col-sm-12 col-md-12">
                                            <?php //total pendiente 
                                            ?>
                                            <div class="col-sm-6 col-md-7 juve17">
                                              <label for="">Total Pendiente</label>
                                            </div>
                                            <div class="col-sm-6 col-md-5 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white;color:blue;font-weight:bold" id="txttotalpendiente" placeholder="00" :value="getTotalPendiente() / 10">
                                            </div>
                                            <?php //pendiente 
                                            ?>
                                            <div class="col-sm-6 col-md-7 juve17">
                                              <label for="">Pendiente</label>
                                            </div>
                                            <div class="col-sm-6 col-md-5 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white;color:red;font-weight:bold" id="txtpendiente" placeholder="00" :value="getPendiente() / 10">
                                            </div>
                                            <?php //cuota 
                                            ?>
                                            <div class="col-sm-6 col-md-7 juve17">
                                              <label for="">Cuota</label>
                                            </div>
                                            <div class="col-sm-6 col-md-5 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white" id="txtcuota" placeholder="00" :value="data && data.average_installment / 10">
                                              <input type="hidden" class="form-control" disabled id="txtmoraporcuota" placeholder="00">
                                            </div>

                                            <?php //mora
                                            ?>
                                            <div class="col-sm-6 col-md-7 juve17">
                                              <label for="">Mora del Crédito</label>
                                            </div>
                                            <div class="col-sm-6 col-md-5 juve17">
                                              <input type="text" class="form-control" disabled style="text-align:center;background:white;color:red;font-weight:bold" id="txtmoraCre" placeholder="00" :value="getMora() / 10">

                                            </div>
                                          </div>
                                        </div>
                                      </fieldset>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php //monto a cobrar 
                            ?>
                            <div class="col-md-4">
                              <form>
                                <div class="tabs-container">
                                  <label for="">Monto a Cobrar</label>
                                  <ul class="nav nav-tabs">
                                  </ul>
                                  <div class="tab-content">
                                    <div class="tab-pane active">
                                      <div class="panel-body">
                                        <fieldset class="form-horizontal">
                                          <div class="form-group">
                                            <?php //total de cuotas 
                                            ?>
                                            <div class="col-sm-12">
                                              <?php //total pendiente 
                                              ?>
                                              <div class="col-sm-6 col-md-7 juve17">
                                                <label for="">Monto Neto</label>
                                              </div>
                                              <div class="col-sm-6 col-md-5 juve17">
                                                <input type="text" class="form-control" style="text-align:right" placeholder="0.00" x-model="payment_amount" :disabled="!data || payment_by_installment">
                                              </div>
                                              <?php //cuota 
                                              ?>
                                              <div class="col-sm-6 col-md-7 juve17">
                                                <label>
                                                  <input type="checkbox" name="chkcuota" x-model="payment_by_installment">
                                                  ¿Por cuota?
                                                </label>
                                              </div>
                                              <div class="col-sm-6 col-md-5 juve17">
                                                <input type="number" autocomplete="off" onkeypress="return numero(event);" style="text-align:center" min="1" class="form-control" placeholder="0" x-model="payment_installment" :disabled="!data || !payment_by_installment">
                                                <input type="hidden" name="txtmonCuo" id="txtmonCuo" class="form-control" value="">
                                              </div>
                                              <?php //pendiente 
                                              ?>
                                              <div class="col-sm-6 col-md-7 juve17">
                                                <label>
                                                  <input type="checkbox" x-model="active_payment_mora">
                                                  ¿Dias de mora?
                                                </label>
                                              </div>
                                              <div class="col-sm-6 col-md-5 juve17">
                                                <input type="number" autocomplete="off" class="form-control" onkeypress="return numero(event);" min="1" style="text-align:right" placeholder="0" x-model="payment_mora" :disabled="!data || !active_payment_mora">
                                                <input type="hidden" name="txtmonmora" id="txtmonmora" class="form-control" value="">
                                                <input type="hidden" name="txtmor" id="txtmor" class="form-control" value="">
                                                <input type="hidden" name="txtcan" id="txtcan" class="form-control" value="">
                                                <input type="hidden" name="txtmoras" id="txtmoras" class="form-control" value="">
                                              </div>
                                              <?php //mora
                                              ?>
                                              <div class="col-sm-6 col-md-7 juve17">
                                                <label for="">TOTAL PAGO</label>
                                              </div>
                                              <div class="col-sm-6 col-md-5 col-md-7 juve17">
                                                <input type="text" class="form-control" disabled style="text-align:right" placeholder="0.00" :value="totalPago() / 10">
                                              </div>
                                              <?php //fecha 
                                              ?>
                                              <div class="col-sm-6 juve17">
                                                <label for="" style="white-space: nowrap;">
                                                  <input type="checkbox" id="checkedActivateDate">
                                                  FECHA
                                                </label>
                                              </div>
                                              <div class="col-sm-6 juve17">
                                                <input disabled type="date" class="form-control" placeholder="dd/mm/aaaa" name="txtfechaPago" id="txtfechaPago" style="text-align:center" value="<?php echo date('Y-m-d') ?>">
                                              </div>
                                              <div class="col-sm-6 juve17">
                                                <label for="" style="white-space: nowrap;">
                                                  METODO DE PAGO
                                                </label>
                                              </div>
                                              <div class="col-sm-6 juve17">
                                                <select class="form-control" x-model="payment_method.type">
                                                  <option value="efectivo" selected>Efectivo</option>
                                                  <option value="yape">Yape</option>
                                                  <option value="transferencia bancaria">Transferencia bancaria</option>
                                                </select>
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type == 'transferencia bancaria'">
                                                <label for="" style="white-space: nowrap;">
                                                  N° OPERACIÓN
                                                </label>
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type == 'transferencia bancaria'">
                                                <input type="text" class="form-control" x-model="payment_method.reference">
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type == 'transferencia bancaria'">
                                                <label for="" style="white-space: nowrap;">
                                                  FECHA DE DEPOSITO
                                                </label>
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type == 'transferencia bancaria'">
                                                <input type="date" class="form-control" x-model="payment_method.date">
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type != 'efectivo'">
                                                <label for="" style="white-space: nowrap;">
                                                  CUENTA
                                                </label>
                                              </div>
                                              <div class="col-sm-6 juve17" x-show="payment_method.type != 'efectivo'">
                                                <select class="form-control" x-model="payment_method.account">
                                                  <option value="" selected>Seleccione</option>
                                                  <template x-for="account in getAccounts">
                                                    <option :value="account.id" x-text="account.number + ' - ' + account.name"></option>
                                                  </template>
                                                </select>
                                              </div>
                                            </div>
                                          </div>
                                        </fieldset>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12" style="text-align:right">
                              <a href="./creditoCobrarHoy.php" class="btn btn-secundary">Lista de cobros</a>
                              <button class="btn btn-sm btn-secundary" @click="resetData"><i class="fa fa-window-close-o"> </i> Cancelar</button>
                              <button class="btn btn-sm btn-info" @click="cobrarCuotas"><i class="fa fa-save"> </i> Guardar</button>
                            </div>
                          </div>
                        </fieldset>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="functiones/cobro.js">
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $("#txtdni").select2({
      minimumResultsForSearch: 2,
      placeholder: "- - Seleccione Cliente - -",
      allowClear: false,
      width: '100%',
      height: '100%',
    });

  });

  function showBoucher(transactionId) {
    window.open('../app/pdf/voucher.php?operacion=' + transactionId, 'voucherTransactionCobro', "width=600,height=850,scrollbars=NO");
  }
</script>

<script>
  function floatval(value) {
    if (value === '') {
      return 0;
    }

    if (isNaN(value)) {
      return 0;
    }

    return parseFloat(value);
  }

  const jsutifyNonPaymentDefailt = {
    credit_id: '',
    description: ''
  }

  document.addEventListener('alpine:init', () => {
    Alpine.data('app', () => ({
      accounts: <?php echo $database->table('bank_accounts')->get() ?>,
      credits: [],
      customer_id: '',
      data: null,
      modalCreditCancelation: false,
      discount_type: 'none',
      discount_value: '',
      payment_amount: '',
      payment_installment: '',
      payment_mora: '',
      payment_by_installment: false,
      active_payment_mora: false,
      payment_method: {
        type: 'efectivo',
        date: '',
        account: '',
        reference: ''
      },
      justifyNonPayment: null,

      getNumeroCuotasPendientes() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          if (
            installment.capital_debt > 0 ||
            installment.interest_debt > 0
          ) {
            return total + 1;
          }

          return total
        }, 0);
      },

      getNumeroCuotasVencidas() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          if (installment.penalty_debt > 0) {
            return total + 1;
          }

          return total;
        }, 0);
      },

      getDiasDeAtrasoDelCredito() {
        if (this.data === null) {
          return 0;
        }

        let installmentToPay = null;
        this.data.installments.forEach(i => {
          if (((i.capital_debt + i.interest_debt) > 0) && installmentToPay === null) {
            installmentToPay = i.expiration_at
          }
        });

        if (installmentToPay === null) {
          return 0
        }

        let fechaInicio = new Date(installmentToPay).getTime();
        let fechaFin = new Date(this.data.now_date).getTime();

        let diff = fechaFin - fechaInicio;

        return diff / (1000 * 60 * 60 * 24);
      },

      getTotalPendiente() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          return total + installment.capital_debt + installment.interest_debt;
        }, 0);
      },

      getPendiente() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          if (this.data.now_date >= installment.expiration_at) {
            return total + installment.capital_debt + installment.interest_debt;
          }

          return total;
        }, 0);
      },

      getMora() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          return total + installment.penalty_debt;
        }, 0);
      },

      getTotalParaEstarAlDia() {
        if (this.data === null) {
          return 0;
        }

        return this.data.installments.reduce((total, installment) => {
          if (installment.expiration_at <= this.data.now_date) {
            return total + installment.capital_debt + installment.interest_debt + installment.penalty_debt;
          }

          return total;
        }, 0);
      },

      openJustifyNonPayment() {
        if (!this.data) {
          return;
        }

        if (this.data.justifyNonPayment) {
          this.justifyNonPayment = {
            id: this.data.justifyNonPayment.id,
            credit_id: this.data.justifyNonPayment.credit_id,
            description: this.data.justifyNonPayment.description
          };
          return;
        }

        this.justifyNonPayment = {
          credit_id: this.data.id,
          description: ''
        };
      },
      init() {
        this.$watch('discount_value', (value, oldValue) => {
          if (!this.data) {
            return;
          }

          if (isNaN(value)) {
            this.discount_value = oldValue;
            return;
          }

          if (this.discount_type === 'amount') {
            if (this.data.interest_debt < value) {
              this.discount_value = this.data.interest_debt;
            }
          } else if (this.discount_type === 'percentage') {
            const total = Math.round((this.data.interest_debt * this.discount_value / 100) * 10) / 10;
            if (total > this.data.interest_debt) {
              this.discount_value = 100;
            }
          }
        })
        this.$watch('payment_amount', (value, oldValue) => {
          if (!this.data) {
            return;
          }

          if (isNaN(value)) {
            this.payment_amount = oldValue;
            return;
          }

          var total = this.data.installments.reduce((total, installment) => {
            return total + installment.capital_debt + installment.interest_debt;
          }, 0);

          if ((value * 10) > total) {
            this.payment_amount = total / 10;
          }
        })
        this.$watch('payment_mora', (value, oldValud) => {
          if (!this.data) {
            return;
          }

          if (isNaN(value)) {
            this.payment_mora = oldValud;
            return;
          }

          let totalInstallmentWithPenalty = this.data.installments
            .reduce((total, installment) => {
              if (installment.penalty_debt > 0) {
                return total + 1;
              }

              return total;
            }, 0);

          if (value > totalInstallmentWithPenalty) {
            this.payment_mora = totalInstallmentWithPenalty;
          }
        })
        this.$watch('payment_installment', (value, oldValue) => {
          if (!this.data) {
            return;
          }

          if (isNaN(value)) {
            this.payment_installment = oldValue;
            return;
          }

          let totalNumberInstallment = this.data.installments.reduce((total, installment) => {
            if (installment.capital_debt > 0 || installment.interest_debt > 0) {
              return total + 1;
            }

            return total;
          }, 0);

          if (value > totalNumberInstallment) {
            this.payment_installment = totalNumberInstallment;
          }
        })
        this.$watch('discount_type', (value) => {
          this.discount_value = '';
        })
      },
      get getAccounts() {
        if (this.payment_method.type === 'yape') {
          return this.accounts.filter(item => item.type == 'digital_wallet');
        }

        return this.accounts.filter(item => item.type == 'bank_account');
      },
      handleSelectCredit(e) {
        this.discount_type = 'none';
        this.discount_value = '';
        this.payment_amount = '';
        this.payment_installment = '';
        this.payment_mora = '';
        this.payment_by_installment = false;
        this.active_payment_mora = false;
        // fetch(`../app/api/detalleCredito.php?creditId=${e.target.value}`)
        fetch(`<?php echo $_ENV['API_PATH'] ?>/credits/${e.target.value}/summary_to_pay`)
          .then(response => response.json())
          .then(data => {
            this.data = data;
          });
      },
      showPaymentHistory() {
        if (!this.data) {
          return;
        }

        window.open('./../app/pdf/resumenCredito.php?creditId=' + this.data.id, 'historial', 'width=1000,height=850,scrollbars=NO');
      },
      showPaymentDetails() {
        if (!this.data) {
          return;
        }

        window.open('./../app/pdf/historialDePago.php?creditId=' + this.data.id, 'pagos', 'width=600,height=850,scrollbars=NO');
      },
      showCreditCancellation() {
        this.modalCreditCancelation = true;
      },
      closeCreditCancellation() {
        this.modalCreditCancelation = false;
      },
      totalPago() {
        if (!this.data) {
          return 0;
        }

        var mora = 0;
        const payment_mora = floatval(this.payment_mora);
        if (this.active_payment_mora) {
          if (payment_mora > 0) {
            mora = this.data.installments
              .filter(installment => installment.penalty_debt > 0)
              .slice(0, payment_mora)
              .reduce((total, installment) => {
                return total + installment.penalty_debt;
              }, 0)
          }
        }

        var cuota = 0;
        if (this.payment_by_installment) {
          const payment_installment = floatval(this.payment_installment)
          if (payment_installment > 0) {
            cuota = this.data.installments
              .filter(installment => (installment.capital_debt + installment.interest_debt) > 0)
              .slice(0, payment_installment)
              .reduce((total, item) => {
                return total + item.capital_debt + item.interest_debt;
              }, 0);
          }
        } else {
          cuota = Math.round(floatval(this.payment_amount) * 10);
        }

        if (cuota > this.data.installmentTotal) {
          return this.data.installmentTotal + mora;
        }

        return cuota + mora;
      },
      totalAPagar() {
        if (this.data === null) {
          return 0;
        }

        var total = this.data.capital_debt + this.data.interest_debt + this.data.penalty_debt;
        var discount = this.discount();

        return total - discount;
      },
      discount() {
        if (this.data === null) {
          return 0;
        }

        if (
          this.discount_type !== 'amount' &&
          this.discount_type !== 'percentage'
        ) {
          return 0;
        }

        const pendiente = this.data.interest_debt;
        var discount_value = floatval(this.discount_value);
        var discount = 0;

        if (this.discount_type === 'amount') {
          discount = Math.round(discount_value * 10);
        } else if (this.discount_type === 'percentage') {
          var value = Math.round(discount_value * 100) / 100;
          discount = Math.round(pendiente * value / 100);
        }

        return discount;

        if (discount < 0) {
          discount = 0;
        } else if (discount > pendiente) {
          discount = pendiente;
        }

        return Math.round(discount * 10) / 10;
      },
      handleSubmitCancelCredit() {
        if (!this.data) {
          return;
        }

        const amount = this.data.installments.reduce((total, installment) => {
          return total + installment.capital_debt + installment.interest_debt;
        }, 0);
        const penalty = this.data.installments.reduce((total, installment) => {
          return total + installment.penalty_debt;
        }, 0);

        const discount = this.discount();

        swal({
          title: '¿Seguro que desea continuar con la cancelación de crédito?',
          icon: 'info',
          buttons: ['No, cerrar', 'Si, continuar con la cancelación']
        }).then((value) => {
          if (value) {
            fetch(`<?php echo $_ENV['API_PATH'] ?>/credits/${this.data.id}/pay`, {
                method: 'post',
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  amount: amount / 10,
                  penalty: penalty / 10,
                  discount: discount / 10,
                  user_id: <?php echo $request->user()->idU ?>
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  swal({
                    text: 'Crédito cancelado con exito!!.',
                    icon: 'success'
                  });
                  showBoucher(data.transaction_id);
                  this.resetData();
                } else {
                  swal({
                    text: "Lo sentimos se produjo un error desconocido.",
                    icon: 'error'
                  });
                }
              });
          }
        });
      },
      resetData() {
        this.data = null;
        this.modalCreditCancelation = false;
        this.discount_type = 'none';
        this.discount_value = '';
        this.payment_amount = '';
        this.payment_installment = '';
        this.payment_mora = '';
        this.payment_by_installment = false;
        this.active_payment_mora = false;
        this.payment_method = {
          type: 'efectivo',
          date: '',
          account: '',
          reference: ''
        }
        document.getElementById('txtprestamos').value = '';
      },
      loadDataCredits() {
        this.resetData();
        const customer_id = document.getElementById('txtdni').value;
        if (!customer_id) {
          return;
        }

        fetch(`../app/api/searchCreditsOfCustomer.php?customerId=${customer_id}`)
          .then(response => response.json())
          .then(data => {
            this.credits = data
          });
      },
      colorStatus() {
        if (!this.data) {
          return '';
        }

        switch (this.data.estado) {
          case 'RETRASADO':
            return 'red';
            break;
          case 'PAGO CUOTA HOY':
            return 'green';
            break;
          case 'PENDIENTE':
            return 'orange';
            break;
          case 'ADELANTADO':
            return 'blue';
            break;
          case 'PENDIENTE MORA':
            return 'black';
            break;
          case 'NO DISPONIBLE':
            return 'black';
            break;
        }
      },
      cobrarCuotas() {
        if (this.data === null) {
          return;
        }

        let amount = 0;
        let penalty = 0;

        if (this.payment_by_installment) {
          amount = this.data.installments
            .filter(item => (item.capital_debt + item.interest_debt) > 0)
            .slice(0, this.payment_installment)
            .reduce((total, item) => {
              return total + item.capital_debt + item.interest_debt;
            }, 0);
          amount = amount / 10;
        } else {
          amount = Math.round(this.payment_amount * 10) / 10
        }

        if (this.active_payment_mora) {
          penalty = this.data.installments
            .filter(item => item.penalty_debt > 0)
            .slice(0, this.payment_mora)
            .reduce((total, item) => {
              return total + item.penalty_debt;
            }, 0);
          penalty = penalty / 10;
        }

        if (!(amount + penalty) > 0) {
          swal({
            icon: 'error',
            title: 'Error',
            text: 'El monto a pagar debe ser mayor a 0.00.'
          });
          return;
        }

        swal({
            icon: 'info',
            title: 'Amortizar cuenta',
            text: `Se amortizara la cuenta ${numberFormat(amount)} y en mora ${numberFormat(penalty)}, asiendo un total de ${numberFormat(amount + penalty)} soles.`,
            buttons: ['No, cerrar', `Si, cobrar ${numberFormat(amount + penalty)} soles.`]
          })
          .then(confirm => {
            if (confirm) {
              const value = this.payment_by_installment ? this.payment_installment : this.payment_amount;
              const type = this.payment_by_installment ? 'installment' : 'amount';
              const mora = this.active_payment_mora ? this.payment_mora : 0;
              fetch(`<?php echo $_ENV['API_PATH'] ?>/credits/${this.data.id}/pay`, {
                  method: 'post',
                  headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                    amount,
                    penalty,
                    payment_method_type: this.payment_method.type,
                    payment_method_date: this.payment_method.date,
                    payment_method_reference: this.payment_method.reference,
                    payment_method_account: this.payment_method.account,
                    user_id: <?php echo $request->user()->idU ?>
                  })
                })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    swal({
                      text: 'Cobro realizado con exito!!',
                      icon: 'success'
                    });
                    showBoucher(data.transaction_id);
                    this.resetData();
                  } else {
                    swal({
                      text: data.message,
                      icon: 'error'
                    });
                  }
                });
            }
          });
      },
      nivelarPago() {
        if (!this.data) {
          return;
        }

        var total = this.data.installments.reduce((total, item) => {
          if (this.data.now_date >= item.expiration_at) {
            return total + item.capital_debt + item.interest_debt;
          }

          return total;
        }, 0);

        var mora = this.data.installments.reduce((total, item) => {
          if (item.penalty_debt > 0) {
            return total + 1;
          }

          return total;
        }, 0);

        this.payment_amount = total / 10;
        this.payment_mora = mora;
        this.payment_by_installment = false;
        this.active_payment_mora = mora > 0 ? true : false;
      },
      handleSubmitCreateJustifyNonPayment() {
        if (this.justifyNonPayment.id) {
          fetch(`./../app/api/updateJustification.php`, {
              method: 'post',
              body: JSON.stringify({
                id: this.justifyNonPayment.id,
                description: this.justifyNonPayment.description
              }),
              headers: {
                'Content-Type': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {

              if (data.success) {
                swal({
                    title: 'Actualizado con éxito!!',
                    icon: 'success'
                  })
                  .then(value => {
                    window.location.reload();
                  });
              } else {
                swal({
                  title: data.message,
                  icon: 'error'
                });
              }

            });
        } else {
          fetch(`../app/api/justifyNonPayment.php`, {
              method: 'post',
              body: JSON.stringify({
                credit_id: this.justifyNonPayment.credit_id,
                description: this.justifyNonPayment.description
              }),
              headers: {
                'Content-Type': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                swal({
                    title: 'Registrado con exito!!',
                    icon: 'success'
                  })
                  .then(_ => {
                    window.location.reload();
                  });
              } else {
                swal({
                  title: data.message,
                  icon: 'error'
                });
              }
            });
        }

      },
      deleteJustificationNonPayment() {
        swal({
            title: '¿Seguro que desea eliminar la justificación?',
            icon: 'info',
            buttons: ['Cerrar', 'Si, eliminar']
          })
          .then(data => {

            if (data) {
              fetch(`./../app/api/deleteJustification.php?nonPaymentJustificationId=${this.justifyNonPayment.id}`)
                .then(response => response.json())
                .then(data => {

                  if (data.success) {
                    swal({
                        title: 'Eliminado con exito!!',
                        icon: 'success'
                      })
                      .then(value => {
                        window.location.reload();
                      });
                  } else {
                    swal({
                      title: data.message,
                      icon: 'error'
                    });
                  }

                });
            }

          });
      }
    }));
  });

  const format = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2
  });

  function numberFormat(number) {
    return format.format(number)
  }
</script>

<!-- <script src="//unpkg.com/alpinejs" defer></script> -->
<script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>
<script type="text/javascript" src="js/VentanaCentrada.js"></script>

<?php
include("footer.php");
?>