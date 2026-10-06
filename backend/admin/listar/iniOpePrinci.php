<?php

use CrediSoporte\Domain\Request\Request;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request = new Request();

if (!$request->fechi || empty($_COOKIE['user1'])) {
  die();
}

$cash = $database->table('tcaja_oficina')
  ->where('idO', 'CG')
  ->whereNull('montof')
  ->first();
?>

<?php if (is_null($cash)) { ?>
  <div class="form-group row">
    <a class="btn btn-sm btn-info" onclick="iniciaB()"><i class="fa fa-legal"></i>Empezar</a>
  </div>
<?php } else { ?>
  <div class="form-group row" id="iOPEHISTO" style="height: 250px;overflow: auto">
    <!--<h1>Empezemos</h1>-->
    <input type="hidden" placeholder="dd/mm/aaaa" name="fecha" id="fecha" style="text-align:center" value="<?php echo $cash->ini ?>">
    <div id="iniciarG"></div>
  </div>
<?php } ?>


<script type="text/javascript" src="./functiones/IniGerente.js"></script>