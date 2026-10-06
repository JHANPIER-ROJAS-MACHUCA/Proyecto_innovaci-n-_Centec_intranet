<?php
include 'head.php';

// caja aperturada
$cash = $database->table('cashes')->whereNull('final_balance')->first();

// ultimas 10 cajas
$cashes = $database->table('cashes')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

?>

<div class="row">
    <div class="col-sm-4">
        <?php foreach ($$cashes as $cash) { ?>
            <div>
                <div>Fecha: <?php echo $cash->ini . ' ' . $cash->iniH ?></div>
                <div></div>
            </div>
        <?php } ?>
    </div>
    <div class="col-sm-8"></div>
</div>

<?php
include 'footer.php'
?>