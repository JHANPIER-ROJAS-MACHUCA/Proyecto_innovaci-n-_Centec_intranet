 <?php
include ('head.php');
 ?>
    <div class="row">
                                <div class="col-lg-4">
                                    <div class="panel panel-info">
      <div class="panel-heading">
      	<h4>billetaje</h4>
       </div>
<form id="billetaje" action="" method="post" enctype="multipart/form-data">
      <div class="panel-body">
    <div class="col-md-4">
           	<div class="form-group"> <label>S/.200</label><input required class="form-control monto" type="number" min="0" step="1" name="txt200" id="txt200" value="0" " onkeyup="sumar2();">  </div>
                       
        <div class="form-group"> <label>S/.20</label> <input required class="form-control" type="number" min="0" step="1" name="txt20" id="txt20" value="0" onkeyup="sumar2();">  </div>
   	<div class="form-group">   <label>S/.2</label>  <input required class="form-control" type="number" min="0" step="1"name="txt2" id="txt2" value="0" onkeyup="sumar2();">  </div>
       <div class="form-group"><label>S/.0.2</label>  <input required class="form-control" type="number" min="0" step="1" name="txt02" id="txt02" value="0" onkeyup="sumar2();">  </div>
            </div>
 <div class="col-md-4">
        <div class="form-group"> <label>S/.100</label> <input  required onkeyup="sumar2();" class="form-control monto" type="number" min="0" step="1" name="txt100" id="txt100" onkeyup="sumar2();" value="0"></div>
         <div class="form-group"> <label>S/.10</label> <input required class="form-control" type="number" min="0" step="1" name="txt10" id="txt10" onkeyup="sumar2();" value="0"></div>
           <div class="form-group"><label>S/.1</label>  <input required class="form-control" type="number" min="0" step="1" name="txt1" id="txt1" onkeyup="sumar2();" value="0"></div>
         <div class="form-group"><label>S/.0.1</label><input required class="form-control" type="number" min="0" step="1" name="txt01" id="txt01" onkeyup="sumar2();" value="0"></div>
             </div>
<div class="col-md-4">
       <div class="form-group">    <label>S/.50</label>  <input required class="form-control" type="number" min="0" step="1" name="txt50" id="txt50" value="0" onkeyup="sumar2();"></div>
        <div class="form-group">  <label>S/.5</label> <input required class="form-control" type="number" min="0" step="1" name="txt5" id="txt5" onkeyup="sumar2();" value="0"></div>
      <div class="form-group">  <label>S/.0.5</label> <input required class="form-control" type="number" min="0" step="1" name="txt05" id="txt05" onkeyup="sumar2();" value="0"> </div> 
        <div class="form-group">  <label>TOTAL</label> <input required readonly  class="form-control" type="number" min="0" step="1" name="txttotal" id="txttotal" value="0.00"> </div> 
       
         
<br/>
  </div>	
	</div>
               <?php  
$idU2=$_COOKIE['user1'];
$consulB=extraer("SELECT * FROM tbilletaje b2 where b2.idBille=(SELECT max(b.idBille) FROM tbilletaje b where b.idU=$idU2)");
$dataB=mysqli_fetch_array($consulB);
$estadoB=$dataB['estado'];
  ?>
     <div align="right" class="panel-footer">
      <?php 
if ($estadoB=='2' or $estadoB==null) {
  ?>
    <button  type="submit" class="btn btn-info">REGISTRAR</button>
  <?php
}else{
 
}
       ?>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
    </div>
</form>
</div>	
<h4 id='loading4' ></h4>
<div id="message4"></div>

                                </div>
