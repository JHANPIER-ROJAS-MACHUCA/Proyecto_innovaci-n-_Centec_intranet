<?php include('head.php') ?>

<form id="uploadPass" action="" method="post" enctype="multipart/form-data">
  <!-- /.row -->
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">CAMBIAR PASSWORD</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="first-name">DNI</label>
                <input readonly required type="text" min="1" class="form-control" id="first-name" name="txtdni" value="<?php echo $request->user()->dniU ?>">

              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="last-name">Contraseña Actual</label>
                <input required type="password" class="form-control" placeholder="Ingrese password actual" name="txtcontra">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="last-name">Contraseña Nueva</label>
                <input required type="password" class="form-control" placeholder="Ingrese password nueva" name="txtcontra1">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="last-name">Repita contraseña Nueva</label>
                <input required type="password" class="form-control" placeholder="Repita Password Nueva" id="last-name" name="txtcontra2">
              </div>
              <input type="hidden" name="txtidusuario" value="<?php echo $request->user()->idU ?>">
            </div>
          </div>
          <div class="panel-footer">
            <button tabindex="8" type="submit" class="btn btn-primary">Modificar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.col-lg-12 -->
  </div>
  <!-- /.row -->
</form>
<h4 id='loadingP'></h4>
<div id="messageP"></div>
<script src="js/usuarioPass.js"></script>
<?php
include('footer.php');
?>