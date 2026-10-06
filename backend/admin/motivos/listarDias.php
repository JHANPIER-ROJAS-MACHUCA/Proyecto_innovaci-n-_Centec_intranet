<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  //$id='1';
  if(isset($id))
  {
  $dias=extraer("SELECT lunes, martes, miercoles, jueves, viernes, sabado, domingo FROM tahorro_motivo_deta where idamd='$id'");
  $row =mysqli_fetch_array($dias);
  ?>
  <table style="width:100%">
    <tr>
      <td align="center">
        DOM
      </td>
      <td align="center">
        LU
      </td>
      <td align="center">
        MA
      </td>
      <td align="center">
        MI
      </td>
      <td align="center">
        JU
      </td>
        <td align="center">
        VI
      </td>
        <td align="center">
        SA
      </td>
      <td align="center">

      </td>
    </tr>
    <tr>
        <td align="center">
          <input type="checkbox" value="" <?php if($row['domingo']=="1"){echo "checked";} ?> name="dom" id="dom">
      </td>
        <td align="center">
        <input type="checkbox" name="" <?php if($row['lunes']=="1"){echo "checked";} ?> value="lu" id="lu">
      </td>
      <td align="center">
        <input type="checkbox" name="" <?php if($row['martes']=="1"){echo "checked";} ?> value="ma" id="ma">
      </td>
        <td align="center">
        <input type="checkbox" name="" <?php if($row['miercoles']=="1"){echo "checked";} ?> value="mi" id="mi">
      </td>
        <td align="center">
        <input type="checkbox" name="" <?php if($row['jueves']=="1"){echo "checked";} ?> value="ju" id="ju">
      </td>
      <td align="center">
        <input type="checkbox" name="" <?php if($row['viernes']=="1"){echo "checked";} ?> value="vie" id="vie">
      </td>
    <td align="center">
        <input type="checkbox" name="" <?php if($row['sabado']=="1"){echo "checked";} ?> value="sab" id="sab">
      </td>
        <td align="center">
          <a class="btn btn-info" title="Guardar dias" onclick="cambioDia()"><i class="fa fa-save"></i></a>
      </td>
    </tr>
  </table>
<?php } ?>
