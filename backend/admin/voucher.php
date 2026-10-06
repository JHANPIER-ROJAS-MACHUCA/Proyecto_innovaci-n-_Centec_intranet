<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Luecano\NumeroALetras\NumeroALetras;

$formatter = new NumeroALetras();

include('conection/bdcredito.php');

$operationId = $_GET['operacion'];

$smtp_empresa = extraer("SELECT id, logo, titulo, nombreEmpresa, abre,siglas,comentario, subnombre, color, ico, direccion FROM tdatos limit 1");
$empresa = mysqli_fetch_array($smtp_empresa);
$comentario = $empresa['comentario'];


$consulta = "SELECT
transactions.*, 
clients.idCG,
clients.nom,
clients.ap,
clients.am,
users.apU,
users.amU,
users.nomU
FROM tcaja_usu_detal as transactions
INNER JOIN tclie_general as clients ON transactions.cliente = clients.idCG
INNER JOIN tcaja_usuario as cash ON transactions.idCA = cash.idCA
INNER JOIN tusuario as users ON cash.idU = users.idU
WHERE transactions.idCAD=$operationId limit 1
";
$smtp_operation = extraer($consulta);
$transaction = mysqli_fetch_array($smtp_operation);

$idInstallments = array_filter(explode(',', $transaction['idCuota']));
$idMoras = array_filter(explode(',', $transaction['idMora']));
$idInstallmentsAndMoras = array_unique($idInstallments + $idMoras);
$idInstallmentsString = implode(',', $idInstallmentsAndMoras);

$consulta2 = "SELECT 
installments.idPD,installments.ncuota, installments.idP, credits.n_cuota
FROM tpresta_detalle AS installments
INNER JOIN tprestamo AS credits ON installments.idP=credits.idP
WHERE installments.idPD IN ($idInstallmentsString)";
$smtp_installments = extraer($consulta2);

$numCuenta = "";
$totalCuotas = "";
$numInstallmentInArray = [];
while ($installment = mysqli_fetch_array($smtp_installments)) {
    $numCuenta = str_pad($installment['idP'], 5, '0', STR_PAD_LEFT);
    $numCuota = str_pad($installment['ncuota'], 2, '0', STR_PAD_LEFT);
    $totalCuotas = str_pad($installment['n_cuota'], 2, '0', STR_PAD_LEFT);

    if (in_array($installment['idPD'], $idInstallments)) {
        array_push($numInstallmentInArray, $numCuota);
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOUCHER</title>
    <script src="tailwind.js"></script>
</head>

<body class="text-xs">
    <div class="max-w-xs mx-auto">
        <div class="text-center"><?php echo $empresa['abre'] . ' ' . $empresa['siglas'] ?> - CRÉDITOS</div>
    
        <p class="text-center"><?php echo $empresa['direccion'] ?></p>
        <h4 class="text-center mt-2 font-semibold">CREDITOS - COBRANZAS</h4>
    
        <hr class="border border-dashed border-gray-700 my-3">
    
        <div class="flex justify-between">
            <div><span class="font-semibold">Cuenta:</span> <?php echo $numCuenta   ?></div>
            <div><span class="font-semibold">Cod. Cliente:</span> <?php echo str_pad($transaction['idCG'], 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div><span class="font-semibold">Cliente:</span> <?php echo $transaction['ap'] . ' ' . $transaction['am'] . ' ' . $transaction['nom'] ?></div>
        <div class="flex justify-between">
            <div class="font-semibold">Próxima Fecha de Pago:</div>
            <div><?php echo  $transaction['next_payment'] ? date('d-m-Y', strtotime($transaction['next_payment'])) : 'NO DEFINIDO' ?></div>
        </div>
        <div class="flex justify-between">
            <div><span class="font-semibold">Cuota:</span> <?php echo implode(',', $numInstallmentInArray) ?></div>
            <div><SPAn class="font-semibold">Resta:</SPAn> <?php echo $transaction['cuotas_pendientes'] . '/' . $totalCuotas ?></div>
        </div>
    
        <hr class="border border-dashed border-gray-700 my-3">
    
        <div class="flex justify-between">
            <div class="font-semibold">SUBTOTAL:</div>
            <div>S/ .<?php echo number_format($transaction['cuota'], 2) ?></div>
        </div>
        <div class="flex justify-between">
            <div class="font-semibold">MORA:</div>
            <div>S/ .<?php echo number_format($transaction['mora'], 2) ?></div>
        </div>
        <div class="flex justify-between">
            <div class="font-semibold">TOTAL PAGADO:</div>
            <div>S/ .<?php echo number_format($transaction['cuota'] + $transaction['mora'], 2) ?></div>
        </div>
    
        <div style="font-size: 9px;">SON <?php echo $formatter->toInvoice($transaction['cuota'] + $transaction['mora'], 2, "soles"); ?></div>
    
        <hr class="border border-dashed border-gray-700 my-3">
    
        <div><span class="font-semibold">Nro de Operación:</span> <?php echo str_pad($operationId, 5, '0', STR_PAD_LEFT) ?></div>
        <div><span class="font-semibold">Fecha y Hora:</span> <?php echo $transaction['created_at'] ? date('d/m/Y h:i A', strtotime($transaction['created_at'])) : 'NO DEFINIDO' ?></div>
        <div><span class="font-semibold">Usuario:</span> <?php echo $transaction['apU'] . ' ' . $transaction['amU'] . ' ' . $transaction['nomU'] ?></div>
    
        <hr class="border border-dashed border-gray-700 my-3">
    
        <div class="text-center">Consultas y Sugerencias: <?php echo '064402207' ?></div>
    
        <p class="text-center">*** SU PUNTUALIDAD ES SU MEJOR GARANTIA PARA SU PROXIMO CREDITO ***</p>
    </div>


    <script>
        window.print();
    </script>
</body>

</html>