<?php

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Session\Session;
use CrediSoporte\Domain\Models\Transaction;

session_start();

$request = new Request();
$session = new Session();

if (is_null($request->user())) {
    header('Location: ../');
    exit();
}

include('conection/bdcredito.php');

$idU = $_COOKIE['user1'];
//USUARIO
$tipo = $request->user()->tipoU;
$idO = $request->user()->idO;

switch ($tipo) {
    case '1':
        $tipoU = "GERENTE";
        break;
    case '2':
        $tipoU = "ADMINISTRADOR";
        break;
    case '3':
        $tipoU = "OPERADOR";
        break;
    case '4':
        $tipoU = "ASESOR";
        break;
    case '5':
        $tipoU = "JEFE DE OPERACIONES";
        break;
    default:
        $tipoU = "&";
        break;
}

$cash = $database::table('tcaja_usuario')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('tcaja_usuario.idU', $request->user()->idU)
    ->where('tcaja_oficina.idO', $request->user()->idO)
    ->whereNull('tcaja_usuario.montofin')
    ->whereNull('tcaja_oficina.montof')
    ->first();

// la caja de usuarios normales de verifica de otra manera
if ($cash && $cash->tipo != 1 && !$cash->montoIni && !$cash->hini) {
    $cash = null;
}

$totalCash = 0;
$totalDigital = 0;

if ($cash) {
    if ($cash->tipo == 1) { // caja administracion tiene otros ingresos y egresos
        $otrasCajas = $database->table('tcaja_usuario')
            ->where('idCO', $cash->idCO)
            ->where('idCA', '!=', $cash->idCA)
            ->sum('montoFin');

        $designacionTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
            ->where('tcaja_usuario.idCO', $cash->idCO)
            ->where('tcaja_usu_detal.tipo', '1')
            ->where('tcaja_usu_detal.habilitacion', '4')
            ->sum('tcaja_usu_detal.monto');

        $totalCash += $cash->monto + $otrasCajas - $designacionTransactions;
    }

    $asignacionTransactions = Transaction::where('idCA', $cash->idCA)
        ->where('tipo', 1)
        ->where('estadodt', 2)
        ->where('habilitacion', 4)
        ->sum('monto');

    $cobrosTransactions = Transaction::leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 3)
        ->selectRaw('sum(if(transaction_details.id, tcaja_usu_detal.total,0)) as digital')
        ->selectRaw('sum(if(transaction_details.id, 0,tcaja_usu_detal.total)) as cash')
        ->first();

    $ahorroTransactions = Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->selectRaw('sum(if(tahorro_deta.tipo = 7, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_deta.tipo = 8, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $incomeAndExpense = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
        ->whereNull('tcaja_usu_detal.idCuota')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 1, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 2, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $desembolsoTransactions = Transaction::where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 2)
        ->sum('total');

    $totalCash += $asignacionTransactions +
        $cobrosTransactions->cash +
        $ahorroTransactions->income -
        $ahorroTransactions->expense +
        $incomeAndExpense->income -
        $incomeAndExpense->expense -
        $desembolsoTransactions;

    $totalDigital = $cobrosTransactions->digital;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $jua1['titulo']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link href="../public/resource/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/resource/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../public/resource/css/animate.css" rel="stylesheet">
    <link href="../public/resource/css/style.css" rel="stylesheet">
    <!--estilo creditos-->
    <link href="../public/resource/css/estilobdcreditos.css" rel="stylesheet">
    <!--fin css-->
    <link href="extra/neo.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="titulo/img/<?php echo $jua1['logo']; ?>" />
    <!-- probando -->
    <script src="jquery/jquery.min.js"></script>
    <script>window.APISPERU_TOKEN=<?php echo json_encode($_ENV['APISPERU_TOKEN'] ?? ''); ?>;</script>
    <script src="js/xeroz.js"></script>
    <link href="../public/resource/css/plugins/select2/select2.min.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="../fonts/glyphicons-halflings-regular.woff">-->
    <!--profile word-->
    <!-- <link href="../css/plugins/summernote/summernote.css" rel="stylesheet">
    <link href="../css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
    <link href="../css/plugins/datapicker/datepicker3.css" rel="stylesheet"> -->
    <script type="text/javascript">
        function actualizarPag() {
            location.reload();
        }
        //Función para actualizar cada 4 segundos(4000 milisegundos)
    </script>

    <style>
        table.table thead tr th {
            background: #2E86C1;
            color: white;
            border-left: 1px solid #2874A6;
        }

        table.table thead tr th:first-child {
            border-left-color: transparent;
        }
    </style>
</head>

