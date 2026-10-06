<?php include('head.php'); ?>
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>

    <h5 style="color:white">Extornaciones<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div class="tabs-container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1" onclick="solicitudes()"><strong>SOLICITUD DE EXTORNO</strong></a></li>
        <li><a data-toggle="tab" href="#tab-2" onclick="opera()"><strong>INFORMACIÓN DE OPERACIONES</strong></a></li>
        <!--<li class=""><a data-toggle="tab" href="#tab-2">Direcciones</a></li>
          <li class=""><a data-toggle="tab" href="#tab-3">Negocios</a></li>
          <li class=""><a data-toggle="tab" href="#tab-4">Prestamos</a></li>-->
      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
          <div class="panel-body">
            <fieldset class="form-horizontal">
              <div class="form-group">
                <div class="col-md-12 col-sm-12">
                  <div class="form-group">
                    <div id="extor1">

                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
        </div>
        <div id="tab-2" class="tab-pane">
          <div class="panel-body">
            <fieldset class="form-horizontal">
              <div class="form-group">
                <div class="col-md-4 col-sm-5">
                  <div class="input-group margin" style="padding-top:10px">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;;background:#23c6c8">COD. </a>
                    </span>
                    <input autocomplete="off" onkeyup="buscar()" class="form-control" style="text-align:right" placeholder="codigo o nombre de usuario" type="text" name="txtbuscar" id="txtbuscar" value="">
                    <span class="input-group-btn">
                      <a class="btn btn-info" accesskey="a" onclick="buscar()" id="llamar" style="font-weight:bold"><i class="fa fa-search"></i> </a>
                    </span>
                  </div>
                </div>
                <div class="col-md-4 col-sm-5">
                  <div class="input-group margin" style="padding-top: 10px;">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;;background:#23c6c8">DE. </a>
                    </span>
                    <input id="textdatestart" class="form-control" type="date" onchange="buscar()">
                  </div>
                </div>
                <div class="col-md-4 col-sm-5">
                  <div class="input-group margin" style="padding-top: 10px;">
                    <span class="input-group-btn" disabled>
                      <a class="btn btn-info" disabled style="font-weight:bold;;background:#23c6c8">HASTA. </a>
                    </span>
                    <input id="textdateend" class="form-control" type="date" onchange="buscar()">
                  </div>
                </div>
              </div>
              <?php //empezemos a generar la tabla de extornaciones 
              ?>
              <div class="form-group">
                <div id="extor">

                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <?php include('footer.php'); ?>
    <script src="functiones/extor.js"></script>
    <script>
      function extor(id) {
        swal({
          title: "¿Seguro que deseas eliminar la transacción?",
          text: 'Esta acción no puede ser revertida.',
          icon: 'warning',
          buttons: ['No, salir', 'Si, eliminar transacción.']
        }).then(response => {
          if (response) {
            const apiPath = '<?php echo $_ENV['API_PATH'] ?>';
            fetch(`${apiPath}/transactions/${id}/extort`, {
                method: 'DELETE'
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  swal({
                    title: 'Transacción eliminado con exito!!',
                    icon: 'success'
                  });
                } else {
                  swal({
                    title: data.message,
                    icon: 'error'
                  })
                }
              })
              .catch(_ => {
                swal({
                  title: 'No se pudo eliminar la transacción.',
                  icon: 'error'
                })
              });
          }
        });

      }
    </script>