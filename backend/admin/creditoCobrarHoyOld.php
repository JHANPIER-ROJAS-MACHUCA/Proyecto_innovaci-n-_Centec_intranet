<?php
include("head.php");
date_default_timezone_set('america/lima');
$f = date("d/m/Y");
$dat = preg_split("~/~", $f);
?>
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<style media="screen">
    .juve17 {
        padding-top: 5px;
    }

    .juve27 {
        text-align: center
    }
</style>

<?php

$currentDate = date('Y-m-d');
if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $currentDate = date('Y-m-d', strtotime($_GET['fecha']));
}

$lugarCobro = isset($_GET['lugar_cobro']) && !empty($_GET['lugar_cobro']) ? $_GET['lugar_cobro'] : '';
$filtroUsuario = isset($_GET['usuario']) && !empty($_GET['usuario']) ? $_GET['usuario'] : '';

$mysqli = new mysqli('localhost', 'root', 'juve7', 'drag');

/* comprobar la conexión */
if ($mysqli->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

$listaUsuariosActivos = [];
$consultaOptenerUsuario = "SELECT usuarios.idU AS id, concat(usuarios.nomU, ' ',usuarios.apU, ' ',usuarios.amU) as nombre FROM tusuario usuarios WHERE estadoU=1";
if ($resultado = $mysqli->query($consultaOptenerUsuario, MYSQLI_USE_RESULT)) {
    while ($obj = $resultado->fetch_object()) {
        array_push($listaUsuariosActivos, $obj);
    }
    $resultado->close();
}


$CODIGO_CREDITO_APROBADO = 4;
"SELECT count(*) FROM tpresta_detalle WHERE idP";
$consultaObtenerCreditosHoy = "SELECT concat(clientes.ap, ' ', clientes.am, ' ', clientes.nom) AS cliente_nombre, 
clientes.dni,
cuotas.ncuota AS numero, 
cuotas.cuota AS monto,
cuotas.montoPagado,
if(cuotas.fechaPago is null, 'NO', 'SI') AS estaCancelado,
creditos.montoAprovado,
creditos.taza,
creditos.pago,
creditos.tipoP,
creditos.n_cuota,
creditos.tlocal,
usuarios.idU AS usuario_id,
concat(usuarios.nomU, ' ',usuarios.apU, ' ',usuarios.amU) AS usuario_nombre,
@id:=cuotas.idP,
(SELECT COUNT(*) 
FROM tpresta_detalle
WHERE idP=@id
AND DATE(fechaProg)<'$currentDate'
AND (montoPagado is null OR cuota > montoPagado)) AS cuotas_retrazadas
FROM tpresta_detalle AS cuotas
INNER JOIN tprestamo AS creditos ON cuotas.idP=creditos.idP
INNER JOIN tclie_general AS clientes ON clientes.idCG=creditos.idCG
LEFT JOIN tusuario AS usuarios ON clientes.idU=usuarios.idU
WHERE DATE(cuotas.fechaProg)='$currentDate'
AND creditos.estado=$CODIGO_CREDITO_APROBADO
GROUP BY creditos.idP
ORDER BY clientes.nom
-- ORDER BY clientes.am
-- ORDER BY clientes.nom
";
$creditosCobrosHoy = [];
$montoTotalACobrar = 0;

/* Si se ha de recuperar una gran cantidad de datos se emplea MYSQLI_USE_RESULT */
if ($resultado = $mysqli->query($consultaObtenerCreditosHoy, MYSQLI_USE_RESULT)) {

    /* Observar que no se puede ejecutar ninguna función que interactue con el
       servidor hasta que el conjunto de resultados se haya cerrado. Todas las llamadas devolverán un
       error 'out of sync' */
    // if (!$mysqli->query("SET @a:='esto no funcionará'")) {
    //     printf("Error: %s\n", $mysqli->error);
    // }
    while ($obj = $resultado->fetch_object()) {
        if ($lugarCobro && $lugarCobro != $obj->tlocal) {
            continue;
        }

        if ($filtroUsuario && $filtroUsuario != $obj->usuario_id) {
            continue;
        }

        $montoTotalACobrar += floatval($obj->monto);
        array_push($creditosCobrosHoy, $obj);
        // print_r($obj);
        // $line.=$obj->uid;
        // $line.=$obj->role;
        // $line.=$obj->roleid;
    }
    $resultado->close();
}
$numeroTotalCreditosACobrar = count($creditosCobrosHoy);

// ----------------------------------------------------------
$data2 = [];
$consulta2 = "SELECT 
credits.idP,
customers.dni,
customers.nom,
customers.ap,
customers.am,
credits.montoAprovado,
credits.fechaDesembolso,
credits.n_cuota,
@creditId:=installments.idP,
sum(installments.cuota) as total,
sum(installments.montoPagado) as totalPagado,
min(installments.fechaProg) as started_at,
max(installments.fechaProg) as finished_at
FROM tpresta_detalle as installments
INNER JOIN tprestamo as credits ON installments.idP = credits.idP
INNER JOIN tclie_general as customers ON credits.idCG = customers.idCG
WHERE credits.estado=4
GROUP BY installments.idP ORDER BY installments.fechaProg";

$data2_sum_deuda = 0;
if ($resultado = $mysqli->query($consulta2, MYSQLI_USE_RESULT)) {
    while ($obj = $resultado->fetch_object()) {
        $inicio = new DateTime($obj->finished_at);
        $hoy = new DateTime($currentDate);
        $intvl = $inicio->diff($hoy);

        $t = strtotime($obj->finished_at) < strtotime($currentDate);
        $d = strtotime($currentDate) - strtotime($obj->finished_at);

        if ($obj->total != $obj->totalPagado && $t && $intvl->days < 60) {
            $data2_sum_deuda += floatval($obj->total) - floatval($obj->totalPagado);
            $obj->tiempoPasado = $intvl->days;
            array_push($data2, $obj);
        }

    }
    $resultado->close();
}
// ----------------------------------------------------------

$mysqli->close();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
        </div>

        <h5 style="color:white">Cobro de Crédito<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <div style="display: flex;margin-bottom: 30px;">
            <div>
                <h3 style="font-weight: bold;">CREDITOS A COBRAR</h3>
                <div>
                    <form style="display: inline;" action="creditoCobrarHoy.php" method="get">
                        <label>FECHA</label>
                        <input name="fecha" type="date" value="<?php echo $currentDate; ?>">
                        <label>LUGAR DE COBRO</label>
                        <select name="lugar_cobro">
                            <option value="" <?php echo $lugarCobro == '' ? 'selected' : '' ?>>Todos</option>
                            <option value="2" <?php echo $lugarCobro == '2' ? 'selected' : '' ?>>Oficina</option>
                            <option value="1" <?php echo $lugarCobro == '1' ? 'selected' : '' ?>>Campo</option>
                        </select>
                        <label>USUARIO</label>
                        <select name="usuario">
                            <option value="" <?php echo $lugarCobro == '' ? 'selected' : '' ?>>Todos</option>
                            <?php
                            foreach ($listaUsuariosActivos as $usuario) {
                            ?>
                                <option value="<?php echo $usuario->id; ?>" <?php echo $filtroUsuario == $usuario->id ? 'selected' : ''; ?>><?php echo $usuario->nombre; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                </div>
            </div>
            <div style="margin-right: 10px; margin-left: auto;">
                <label>TOTAL CREDITOS</label>
                <input type="text" class="form-control" value="<?php echo $numeroTotalCreditosACobrar; ?>" disabled>
            </div>
            <div>
                <label>MONTO TOTAL A COBRAR</label>
                <input type="text" class="form-control" value="<?php echo number_format($montoTotalACobrar, 2); ?>" disabled>
            </div>
        </div>
        <table id="datatableCreditoCobrarHoy" class="table table-striped">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Cliente</th>
                    <th>Monto aprobado</th>
                    <th>Taza</th>
                    <th>T. Pago</th>
                    <th>Periodo</th>
                    <th>Tipo</th>
                    <th>Lugar cobro</th>
                    <th>Cuotas retrazadas</th>
                    <th>Usuario</th>
                    <th>Monto</th>
                    <th>M.P</th>
                    <th>M.F</th>
                    <th>¿Pago?</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($creditosCobrosHoy as $credito) {

                    $classTr = "";
                    if ($credito->monto - $credito->montoPagado == 0) {
                        $classTr = "background: #E8F5E9;color:#2E7D32;";
                    } else {
                        $classTr = "background: #FFEBEE;color:#D50000;";
                    }
                ?>

                    <tr style="<?php echo $classTr; ?>">
                        <td><?php echo $credito->dni; ?></td>
                        <td><?php echo $credito->cliente_nombre; ?></td>
                        <td><?php echo $credito->montoAprovado; ?></td>
                        <td><?php echo $credito->taza . '%'; ?></td>
                        <td>
                            <?php
                            switch ($credito->pago) {
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
                                case 5:
                                    echo "QUINCENAL";
                                    break;
                            }
                            ?>
                        </td>
                        <td><?php echo $credito->n_cuota; ?></td>
                        <td>
                            <?php
                            switch ($credito->tipoP) {
                                case '1':
                                    echo 'Transporte';
                                    break;
                                case '2':
                                    echo 'Comercio';
                                    break;
                                case '3':
                                    echo 'Prendario';
                                    break;
                                case '4':
                                    echo 'Servicio';
                                    break;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($credito->tlocal == 1) {
                                echo 'CAMPO';
                            } else if ($credito->tlocal == 2) {
                                echo 'OFICINA';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($credito->cuotas_retrazadas > 0) {
                                echo $credito->cuotas_retrazadas . ' cuota(s) retrazada(s).';
                            }
                            ?>
                        </td>
                        <td><?php echo $credito->usuario_nombre; ?></td>
                        <td><?php echo $credito->monto; ?></td>
                        <td><?php echo $credito->montoPagado ?? 0; ?></td>
                        <td><?php echo $credito->monto - $credito->montoPagado; ?></td>
                        <td><?php
                            if ($credito->monto - $credito->montoPagado == 0) {
                                echo "SI";
                                // echo "<span style='display: inline-block; background: green; color:white; padding: 2px 10px'>SI<span>";
                            } else {
                                echo "NO";
                                // echo "<span style='display: inline-block; background: red; color:white; padding: 2px 10px'>NO<span>";
                            }
                            ?></td>
                    </tr>

                <?php
                }
                ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: end;margin-bottom: 10px;">
            <div>
                <label>Total a cobrar</label>
                <input class="form-control" style="width: 200px; color: red" type="text" value="<?php echo number_format($data2_sum_deuda,2); ?>" disabled />
            </div>
        </div>
        <table id="table2" class="table table-striped">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Cliente</th>
                    <th>Desembolso</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Tiempo pasado</th>
                    <th>Total</th>
                    <th>Total pagado</th>
                    <th>Deuda</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data2 as $data) {
                ?>
                    <tr>
                        <td><?php echo $data->dni; ?></td>
                        <td><?php echo $data->ap . ' ' . $data->am . ' ' . $data->nom; ?></td>
                        <td><?php echo $data->fechaDesembolso ?></td>
                        <td><?php echo $data->started_at ?></td>
                        <td><?php echo $data->finished_at ?></td>
                        <td><?php echo $data->tiempoPasado . ' dias' ?></td>
                        <td><?php echo $data->total ?></td>
                        <td><?php echo floatval($data->totalPagado) ?></td>
                        <td><?php echo number_format(floatval($data->total) - floatval($data->totalPagado), 2) ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php
include("footer.php");
?>
<script src="functiones/cobro.js"></script>
<script src="../js/plugins/dataTables/datatables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#datatableCreditoCobrarHoy').DataTable({
            pageLength: 15,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                /*{ extend: 'copy'},
                {extend: 'csv'},*/
                {
                    extend: 'excel',
                    title: 'Cobros '
                },
                /*{extend: 'pdf', title: 'ExampleFile'},*/
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });

        $('#table2').DataTable({
            pageLength: 15,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                /*{ extend: 'copy'},
                {extend: 'csv'},*/
                {
                    extend: 'excel',
                    title: 'Cobros '
                },
                /*{extend: 'pdf', title: 'ExampleFile'},*/
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });

    });
</script>