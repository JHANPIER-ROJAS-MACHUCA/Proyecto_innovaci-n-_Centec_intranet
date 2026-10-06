<?php

use CrediSoporte\Domain\Models\User;

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

$justifications = $database->table('non_payment_justifications')
    ->join('tprestamo', 'non_payment_justifications.credit_id', 'tprestamo.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tusuario', 'non_payment_justifications.user_id', 'tusuario.idU')
    ->orderBy('created_at', 'desc')
    ->select(
        'non_payment_justifications.*',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU'
    )
    ->get();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recorrido Justificaciones</title>
    <script src="./../public/resource/js/tailwind.js"></script>
</head>

<body>
    <div class="flex h-screen">
        <div class="flex-none w-96">
            <div class="px-3 py-2">
                <h4 class="text-lg font-semibold">Justificaciones</h4>
            </div>

            <div class="flex space-x-4">
                <div>
                    <label>Funcionario</label>
                    <select name="user" class="px-3 py-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos</option>
                        <?php foreach (User::where('estadoU', '1')->get() as $key => $item) { ?>
                            <option value="<?php echo $item->idU ?>"><?php echo $item->nomU . ' ' . $item->apU ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label>Fecha justificada</label>
                    <input type="date" class="px-3 py-2 border border-gray-300 rounded-md w-full" name="date">
                </div>
                <div class="self-end">
                    <button class="px-3 py-2 border border-gray-300 rounded-md">Aplicar filtro</button>
                </div>
            </div>

            <?php foreach ($justifications as $key => $item) { ?>
                <div class="border-4 border-red-500">
                    <div>
                        <?php echo $item->ap . ' ' . $item->am . ' ' .  $item->nom ?>
                    </div>
                    <div class="">
                        <?php echo $item->description ?>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <div class="flex-none w-6 h-6 bg-gray-300 rounded-full"></div>
                            <div class="text-xs text-gray-700"><?php echo $item->nomU . ' ' . $item->apU ?></div>
                        </div>
                        <div class="text-xs text-gray-700"><?php echo date('d/m/Y H:i', strtotime($item->created_at)) ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="bg-gray-200 flex-auto" id="elementMap">
        </div>
    </div>
</body>

</html>