<body class="">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span>
                                <a href="userImg.php" style="display: inline-block; width: 75px; height: 75px; background: white; border-radius: 50%;">
                                    <?php if ($request->user()->img) { ?>
                                        <img style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" alt="foto de perfil" src="../storage/profiles/<?php echo $request->user()->img ?>" />
                                    <?php } ?>
                                </a>
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $request->user()->apU . ' ' . $request->user()->amU . ' ' . $request->user()->nomU; ?></strong>
                                    </span> <span class="text-muted text-xs block"><?php echo "$tipoU"; ?> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="userModificar.php">Modificar</a></li>
                                <li><a href="userPassword.php">Cambiar Password</a></li>

                                <li class="divider"></li>
                                <li><a href="extra/sesion.php" id="Csession" accesskey="x"><i class="fa fa-sign-out" style="color:red"></i> <span class="nav-label">Cerrar Sesión</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="inicio.php"><i class="fa fa-th-large"></i> <span class="nav-label">CPANEL</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-user-circle"></i> <span class="nav-label">CLIENTES</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a href="clientes.php">Clientes</a></li>
                            <li><a href="resumenCreditosDelCliente.php">Posición del cliente</a></li>
                        </ul>
                    </li>
                    <?php //}
                    /*verificamos si abrio su caja como GERENTE,admin y usuario final*/
                    $consulta = "";
                    $juvenalF = "";
                    if (
                        $request->user()->tipoU == '1' ||
                        $request->user()->tipoU == '5' ||
                        $request->user()->tipoU == '7'
                    ) {
                        $consulta = extraer("SELECT if(fin is null,0,1) as resul FROM tcaja_oficina where idO='CG' order by idCO desc limit 1");
                    } //admin
                    else {
                        $ofiidentici = $_COOKIE['tofi'];
                        $infoa1 = extraer(" SELECT idCO FROM tcaja_oficina where idO='$ofiidentici' order by idCO desc limit 1");
                        $reax = mysqli_fetch_array($infoa1);
                        $idas = $reax['idCO'];
                        if ($_COOKIE['tuser'] == '2') {
                            $consulta = extraer("SELECT if(count(*)=0,1,0) as resul FROM tcaja_oficina where idCO='$idas' and ini is not null and fin is null");
                        } else {
                            $usuasio = $_COOKIE['user1'];
                            $consulta = extraer("SELECT if(count(*)=0,1,0) as resul FROM tcaja_usuario where idU='$usuasio' and idCO='$idas' and montoIni is not null and montofin is null");
                        }
                    }
                    $final7 = mysqli_fetch_array($consulta);
                    $limitadorfinal = $final7['resul'];
                    if ($limitadorfinal == 0) {
                    ?>
                        <li id="idCredito">
                            <a href="#"><i class="fa fa-money"></i> <span class="nav-label">COBRAR</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="creditoCobrar.php">Cobrar Crédito</a></li>
                                <li><a href="agregarAhorro.php">Ahorros</a></li>
                                <li><a target="_blank" href="cobroMobile.php">Campo</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class=" fa fa-empire"></i> <span class="nav-label">CAJA</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <?php if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '7') { ?>
                                    <li id="CajaBode"><a href="agreBobeda.php">Caja Bodega</a></li>
                                <?php } ?>
                                <li><a href="iniOperaciones.php">Iniciar Operaciones</a></li>
                                <?php if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '2' || $_COOKIE['tuser'] == '3') { ?>
                                <li><a href="soliGastos.php">Recibo de Egresos</a></li>
                                <?php } ?>
                                <li>
                                    <a href="solirecibo.php">Recibo de Ingresos</a>
                                    <?php
                                    if (!empty($idO)) {
                                        $data = $database->table('tbilletaje')
                                            ->join('tusuario', 'tbilletaje.idU', 'tusuario.idU')
                                            ->where('tbilletaje.estado', 2)
                                            ->where('tusuario.idO', $idO)
                                            ->count();
                                    }

                                    if (
                                        $request->user()->tipoU == 1 ||
                                        $request->user()->tipoU == 2 ||
                                        $request->user()->tipoU == 7
                                    ) {
                                    ?>
                                <li>
                                    <a href="confirmarBilletaje.php">
                                        Confirmar Billetaje
                                        <span class="label label-info">
                                            <?php if (!empty($idO)) {
                                                echo $data;
                                            } ?>
                                        </span>
                                    </a>
                                </li>
                            <?php } ?>

                            <li><a href="arqueoCaja.php">Movimientos de caja</a></li>
                            <li><a href="reporteUsuarioCobro.php">Cobros</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-credit-card"></i> <span class="nav-label">PRESTAMOS</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="creditosDesembolsos.php">Desembolsos</a></li>
                                <li><a href="creditosActivos.php">Activos</a></li>
                                <li><a href="creditosFinalizados.php">Finalizados</a></li>
                                <li><a href="carterasVencidas.php">Carteras vencidas</a></li>
                                <li><a href="creditosFinalizadosActivos.php">Finalizados y activos</a></li>
                                <li><a href="reporteCobroCreditos.php">Cobros por fecha</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#"><i class=" fa fa-slack"></i> <span class="nav-label">INSTRUMENTOS DE CONTROL</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="metasPorUsuario.php">Monitor de seguimiento de metas</a></li>
                                <li><a href="<?php echo $_ENV['SERVER2'] . '/portfolios/goals' ?>">Resumen metas</a></li>
                                <li><a href="reporteDesembolsosTodos.php">Desembolsos</a></li>
                                <li><a href="./proyecciones.php">Poryecciones</a></li>
                                <!-- <li><a href="./reporteDeudores.php">Deudores</a></li> -->
                            </ul>
                        </li>

                        <li>
                            <a href="#"><i class=" fa fa-bar-chart-o"></i> <span class="nav-label">ADMINISTRACIÓN</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <?php
                                if (
                                    $request->user()->tipoU == '1' ||
                                    $request->user()->tipoU == '2'
                                ) {
                                ?>
                                    <li><a href="<?php echo $_ENV['SERVER2'] . '/condone_delays' ?>">Condonación de Mora<span class="label label-info"></span></a></li>
                                    <li><a href="<?php echo $_ENV['SERVER2'] . '/portfolios' ?>">Administrar Cartera<span class="label label-info"></span></a></li>
                                    <!-- <li><a href="reporteMoras.php">Reporte Moras</a></li> -->
                                    <li><a target="_blank" href="<?php echo $_ENV['SERVER2'] . '/map/justifications' ?>">Ver justificaciones<span class="label label-info"></span></a></li>
                                <?php } ?>

                                <li>
                                    <a href="PrestamoReporte.php">
                                        Prestamos
                                        <span class="label label-info">
                                            <?php if (!empty($idO)) {
                                                echo $database->table('tprestamo')
                                                    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
                                                    ->join('tusuario', 'tclie_general.idU', 'tusuario.idU')
                                                    ->where('tclie_general.idO', $idO)
                                                    ->where('tprestamo.estado', 1)
                                                    ->count();
                                            } ?>
                                        </span>
                                    </a>
                                </li>
                                <?php //} 
                                ?>
                                <li><a href="soliExtorno.php">Solicitar Extorno</a></li>
                                <li><a href="reporteCreditosConRelaciones.php">Reporte sentinel</a></li>
                                <!--motivo-->
                            </ul>
                        </li>

                        <li>
                            <a href="#"><i class="fa fa-line-chart"></i> <span class="nav-label">REPORTES</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <?php
                                if (
                                    $request->user()->tipoU == '1' ||
                                    $request->user()->tipoU == '7' ||
                                    $request->user()->tipoU == '5'
                                ) {
                                ?>
                                    <li><a href="deposito_rep.php">Depositos y ahorros</a></li>
                                    <!--motivo-->
                                    <!-- <li><a href="reporteDeudores.php">Reporte de Deudores</a></li>
                                    <li><a href="reporteMoras.php">Reporte Total de Moras</a></li> -->
                                    <!--<li><a href="reporteMorasPorDias.php">Reporte de moras por d&iacute;as de atrazo</a></li>-->
                                    <!--<li><a href="reporteMoras180.php">Cartera Morosa</a></li>-->
                                    <li><a href="reporteUsuarioCobro.php">Cobros del día</a></li>

                                    <li><a href="cierremes.php">Reporte de Cierre de Mes</a></li>
                                    <li><a href="cierremes1.php">Reporte de Cierre de Personalizado</a></li>
                                    <li><a href="./metasPorUsuario.php">Metas</a></li>
                                <?php } ?>

                                <li><a href="creditoCobrarHoy.php">Cartera de cobro</a></li>

                                <?php if ($request->user()->tipoU == '3' || $request->user()->tipoU == '4') { ?>
                                    <li><a href="metasPorUsuario.php">Avance de metas</a></li>
                                <?php } ?>
                            </ul>
                        </li>

                        <?php
                        if (
                            $request->user()->tipoU == '1' ||
                            $request->user()->tipoU == '7' ||
                            $request->user()->tipoU == '5'
                        ) {
                        ?>
                            <li>
                                <a href="#"><i class="fa fa-database"></i> <span class="nav-label">SERVICIOS</span><span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="extorno.php">Extornos</a></li>
                                    <li><a href="cobrosEliminados.php">Extornos eliminados</a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <?php if ($request->user()->tipoU == '1' || $request->user()->tipoU == '7') { ?>
                            <li id="cfig">
                                <a href=""><i class="fa fa-cog"></i> <span class="nav-label">CONFIGURACIÓN</span><span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li id="usariosSistema"><a href="agregarUsuarios.php">Usuarios del Sistema</a></li>
                                    <li id="oficinasctcp"><a href="agregarOficinas.php">Oficinas</a></li>
                                    <li id="depositosctcp"><a href="depositos.php">Depositos</a></li>
                                    <li id="operaciones"><a href="operacion.php">Operaciones</a></li>
                                    <li id="empresa"><a href="empresa.php">Empresa</a></li>
                                    <li><a href="metas.php">Metas</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>

                    <!-- <?php if ($request->user()->tipoU == '3' || $request->user()->tipoU == '4') { ?>
                        <li>
                            <a href="#"><i class="fa fa-line-chart"></i> <span class="nav-label">REPORTES</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="creditoCobrarHoy.php">Cobros del día</a></li>
                                <li><a href="metasPorUsuario.php">Avance de metas</a></li>
                            </ul>
                        </li>
                    <?php } ?> -->

                    <!-- <li>
                        <a href="evaluacion1.php"><i class=" fa fa-empire"></i> <span class="nav-label">EVALUACIÓN</span></a>
                    </li> -->

                    <li>
                        <a href="#"><i class="fa fa-lightbulb-o"></i> <span class="nav-label">PROPUESTA</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a href="propuestas.php">Propuestas</a></li>
                            <?php if ($request->user()->tipoU == '3' || $request->user()->tipoU == '4') { ?>
                                <li><a href="propuesta.php" target="_blank">Crear propuesta</a></li>
                            <?php } ?>
                        </ul>
                    </li>

                    <li>
                        <a href="seguimientoDeMora.php"><i class="fa fa-download"></i> <span class="nav-label">SEGUIMIENTO DE MORA</span></a>
                    </li>

                    <li>
                        <a href="formatos.php"><i class="fa fa-download"></i> <span class="nav-label">FORMATOS</span></a>
                    </li>

                    <li>
                        <a href="extra/sesion.php" id="Csession" accesskey="x">
                            <i class="fa fa-sign-out" style="color:red"></i>
                            <span class="nav-label">Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0; display: flex; justify-content: between;width: 100%;">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <div class="nav navbar-top-links navbar-left" style="flex: auto; display: flex; justify-content: center;">
                        <?php if ($request->user()->tipoU != 1) { ?>
                            <div style="height: 50px;display: flex; align-items: center; font-size: 16px; font-weight: 600;">
                                <?php if ($request->user()->tipoU == 2 && !is_null($cash)) { ?>
                                    <span style="color: blue; font-weight: bold;margin-right: 10px;">CAJA ABIERTA DE</span>
                                    <span style="margin-right: 20px;"><?php echo date('d/m/Y', strtotime($cash->ini)) . ' ' . $cash->iniH ?></span>
                                    <span style="margin-right: 10px; color: blue">EFECTIVO</span>
                                    <span style="font-weight: bold; margin-right: 10px;">S/ <?php echo number_format($totalCash, 2) ?></span>
                                    <span style="margin-right: 10px; color: blue">DIGITAL</span>
                                    <span style="font-weight: bold;">S/ <?php echo number_format($totalDigital, 2) ?></span>
                                <?php } else if (!is_null($cash)) { ?>
                                    <span style="color: blue; font-weight: bold;margin-right: 10px;">CAJA ABIERTA DE</span>
                                    <span style="margin-right: 20px;"><?php echo date('d/m/Y', strtotime($cash->ini)) . ' ' . $cash->hini ?></span>
                                    <span style="margin-right: 10px; color: blue">EFECTIVO</span>
                                    <span style="font-weight: bold; margin-right: 10px;">S/ <?php echo number_format($totalCash, 2) ?></span>
                                    <span style="margin-right: 10px; color: blue">DIGITAL</span>
                                    <span style="font-weight: bold;">S/ <?php echo number_format($totalDigital, 2) ?></span>
                                <?php } else {
                                    echo 'CAJA CERRADA';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown" id="notiJuve">
                        </li>
                        <a type="button" class="btn btn-xs btn-default" onclick="actualizarPag()"><span class="fa fa-retweet"></span></a>
                        <li>
                            <a href="extra/sesion.php" accesskey="x">
                                <i class="fa fa-sign-out" style="color:red"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="wrapper wrapper-content">