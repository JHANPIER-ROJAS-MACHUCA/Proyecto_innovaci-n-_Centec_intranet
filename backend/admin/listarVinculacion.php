    <?php require_once('conection/conex.php'); ?>   
   <?php 
                                      //  $idP=$_SESSION['idP'];
$idP=$_POST['idP'];
                            $query_prestamo= "SELECT *FROM tprestamo tp,tvinculacion tv,tclie_general tc   where tp .idV=tv.idV AND tp.idCG=tc.idCG AND idP=$idP";
$prestamo= mysqli_query($conex, $query_prestamo) or die(mysqli_error());
$row_prestamo= mysqli_fetch_assoc($prestamo);
$imgT=$row_prestamo['croquisT'];
$imgC=$row_prestamo['croquisC'];
$imgA=$row_prestamo['croquisA'];
$idconyuge=$row_prestamo['conyugue'];
$idaval=$row_prestamo['aval'];
$datatitular=$row_prestamo['nom'].' '.$row_prestamo['ap'].' '.$row_prestamo['am'];
$dniT=$row_prestamo['dni'];
$celT=$row_prestamo['cel'];
//conyuge
   $query_vinc= "SELECT * FROM tclie_general  tc   where  idCG='$idconyuge'";
$vinc= mysqli_query($conex, $query_vinc) or die(mysqli_error());
$row_vinc= mysqli_fetch_assoc($vinc);
$dataconyuge=$row_vinc['nom'].' '.$row_vinc['ap'].' '.$row_vinc['am'];
$dniC=$row_vinc['dni'];
$celC=$row_vinc['cel'];
//aval
  $query_aval= "SELECT * FROM tclie_general  tc   where  idCG='$idaval'";
$aval= mysqli_query($conex, $query_aval) or die(mysqli_error());
$row_aval= mysqli_fetch_assoc($aval);
$dataaval=$row_aval['nom'].' '.$row_aval['ap'].' '.$row_aval['am'];
$dniA=$row_aval['dni'];
$celA=$row_aval['cel'];                                    
                                            ?>
                                                <!--VINCULACION-->
                                                <!--busca-->
                                         
                                           <h4>Datos vinculados</h4>
                                              <div class="col-md-12">
                                                <TABLE>
                                                    <thead>
                                                        <tr>
                                                            <th>TIPO</th>
                                                            <th>DNI</th>
                                                            <th>DATOS</th>
                                                            <th>#</th>
                                                             <th>Ir</th>
                                                        </tr>
                                                       
                                                       
                                                    </thead>
                                                    <tbody>
                                                        <tr>

                                                            <td>TITULAR</td>
                                                            <td><?php echo "$dniT"; ?></td>
                                                            <td><?php echo "$datatitular"; ?></td>
                                                            <td> <?php echo "$celT"; ?></td>
                                                            <td> </td>
                                                        </tr>
                                                     <tr>
                                                            <td>CONYUGE</td>
                                                            <td><?php echo "$dniC"; ?></td>
                                                            <td>  <?php echo "$dataconyuge"; ?></td>
                                                            <td>  <?php echo "$celC"; ?></td>
                                                            <td><?php  if ($idconyuge!="") {
                                                                ?>
                                                                 <form action="profile.php" method="post">
                            <input type="hidden" name="idCG" value="<?php echo $idconyuge;?>">
                             <!--<a class="btn btn-xs btn-white"><i class="fa fa-user-plus"></i> Follow</a>-->
                            <button type="submit" class="btn btn-xs btn-white"><i class="fa fa-paper-plane"></i> Ir</button>
                            </form> <?php
                                                            } ?>  </td>
                                                        </tr>
                                                         <tr>
                                                            <td>AVAL</td>
                                                            <td><?php echo "$dniA"; ?></td>
                                                            <td> <?php echo "$dataaval"; ?></td>
                                                            <td>  <?php echo "$celA"; ?></td>
                                                            <td><?php  if ($idaval!="") {
                                                                ?>
                                                                 <form action="profile.php" method="post">
                            <input type="hidden" name="idCG" value="<?php echo $idaval;?>">
                             <!--<a class="btn btn-xs btn-white"><i class="fa fa-user-plus"></i> Follow</a>-->
                            <button type="submit" class="btn btn-xs btn-white"><i class="fa fa-paper-plane"></i> Ir</button>
                            </form> <?php
                                                            } ?>   
                        </td>
                                                        </tr>
                                                    </tbody>
                                                </TABLE>
                     
                    
                       
                    </div>
                                          
                                <div class="form-group">
