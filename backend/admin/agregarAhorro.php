<?php

use CrediSoporte\Domain\Models\Customer;

include('head.php');

$customers = Customer::all();

$ahorros = $database->table('tahorro')
  ->select(
    'tahorro.idA',
    'tclie_general.idCG',
    'tclie_general.ap',
    'tclie_general.am',
    'tclie_general.nom'
  )
  ->selectRaw('(select sum(if(tahorro_deta.tipo = 7,tahorro_deta.monto,0)) - sum(if(tahorro_deta.tipo = 8,tahorro_deta.monto,0)) as saldo from tahorro_deta where tahorro_deta.idA = tahorro.idA and estad=1) as saldo') // saldo
  ->selectRaw('(select concat_ws("|", monto,tipo,fecha) from tahorro_deta where idA=tahorro.idA and estad=1 order by idAd desc limit 1) as other_data') // la ultima transaccion
  ->join('tclie_general', 'tahorro.id', 'tclie_general.idCG')
  ->where('tahorro.tipoA', 1)
  ->orderBy('tahorro.idA', 'desc')
  ->get();

foreach ($ahorros as $ahorro) {
  $datos = explode('|', $ahorro->other_data);
  if (count($datos) != 3) {
    $datos[0] = '';
    $datos[1] = '';
    $datos[2] = '';
  }

  switch ($datos[1]) {
    case '1':
      $movimiento = 'Depósito';
      break;

    case '7':
      $movimiento = 'Ahorro';
      break;

    case '8':
      $movimiento = 'Retiro';
      break;

    case '4':
      $movimiento = 'Descuento';
      break;

    case '5':
      $movimiento = 'Adelanto';
      break;

    default:
      $movimiento = '';
      break;
  }

  $ahorro->monto = $datos[0] > 0 ? floatval($datos[0]) : 0;
  $ahorro->movimiento = $movimiento;
  $ahorro->fecha = $datos[2] ? date('d/m/Y', strtotime($datos[2])) : '';
}

?>
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>
    <h5 style="color:white">Reporte de Ahorro<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body" x-data="ahorros">

    <div class="form-group row">
      <label for="q" class="col-md-1 control-label">Tipo</label>
      <div class="col-md-2">
        <select class="form-control" name="" id="listaTipo">
          <option value="1">Clientes</option>
          <option value="2">Usuarios</option>
        </select>
      </div>
      <label class="col-md-1 control-label" id="tipoCambio"></label>
      <div class="col-md-5" id="lista1" style="padding-bottom:10px">
        <select class="form-control select2_demo_3" x-model="customer_id" id="listaAho1">
          <option value="" selected>--Seleccione--</option>
          <?php foreach ($customers as $customer) { ?>
            <option value="<?php echo $customer->idCG ?>"><?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-3">
        <button title="Buscar Cliente" type="button" class="btn btn-info" style="height: 29px" @click='searchAhorro'>
          <i class="glyphicon glyphicon-search"></i>
        </button>
        &nbsp;
        <a title="Agregar Ahorro" data-toggle="tab" class="btn btn-primary" style="height: 29px" @click='registerAhorro'>
          <i class="fa fa-plus-circle"></i>
        </a>
      </div>

      <div class="col-md-3">

        <span id="loader"></span>
      </div>

    </div>
    <!--</form>-->

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
      <template x-for="ahorro in filtered" :key="ahorro.idA">
        <a :href="'./detalleAhorro.php?customerId=' + ahorro.idCG" style="display: inline-block; border: 1px solid #e7eaec; padding: 20px; color: #2C3E50;">
          <h4 style="text-align: center;" x-text="ahorro.ap + ' ' + ahorro.am + ' '+ ahorro.nom"></h4>
          <div><span style="font-weight: bold;">Saldo:</span> S/. <span x-text="ahorro.saldo"></span></div>
          <div>
            <span style="font-weight: bold;">Movimiento</span>
            <span x-text="ahorro.movimiento"></span>
          </div>
          <div>
            <span style="font-weight: bold;">Monto:</span>
            <span x-text="ahorro.monto > 0 ? 'S/.' : ''"></span>
            <span x-text="ahorro.monto"></span>
          </div>
          <div>
            <span style="font-weight: bold;">Fecha</span> <span x-text="ahorro.fecha"></span>
          </div>
        </a>
      </template>
      <?php //foreach ($ahorros as $ahorro) { 
      ?>
      <!-- <a href="./detalleAhorro.php?customerId=<?php echo $ahorro->idCG ?>" style="display: inline-block; border: 1px solid #e7eaec; padding: 20px; color: #2C3E50;">
          <h4 style="text-align: center;"><?php echo $ahorro->ap . ' ' . $ahorro->am . ' ' . $ahorro->nom ?></h4>
          <div><span style="font-weight: bold;">Saldo:</span> S/. <?php echo $ahorro->saldo ?? 0 ?></div>
          <div>
            <span style="font-weight: bold;">Movimiento</span>
            <?php
            $datos = explode('|', $ahorro->other_data);
            if (count($datos) != 3) {
              $datos[0] = '';
              $datos[1] = '';
              $datos[2] = '';
            }

            switch ($datos[1]) {
              case '1':
                echo 'Depósito';
                break;

              case '7':
                echo 'Ahorro';
                break;

              case '8':
                echo 'Retiro';
                break;

              case '4':
                echo 'Descuento';
                break;

              case '5':
                echo 'Adelanto';
                break;

              default:
                # code...
                break;
            }
            ?>
          </div>
          <div>
            <span style="font-weight: bold;">Monto:</span> <?php echo $datos[0] > 0 ? 'S/. ' . $datos[0] : '' ?>
          </div>
          <div>
            <span style="font-weight: bold;">Fecha</span> <?php echo $datos[2] ? date('d/m/Y', strtotime($datos[2])) : '' ?>
          </div>
        </a> -->
      <?php //} 
      ?>
    </div>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('ahorros', () => ({
        data: <?php echo $ahorros ?>,
        customer_id: '',
        get filtered() {
          if (!this.customer_id) {
            return this.data;
          }

          return this.data.filter(i => {
            return i.idCG == this.customer_id;
          });
        },
        searchAhorro() {
          const value = document.getElementById('listaAho1').value;
          this.customer_id = value;
          console.log(value);
        },
        registerAhorro() {
          const value = document.getElementById('listaAho1').value;
          if (value) {
            window.location.href = './detalleAhorro.php?customerId=' + value;
          }
        }
      }));
    });
  </script>

  <script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>

  <script type="text/javascript">
    window.addEventListener('load', function() {
      $("#listaAho1").select2({
        minimumResultsForSearch: 2,
        placeholder: "- - Seleccione - -",
        allowClear: true,
        width: '100%',
      });
    });
  </script>

  <?php include('footer.php'); ?>