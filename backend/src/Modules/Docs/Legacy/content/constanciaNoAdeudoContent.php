<?php


use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

setlocale(LC_TIME, "spanish");
$request = new Request();

$customer = Customer::find($request->customerId);

if (!$customer) {
    http_response_code(404);
    die();
}

$credits = Credit::where('idCG', $request->customerId)
    ->where('estado', 4)
    ->get();

if (count($credits) > 0) {
    http_response_code(404);
    die();
}

$business = $database->table('tdatos')->first();

$mesEnEspañol = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de no adeudo</title>
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            line-height: 1.7rem;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">CONSTANCIA DE NO ADEUDO</h2>
    <p><b>CLIENTE:</b> <?php echo mb_strtoupper("$customer->ap $customer->am $customer->nom", 'UTF-8') ?></p>
    <p style="text-align: justify;">
        <b><?php echo $business->nombreEmpresa ?></b> con domicilio en
        <b><?php echo $business->direccion ?> - SATIPO - SATIPO - JUNÍN</b>.
        Dejamos constancia que el Sr.(a) <b><?php echo mb_strtoupper("$customer->ap $customer->am $customer->nom", 'UTF-8') ?></b>,
        identificada con <b>DNI N° <?php echo $customer->dni ?></b>,
        domicilio en <b><?php echo $customer->direc ?></b> es titular de la cuenta mencionada en nuesta oficina de SATIPO.
    </p>

    <p style="text-align: justify;">
        Manifiesto que, a la fecha de emisión de la presente, los créditos se encuentran cancelado,
        por tanto, el cliente no manifiesta deuda vigente con nuestra entidad financiera.
    </p>

    <p style="text-align: justify;">Se otorga el presente documento a solicitid del interesado para los fines pertinentes.</p>

    <p style="text-align: right;"><b>Satipo, <?php echo date('d') . ' de ' . $mesEnEspañol[date('n') - 1] . ' de ' . date('Y') ?></b></p>

    <p><b>Atentamente,</b></p>
</body>

</html>