<div class="col-lg-8">
  <!--cierre caja-->
              <div class="panel panel-info">
         <div class="panel-heading">
                Cierre de Caja
         </div>
         <div class="panel-body">
                 <div class="form-group  row">           
                              <label class="col-md-1 control-label">Fecha</label>
                              <div class="col-md-3">
                                  <input type="date" class="form-control input-sm" id="" required>
                                  <input type="hidden" id="id" name="id" readonly required value="" /> 
                              </div>
                              <label class="col-md-1 control-label">Codigo</label>
                                        <div class="col-md-3">
                                            <input style="width: 30%" type="text" class="form-control input-sm"  readonly>
                                        </div>
                                <label  class="col-md-1 control-label">Usuario</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control input-sm" id=""  readonly>
                                        </div>
                     </div>
                       <div class="form-group  row">  
                       <div align="right"><label class="col-md-1 control-label">Nombre</label></div>         
                              <div class="col-md-7">
                                  <input type="text" readonly class="form-control input-sm" id="" required>
                              </div>
                     </div>
          <div class="tabs-container">
                <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#tab-1"> Cuadre Caja en Soles (S/.)</a></li>
               </ul>   
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                      <br>
                      <div class="form-group">
                        <div class="col-md-12 ">
                        <div class="col-md-8">
                          <label align="right"  class="col-md-4">SALDO INICIAL   SOLES (S/.):</label>
                <div class="col-md-4">
                  <input type="text" readonly class="form-control" value="0.00">
                </div>
                        </div>
                      </div>
                      </div><br><br>
                      <div class="form-group">
                        <div class="col-md-12 ">
                        <div class="col-md-6">
                           <div class="panel panel-info">
                                        <div class="panel-heading">
                                              <h3 class="panel-title" align="center"><b>INGRESOS</b></h3>
                                        </div>
                                        <div class="panel-body">
                                             <div class="ibox-content table-responsive" id="ingresos">
                                </div>
                                        </div>
                                        <div class="panel-footer">
                                                       <div class="form-group row">
              <div  align="right"><label class="col-md-5 control-label">TOTAL INGRESOS:</label></div>
                     <div class="col-md-6">
                        <input style="width: 50%" readonly type="text" class="form-control input-sm" id="">
                      </div>
                  </div>
                                        </div>
                                    </div>
                        </div>
                        <div class="col-md-6">
                           <div class="panel panel-info">
                                        <div class="panel-heading">
                                             <h3 class="panel-title" align="center"><b>EGRESOS</b></h3>
                                        </div>
                                        <div class="panel-body">
                                            <p>Egresos</p>
                                        </div>
                                        <div class="panel-footer">
                                          <!--  TOTAL EGRESOS : <input style="width:30%" class="form-control" type="number" name="" readonly>-->
                                             <div class="form-group row">
              <div style="text-align: right;"><label class="col-md-5 control-label">TOTAL EGRESOS:</label></div>
                     <div class="col-md-6">
                        <input style="width: 50%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
                                        </div>
                                    </div>
                        </div>
                      </div>
                      </div>
                   </div>
                </div>
             </div> 
             <div class="form-group row">
              <div align="right"><label class="col-md-5 control-label">SALDO FINAL SOLES (S/.):</label></div>
                     <div class="col-md-6">
                        <input style="width: 30%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
               <div class="form-group row">
              <div align="right"><label class="col-md-4 control-label">SALDO FINAL CORTE (S/.):</label></div>
                     <div class="col-md-2">
                        <input style="width: 100%" readonly type="text" class="form-control input-sm" id="">
                      </div>
                      <div align="lefth"><label class="col-md-2 control-label">DIFERENCIA (S/.):</label></div>
                     <div align="lefth"  class="col-md-4">
                        <input style="width: 100%" readonly type="text" class="form-control input-sm" id="">
                      </div>
             </div>
        </div>
          <div align="right" class="panel-footer">
                                                <button type="submit" class="btn btn-info">GUARDAR</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">SALIR</button>
                                        </div>
 </div>
 <!--cierre caja fin-->


</div>
</div>

 <script src="js/billetaje.js"></script>
 <?php
include ('footer.php');
 ?>
 <script src="functiones/cierrecaja.js"></script>
<script>


function sumar2()
{
   var b200=$('#txt200').val();
   var b100=$('#txt100').val();
  var b50=$('#txt50').val();
  var b20=$('#txt20').val();
  var b10=$('#txt10').val();
  var b5=$('#txt5').val();
  var b2=$('#txt2').val();
  var b1=$('#txt1').val();
  var b05=$('#txt05').val();
  var b02=$('#txt02').val();
  var b01=$('#txt01').val();
    if(b200=="")
  {
    b200=0;
  }
   if( b100=="")
  {
    b50=0;
  }
     if(b50=="")
  {
    b50=0;
  }
  if(b20=="")
  {
    b20=0;
  }
 if(b10=="")
  {
    b10=0;
  }
  if(b5=="")
  {
    b5=0;
  }
   if(b2=="")
  {
    b2=0;
  }
    if(b1=="")
  {
    b1=0;
  }
    if(b05=="")
  {
    b05=0;
  }
    if(b02=="")
  {
    b02=0;
  }
    if(b01=="")
  {
    b01=0;
  }
 total=(parseFloat(b200)*200)+(parseFloat(b100)*100)+(parseFloat(b50)*50) +(parseFloat(b20) * 20)+(parseFloat(b10)*10)  + (parseFloat(b5)*5)+(parseFloat(b2)*2)+(parseFloat(b1)*1)+(parseFloat(b05)*0.5)+(parseFloat(b02)*0.2)+(parseFloat(b01)*0.1);
  $('#txttotal').val(total);
}
</script>