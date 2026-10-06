   <link href="../css/bootstrap.min.css" rel="stylesheet">
   <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
     <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
                                             <h5>CONYUGE</h5>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group  row">
                              <label for="nombres_c" class="col-md-1 control-label">DNI/AP/</label>
                              <div class="col-md-3">
                                  <input type="text" class="form-control input-sm" id="dni_c" placeholder="Selecciona un Cliente" required>
                                  <input type="hidden" id="idconyuge" name="idconyuge" readonly required value="" />
                              </div>
                              <label for="telefono_c" class="col-md-1 control-label">AP</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control input-sm" id="ap_c" placeholder="Apellidos" readonly>
                                        </div>
                                <label for="correo_c" class="col-md-1 control-label">NOM</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control input-sm" id="nom_c" placeholder="Nombres" readonly>
                                        </div>
                     </div>
                                        </div>
                                        <div class="panel-footer">
                                            Panel Footer
                                        </div>
                                    </div>
                                    <script src="jqueryui/jquery.min.js"></script>
<link rel="stylesheet" href="jqueryui/jquery-ui.css">
<script src="jqueryui/jquery-ui.js"></script>


 <script>
        $(function() {
                        $("#dni_c").autocomplete({
                            source: "ajax/autocomplete/cliente.php",
                            minLength: 2,
                            select: function(event, ui) {
                                event.preventDefault();
                                $('#idconyuge').val(ui.item.idCG);
                                $('#dni_c').val(ui.item.dni);
                                $('#ap_c').val(ui.item.ap);
                                $('#nom_c').val(ui.item.nom);
                             }
                        });
                    });
    $("#dni_c" ).on( "keydown", function( event ) {
                        if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
                        {
                            $("#idconyuge").val("");
                            $("#nom_c").val("");
                            $("#ap_c").val("");
                        }
                        if (event.keyCode==$.ui.keyCode.DELETE){
                            $("#dni_c").val("");
                            $("#idconyuge").val("");
                            $("#nom_c" ).val("");
                            $("#ap_c" ).val("");
                        }
            });
    </script>
