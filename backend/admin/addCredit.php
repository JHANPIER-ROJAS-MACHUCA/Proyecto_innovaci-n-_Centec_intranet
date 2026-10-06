<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

include('head.php');

$request = new Request();

$customer = Customer::with('riskProfile', 'portfolio', 'attachments', 'relations')
    ->find($request->customerId);

if (!$customer) {
    echo "";
    die();
}

$creditsActives = Credit::where('idCG', $request->customerId)->where('estado', 4)->get();
$allCredits = Credit::where('idCG', $request->customerId)
    ->whereIn('estado', [4, 5])
    ->orderBy('idP', 'desc')
    ->limit('20')
    ->get();

$creditTypes = $database->table('credit_types')->get();

?>

<div class="container">
    <div>
        <label>Tipo de prestamo</label>
        <select class="form-control">
            <option value="" selected disabled>Seleccione</option>
            <?php foreach ($creditTypes as $key => $type) { ?>
                <option><?php echo $type->name ?></option>
            <?php } ?>
        </select>
    </div>
    <div>
        <label>Monto propuesto</label>
        <input type="number" class="form-control" />
    </div>
    <div>
        <label>Tasa</label>
        <input type="number" class="form-control" />
    </div>
    <div>
        <label>Mora por día</label>
        <input type="number" class="form-control" />
    </div>
    <div>
        <label>Días de pago</label>
        <input type="number" class="form-control" />
    </div>
    <div>
        <label>Número de cuotas</label>
        <input type="number" class="form-control" />
    </div>
    <div>
        <label>Funcionario</label>
        <select class="form-control">
            <option value="" selected disabled>Seleccione</option>
            <?php foreach (User::whereIn('tipoU', [3, 4])->where('estadoU', 1)->get() as $key => $user) { ?>
                <option value="<?php echo $user->idU ?>"><?php echo $user->apU ?></option>
            <?php } ?>
        </select>
    </div>
    <div>
        <label>
            <input type="checkbox" />
            <span>Con días de gracia</span>
        </label>
    </div>
    <div>
        <label>Fecha primera cuota</label>
        <input type="date" class="form-control" />
    </div>
    <div>
        <label>Días de gracia</label>
        <input type="number" class="form-control" />
    </div>
</div>

<?php include('footer.php') ?>