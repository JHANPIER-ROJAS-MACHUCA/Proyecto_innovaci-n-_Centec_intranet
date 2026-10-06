<?php include('head.php') ?>


<form id="uploadUser" action="" method="post" enctype="multipart/form-data">
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
                <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
                    USUARIO MODIFICAR
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <div class="row">

                        <div class="col-lg-4">

                            <div class="form-group">
                                <label>Dni :</label>
                                <input readonly tabindex="1" required name="txtdni" type="text" class="form-control" placeholder="Ingrese Dni" value="<?php echo $request->user()->dniU ?>">
                            </div>
                            <div class="form-group">
                                <label>Correo :</label>
                                <input tabindex="4" name="txtcorreo" type="text" class="form-control" placeholder="Ingrese Correo" value="<?php echo $request->user()->correoU ?>">
                            </div>




                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Direccion :</label>
                                <input tabindex="2" name="txtdireccion" type="text" class="form-control" placeholder="Ingrese Direccion" value="<?php echo $request->user()->direcU ?>">
                            </div>


                        </div>
                        <!-- /.col-lg-6 (nested) -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Celular :</label>
                                <input tabindex="3" name="txtcelular" type="text" class="form-control" placeholder="Ingrese Celular" value="<?php echo $request->user()->celU ?>">
                            </div>
                            <input readonly tabindex="6" name="txtidusuario" type="hidden" class="form-control" value="<?php echo $request->user()->idU ?>">
                        </div>
                        <!-- /.col-lg-6 (nested) -->
                    </div>
                </div>
                <!-- /.panel-body -->
                <div class="panel-footer">
                    <button tabindex="8" type="submit" class="btn btn-info">Modificar</button>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</form>


<h4 id='loadingU'></h4>
<div id="messageU"></div>
<script src="js/usuarioModificar.js"></script>





<?php
include('footer.php');
?>