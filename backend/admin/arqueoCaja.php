<?php

include('head.php');

// if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7' && $_COOKIE['tuser'] != '2') {
//     echo "<script>location.href='index.php'</script>";
// }

include '../vendor/autoload.php';
include '../src/Domain/Database/bootstrap.php';

$userId = isset($_GET['userId']) ? $_GET['userId'] : '';
$cashId = isset($_GET['cashId']) ? $_GET['cashId'] : '';

$users = [];
if ($_COOKIE['tuser'] == 3 || $_COOKIE['tuser'] == 4) {
    $users = $database::table('tusuario')
    ->where('estadoU', 1)
    ->where('idU', $_COOKIE['user1'])
    ->get();
}else{
    $users = $database::table('tusuario')
        ->where('estadoU', 1)
        ->whereIn('tipoU', [3,4])
        ->get();
}

$cashes = $database->table('tcaja_usuario')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('tcaja_usuario.idU', $userId)
    ->orderBy('tcaja_oficina.ini', 'desc')
    ->orderBy('tcaja_usuario.hini', 'desc')
    ->limit(100)
    ->get();

$cashSelected = $database->table('tcaja_usuario')
    ->where('idCA', $cashId)
    ->where('idU', $userId)
    ->first();

?>
<?php //lista buscador 
?>
<link href="../css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<?php //fecha 
?>
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

<?php //tabla 
?>
<link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Reportes <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body" id="crack">
        <div id="juve">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">REPORTES DESEMBOLSOS </a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body" id="impa">
                            <form action="arqueoCaja.php" method="get">
                                <div class="form-group row">
                                    <label class="col-md-1 control-label" id="">USUARIO</label>
                                    <div class="col-md-2" id="lista1" style="padding-bottom:10px">
                                        <select class="form-control" name="userId">
                                            <?php foreach ($users as $user) { ?>
                                                <option value="<?php echo $user->idU ?>" <?php echo $user->idU == $userId ? 'selected' : '' ?>>
                                                    <?php echo $user->apU . ' ' . $user->amU . ' ' . $user->nomU ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <?php if ($userId) { ?>
                                        <label class="col-md-1 control-label" id="">CAJA</label>
                                        <div class="col-md-2" id="lista1" style="padding-bottom:10px">
                                            <select class="form-control" name="cashId">
                                                <option value="">-- Seleccione --</option>
                                                <?php foreach ($cashes as $cash) { ?>
                                                    <option value="<?php echo $cash->idCA ?>" <?php echo $cash->idCA == $cashId ? 'selected' : '' ?>>
                                                        <?php echo $cash->ini . ' ' . $cash->hini ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <div class="col-md-2">
                                        <button title="Buscar Cliente" type="submit" class="btn btn-info" style="height: 29px">
                                            <i class="glyphicon glyphicon-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="col-xs-12">
                                <?php if ($cashSelected) { ?>
                                    <a class="btn btn-primary" href="./../app/pdf/arqueoCaja.php?cashId=<?php echo $cashSelected->idCA ?>" target="_blank">VER ARQUEO DE CAJA</a>
                                <?php } else { ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="extra/min.js"></script>
<?php
//fehca 
?>
<?php include('footer.php'); ?>