<form id="upload4" action="" method="post" enctype="multipart/form-data">
                                                <h5>CONYUGE</h5>
                                        <div class="form-group row">
                              <label for="nombres_c" class="col-md-1 control-label">DNI</label>
                              <div class="col-md-3">
                                  <input type="text" class="form-control input-sm" id="dni_c" placeholder="Selecciona un Cliente" required>
                                  <input type="hidden" id="idconyuge" name="idconyuge" readonly required value="<?php echo $idconyuge ?>" /> 
                                   <input style="width:35px;height:25px" class="form-control" readonly type="hidden" name="txtidP5" id="txtidP5" value="<?php echo $idP;?>">
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
                                 <h5>AVAL</h5>
                        <div class="form-group row">

                  <label for="nombres_c" class="col-md-1 control-label">DNI</label>
                  <div class="col-md-3">
                      <input type="text" class="form-control input-sm" id="dni_c2" placeholder="Selecciona un Cliente" required>
                      <input type="hidden" id="idaval" name="idaval" readonly  required value="<?php echo $idaval; ?>"/>  
                  </div>
                  <label for="telefono_c" class="col-md-1 control-label">AP</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" id="ap_c2" placeholder="Apellidos" readonly>
                            </div>
                    <label for="correo_c" class="col-md-1 control-label">NOM</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" id="nom_c2" placeholder="Nombres" readonly>
                            </div>
                 </div>

                 <input type="submit" value="Vincular" class="submit" />
</form>
<h4 id='loading4' >Cargando..</h4>
<div id="message4"></div>
 <script src="script4.js"></script>
                  <!--img-->
                              <h5>IMAGEN TITULAR</h5>
                             <div class="form-group row">
                             <div class="main">
<br/>

<form id="uploadimage3" action="" method="post" enctype="multipart/form-data">
       <input style="width:35px;height:25px" class="form-control" readonly type="text" name="txtidP4" id="txtidP4" value="<?php echo $idP;?>" >
         
<div id="image_preview3" ><img id="previewing3" <?php if ($imgT=="") { ?> src="img2/croquis/maps2.jpg" <?php }else{ ?> src="upload/<?php echo $row_prestamo['croquisT'] ?>" <?php } ?>  /></div>
<hr id="line" >
<label>Selecciona tu imagen</label><br/>
<input type="file" name="file3" id="file3" required />

<input type="submit" value="Actualizar" class="submit" />
</form>
<h4 id='loading3' >Cargando..</h4>
<div id="message3"></div>
</div>

       </div>    
       <script src="script3.js"></script>
          <!---->
                             <!--img-->
                              <h5>IMAGEN CONYUGE</h5>
                             <div class="form-group row">
                             <div class="main">
<br/>

<form id="uploadimage" action="" method="post" enctype="multipart/form-data">
       <input style="width:35px;height:25px" class="form-control" readonly type="text" name="txtidP2" id="txtidP2" value="<?php echo $idP;?>">
         
<div id="image_preview" ><img id="previewing" <?php if ($imgC=="") { ?> src="img2/croquis/maps2.jpg" <?php }else{ ?> src="upload/<?php echo $row_prestamo['croquisC'] ?>" <?php } ?>  /></div>
<hr id="line" >
<label>Selecciona tu imagen</label><br/>
<input type="file" name="file" id="file" required />

