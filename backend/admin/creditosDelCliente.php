<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

include('head.php');

$rquest = new Request();

$customer = Customer::find($request->get('customerId'));

$credits = Credit::where('idCG', $request->get('customerId'))
    ->get();

if (!$customer) {
    echo "No encontramos nada.";
    exit();
}
?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
            <a href="formato.php" class="btn btn-primary" target="_blank"><i class="fa fa-plus-circle"></i> Nuevo credito</a>
        </div>

        <h5 style="color:white">Creditos<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>

    </div>
    <div class="panel-body">
        <h3 style="font-weight: bold;">
            <span>CLIENTE: </span>
            <span style="margin-left: 5px;"><?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom  ?></span>
        </h3>

        <div class="table-responsive" style="margin-top: 20px;">
            <table class="table table-striped">
                <thead>
                    <tr style="background:<?php echo $jua1['color'] ?> ;color:white;text-align:center">
                        <th>
                            TIPO DE PRESTAMO
                        </th>
                        <th>
                            MONTO PROPUESTO
                        </th>
                        <th>
                            MONTO APROBADO
                        </th>
                        <th>
                            TAZA
                        </th>
                        <th>
                            PAGO
                        </th>
                        <th>
                            CUOTAS
                        </th>
                        <th>
                            CUOTA
                        </th>
                        <th>
                            MORA
                        </th>
                        <th>
                            ESTADO
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credits as $credit) { ?>
                        <tr>
                            <td>
                                <?php
                                switch ($credit['tipoP']) {
                                    case 1:
                                        echo "TRANSPORTE";
                                        break;
                                    case 2:
                                        echo "COMERCIO";
                                        break;
                                    case 3:
                                        echo "PRENDATARIO";
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo "S/. " . $credit['montoPropuesto']; ?></td>
                            <td><?php echo "S/. " . $credit['montoAprovado']; ?></td>
                            <td><?php echo $credit['taza'] . "%"; ?></td>
                            <td>
                                <?php
                                switch ($credit['pago']) {
                                    case 1:
                                        echo "DIARIO";
                                        break;
                                    case 2:
                                        echo "SEMANAL";
                                        break;
                                    case 3:
                                        echo "PAGO UNICO";
                                        break;
                                    case 4:
                                        echo "MENSUAL";
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo $credit['plazo']; ?></td>
                            <td><?php echo "S/. " . $credit['cuota']; ?></td>
                            <td><?php echo "S/. " . $credit['mora']; ?></td>
                            <td>
                                <?php
                                switch ($credit['estado']) {
                                    case '1':
                                        echo "PROPUESTO";
                                        break;
                                    case '2':
                                        echo "APROVADO";
                                        break;
                                    case '3':
                                        echo "DESAPROBADO";
                                        break;
                                    case '4':
                                        echo "DESEMBOLSADO";
                                        break;
                                    case '5':
                                        echo "CANCELADO";
                                        break;
                                    case '6':
                                        echo "ANULADO";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>