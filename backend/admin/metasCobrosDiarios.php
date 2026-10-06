<?php

include 'head.php';

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;" x-data="data">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
        </div>
        <h5 style="color:white">Metas cobros diarios <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <div>
        <div>
            <label "></label>
        </div>
    </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Monto a cobrar</th>
                    <th>Cobro</th>
                    <th>Porcentaje</th>
                    <th>No cobrado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

<?php include 'head.php' ?>