<input type="submit" value="Actualizar" class="submit" />
</form>
<h4 id='loading' >Cargando..</h4>
<div id="message"></div>
</div>

       </div>    
       <script src="script.js"></script>
          <!---->
                 <h5>IMAGEN AVAL</h5>
                        <!--img-->
                             <div class="form-group row">
                                                 <div class="main">
                    <br/>

                    <form id="uploadimage2" action="" method="post" enctype="multipart/form-data">
                           <input style="width:35px;height:25px" class="form-control" readonly type="text" name="txtidP3" id="txtidP3" value="<?php echo $idP;?>">
                          
                          
                    <div id="image_preview" ><img id="previewing2" <?php if ($imgA=="") { ?> src="img2/croquis/maps2.jpg" <?php }else{ ?> src="upload/<?php echo $row_prestamo['croquisA'] ?>" <?php } ?>  /></div>
                    <hr id="line" >
                    <label>Selecciona tu imagen</label><br/>
                    <input type="file" name="file2" id="file2" required />
                  
                    <input type="submit" value="Actualizar" class="submit" />
                    </form>
                    <h4 id='loading2' >Cargando..</h4>
                    <div id="message2"></div>
                    </div>

       </div>    
                 <script src="script2.js"></script>
          <!---->
                 <!--script buscador-->
     <link rel="stylesheet" href="jqueryui/jquery-ui.css">
    <script src="jqueryui/jquery-ui.js"></script>
<script>
        $(function() {
                        $("#dni_c").autocomplete({
                            source: "./ajax/autocomplete/cliente.php",
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
    <script>
        $(function() {
                        $("#dni_c2").autocomplete({
                            source: "./ajax/autocomplete/cliente.php",
                            minLength: 2,
                            select: function(event, ui) {
                                event.preventDefault();
                                $('#idaval').val(ui.item.idCG);
                                $('#dni_c2').val(ui.item.dni);
                                $('#ap_c2').val(ui.item.ap);
                                $('#nom_c2').val(ui.item.nom);
                             }
                        }); 
                    });
                    
    $("#dni_c2" ).on( "keydown", function( event ) {
                        if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
                        {
                            $("#idaval").val("");
                            $("#nom_c2").val("");
                            $("#ap_c2").val("");
                                            
                        }
                        if (event.keyCode==$.ui.keyCode.DELETE){
                            $("#dni_c2").val("");
                            $("#idaval").val("");
                            $("#nom_c2" ).val("");
                            $("#ap_c2" ).val("");
                        }
            }); 
    </script>  

<style type="text/css">


.main {
position: relative;
width: 400px;
height:530px;

}
.main label{
color: white;
margin-left: 60px;
}
#image_preview,#image_preview2,#image_preview3{
position: absolute;
font-size: 30px;

top: 10px;
left: 10px;
width: 450px;
height: 230px;
text-align: center;
line-height: 180px;
font-weight: bold;
color: #C0C0C0;
background-color: #FFFFFF;
overflow: auto;
}

.submit{
font-size: 16px;
background: linear-gradient(#ffbc00 5%, #ffdd7f 100%);
border: 1px solid #e5a900;
color: #4E4D4B;
font-weight: bold;
cursor: pointer;
width: 300px;
border-radius: 5px;
padding: 10px 0;
outline: none;
margin-top: 20px;
margin-left: 15%;
}
.submit:hover{
background: linear-gradient(#ffdd7f 5%, #ffbc00 100%);
}
#file,#file2,#file3 {
color: white;
padding: 5px;
margin-top: 10px;
border-radius: 5px;
margin-left: 14%;
width: 72%;
}
#message,#message2,#message3,#message4{
position:absolute;
top:180px;
left:15px;
}
#success
{
color:green;
}
#invalid
{
color:red;
}
#line 
{
margin-top:288px;
}
#error
{
color:red;
}
#error_message
{
color:blue;
}
#loading,#loading2,#loading3,#loading4
{
display:none;
position:absolute;
top:170px;
left:85px;
font-size:25px;
}
hr{
    border-top: 1px solid  #27ae60;
}
</style>
                                         
                                        </div>