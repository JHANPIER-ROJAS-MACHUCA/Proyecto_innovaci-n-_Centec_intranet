<div class="row">
  <div class="col-lg-4">
    <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
      <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <strong>Registro de Billetaje</strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <div class="col-md-4 col-sm-4">
                <label>S/.200</label>
                <input maxlength="3" class="form-control monto" type="text" name="txt200" id="txt200" onkeypress="return numero(event)" placeholder="0" value="" onkeyup="sumar2();">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.100</label>
                <input maxlength="3" onkeyup="sumar2();" class="form-control monto" type="text" name="txt100" id="txt100" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.50</label>
                <input maxlength="3" class="form-control" type="text" name="txt50" id="txt50" onkeypress="return numero(event)" placeholder="0" value="" onkeyup="sumar2();">
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-sm-4">
                <label>S/.20</label>
                <input maxlength="3" class="form-control" type="text" name="txt20" id="txt20" value="" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.10</label>
                <input maxlength="3" class="form-control" type="text" name="txt10" id="txt10" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.5</label>
                <input maxlength="3" class="form-control" type="text" name="txt5" id="txt5" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-sm-4">
                <label>S/.2</label>
                <input maxlength="3" class="form-control" type="text" ame="txt2" id="txt2" value="" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.1</label>
                <input maxlength="3" class="form-control" type="text" name="txt1" id="txt1" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.0.5</label>
                <input maxlength="3" class="form-control" type="text" name="txt05" id="txt05" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-sm-4">
                <label>S/.0.2</label>
                <input maxlength="3" class="form-control" type="text" name="txt02" id="txt02" value="" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();">
              </div>
              <div class="col-md-4 col-sm-4">
                <label>S/.0.1</label>
                <input maxlength="3" class="form-control" type="text" name="txt01" id="txt01" onkeypress="return numero(event)" placeholder="0" onkeyup="sumar2();" value="">
              </div>

            </div>
          </div>
          <br />
        </div>

        <div class="row">
          <div class="col-md-4"></div>
          <div class="col-md-6">
            <div class="form-group">
              <label>TOTAL</label>
              <input disabled class="form-control" type="text" name="txttotal" id="txttotal" value="0.00">
            </div>
          </div>
        </div>
      </div>

      <div align="right" class="panel-footer">
        <button type="button" id="btnenvio" onclick="enviar1()" class="btn btn-info">REGISTRAR</button>
        <button type="reset" id="btnlimpio" class="btn btn-secondary">Limpiar</button>
      </div>
    </div>
  </div>
  <div class="col-md-12 col-lg-8 col-sm-12">
    <!--cierre caja-->
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong> Cierre de Caja</strong>

      </div>
      <div class="panel-body">
        <div class="" id="ingresos">

        </div>
      </div>

    </div>

  </div>
</div>
</div>

<!--<script src="js/billetaje.js"></script>-->

<script src="functionesBasic/bill.js">
</script>