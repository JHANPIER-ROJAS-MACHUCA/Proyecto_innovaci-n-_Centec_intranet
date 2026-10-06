<?php

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

include('head.php');

$rquest = new Request();

$customer = Customer::find($request->get('customerId'));

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
            <span>CREAR PRESTAMO DEL CLIENTE: </span>
            <span style="margin-left: 5px;"><?php echo $customer->ap . ' ' . $customer->am . ' ' . $customer->nom  ?></span>
        </h3>

        <form action="" method="post">
            <div class="row">
                <div class="col-12">
                    <strong>DATOS DEL PRESTAMO</strong>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 ">
                        <label>Tipo de Prestamo</label>
                        <select class="form-control" onchange="detal()" name="txttipoPresta" id="txttipoPresta">
                            <option value="1">Transporte</option>
                            <option value="2">Comercio</option>
                            <option value="3">Prendatario</option>
                            <option value="4">Servicio</option>
                        </select>
                    </div>
                    <?php //monto 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Monto Propuesto</label>
                        <div class="input-group margin">
                            <span class="input-group-btn" disabled>
                                <a class="btn btn-info" style="font-weight:bold">S/. </a>
                            </span>
                            <input autocomplete="off" onkeypress="return numi(event)" onkeyup="monti()" title="Monto a Designar" maxlength="9" class="form-control" style="text-align:right" placeholder="0.00" type="text" name="txtmonto" id="txtmonto" value="">
                        </div>
                    </div>
                    <?php //tasa 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Tasa de Interes %</label>
                        <div class="input-group margin">
                            <select class="form-control" name="txtinteres" onchange="monti()" id="txtinteres">
                                <?php
                                for ($i = 4; $i < 51; $i++) {
                                ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php } ?>
                                <?php
                                // if ($_COOKIE['tuser'] == "2" || $_COOKIE['tuser'] == "1" || $_COOKIE['tuser'] == "7") {
                                ?>
                                <!-- <option value="15">15</option> -->
                                <?php //} 
                                ?>
                            </select>
                            <!--<input autocomplete="off" onkeypress="return numero(event)" onkeyup="monti()"  title="Taza de interes"   maxlength="3" class="form-control" style="text-align:right" placeholder="0.00" type="text"  value="8">-->
                            <span class="input-group-btn" disabled>
                                <a class="btn btn-info" style="font-weight:bold">%</a>
                            </span>
                        </div>
                    </div>
                    <?php //pago 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Pagos</label>
                        <select class="form-control" id="txtpago" name="txtpago" onchange="monti()">
                            <option value="" disabled selected>- -Seleccione- -</option>
                            <option value="1">Diario</option>
                            <option value="2">Semanal</option>
                            <option value="3">Pago Unico</option>
                            <option value="5">Quincenal</option>
                            <option value="4">Mensual</option>
                        </select>
                    </div>
                    <?php //plazo 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Plazo</label>
                        <input type="text" class="form-control" onkeyup="monti()" autocomplete="off" onkeypress="return numero(event)" name="txtplazo" id="txtplazo" value="">
                    </div>
                    <?php //cuota 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Cuota</label>
                        <div class="input-group margin">
                            <span class="input-group-btn" disabled>
                                <a class="btn btn-info" style="font-weight:bold">S/. </a>
                            </span>
                            <input type="text" class="form-control" style="background:white" disabled id="txtcuota" name="txtcuota" value="">
                            <input type="hidden" class="form-control" style="background:white" id="txtcuotaf1" name="txtcuotaf1" value="">
                        </div>
                    </div>
                    <?php //utima cuota 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Ultima Cuota</label>
                        <div class="input-group margin">
                            <span class="input-group-btn" disabled>
                                <a class="btn btn-info" style="font-weight:bold">S/. </a>
                            </span>
                            <input type="text" class="form-control" style="background:white" disabled id="txtcuotaF" name="txtcuotaF" value="">
                        </div>
                    </div>
                    <?php //mora x di 
                    ?>
                    <div class="col-sm-2 ">
                        <label>Mora por día</label>
                        <div class="input-group margin">
                            <span class="input-group-btn" disabled>
                                <a class="btn btn-info" style="font-weight:bold">S/. </a>
                            </span>
                            <input type="text" onkeypress="return numi(event)" class="form-control" style="background:white" id="txtmora" name="txtmora" value="" maxlength="5" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-sm-2 ">
                        <label>Funcionario responsable</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Seleccione una opción</option>
                            <?php
                            $stmtUser = extraer("SELECT * FROM tusuario WHERE estadoU='1' AND (tipoU=3 || tipoU=4)");
                            while ($row = mysqli_fetch_array($stmtUser)) {
                            ?>
                                <option value="<?php echo $row['idU'] ?>"><?php echo $row['apU'] . ' ' . $row['amU'] . ' ' . $row['nomU'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label id="labelFechaPago">Fecha de inicio de pago</label>
                        <input type="date" class="form-control" name="started_at" id="started_at">
                    </div>
                    <?php //fecha de ultimo pago 
                    ?>
                    <div class="col-sm-2" id="fechiPa" style="display:none">
                        <label>Fecha de Pago</label>
                        <input type="text" value="" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="AfechaP" id="AfechaP">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <strong>DATOS AVAL</strong>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <label>DNI(*)</label>
                        <input type="text" class="form-control" accesskey="s" placeholder="Ingrese número de DNI" onkeypress="return numero(event)" onkeyup="consultaConyuge('txtdni','txtap','txtam','txtnom', 'sexo', 'Afecha')" autocomplete="off" id="txtdni" name="txtdni" maxlength="8">
                    </div>
                    <div class="col-sm-3">
                        <label>Apellido Paterno</label>
                        <input type="text" class="form-control" placeholder="Apellido Paterno" autocomplete="off" id="txtap" name="txtap">
                    </div>
                    <div class="col-sm-3">
                        <label>Apellido Materno</label>
                        <input type="text" class="form-control" placeholder="Apellido Materno" autocomplete="off" id="txtam" name="txtam">
                    </div>
                    <div class="col-sm-3">
                        <label>Nombres</label>
                        <input type="text" class="form-control" placeholder="Nombre" id="txtnom" autocomplete="off" name="txtnom">
                    </div>
                    <?php //sexo 
                    ?>
                    <div class="col-sm-3">
                        <label>Sexo</label>
                        <select class="form-control" name="sexo" id="sexo">
                            <option value="" disabled selected>--Seleccione--</option>
                            <option value="F">FEMENINO</option>
                            <option value="M">MASCULINO</option>
                        </select>
                    </div>
                    <?php //fecha nac 
                    ?>
                    <div class="col-sm-3">
                        <label>Fecha de Nac.</label>
                        <input type="text" value="" class="form-control datepicker" style="text-align:center" placeholder="dd/mm/aaaa" name="Afecha" id="Afecha">
                    </div>
                    <?php //imagen 
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <strong>DATOS AVAL</strong>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <label>DNI(*)</label>
                        <input type="text" class="form-control" placeholder="Ingrese número de DNI" onkeypress="return numero(event)" autocomplete="off" onkeyup="consultaAval('txtdni1','txtap1','txtam1','txtnom1', 'txtdirec', 'txtcel', 'txtocu')" id="txtdni1" name="txtdni1" maxlength="8">
                    </div>
                    <div class="col-sm-3">
                        <label>Apellido Paterno</label>
                        <input type="text" class="form-control" placeholder="Apellido Paterno" autocomplete="off" id="txtap1" name="txtap1">
                    </div>
                    <div class="col-sm-3">
                        <label>Apellido Materno</label>
                        <input type="text" class="form-control" placeholder="Apellido Materno" autocomplete="off" id="txtam1" name="txtam1">
                    </div>
                    <div class="col-sm-3">
                        <label>Nombres</label>
                        <input type="text" class="form-control" placeholder="Nombre" id="txtnom1" autocomplete="off" name="txtnom1">
                    </div>
                    <div class="col-sm-3">
                        <label>Dirección</label>
                        <input type="text" class="form-control" name="txtdirec" id="txtdirec" placeholder="jr. desconocido n° 00" value="">
                    </div>
                    <div class="col-sm-3">
                        <label>Celular</label>
                        <input type="text" class="form-control" name="txtcel" id="txtcel" onkeypress="return numero(event)" maxlength="11" value="">
                    </div>
                    <div class="col-sm-3">
                        <label>Ocupación</label>
                        <input type="text" class="form-control" maxlength="45" name="txtocu" id="txtocu" placeholder="A que se dedica" value="">
                    </div>
                    <div class="col-sm-3">
                        <label>Dirección del Centro de Tabrabajo</label>
                        <input type="text" class="form-control" name="txtdirecTra" id="txtdirecTra" maxlength="45" value="">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>