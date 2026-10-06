<option value="-1" disabled selected>-- Seleccione--</option>
<?php
include('../conection/bdcredito.php');
extract($_POST);
//$id='1';
$mess=extraer("SELECT idamd,mes FROM tahorro_motivo_deta where idam='$id' order by mes desc");
while($row =mysqli_fetch_array($mess))
{

  $valor=$row['mes'];
  switch ($valor) {
    case "01":
        $mes="ENERO";
        break;
    case "02":
        $mes="FEBRERO";
        break;
    case "03":
        $mes="MARZO";
        break;
    case "04":
        $mes="ABRIL";
        break;
    case "05":
        $mes="MAYO";
        break;
    case "06":
        $mes="JUNIO";
        break;
    case "07":
        $mes="JULIO";
        break;
    case "08":
        $mes="AGOSTO";
        break;
    case "09":
        $mes="SEPTIEMBRE";
      break;
    case "10":
        $mes="OCTUBRE";
        break;
    case "11":
        $mes="NOVIEMBRE";
        break;
    case "12":
        $mes="DICIEMBRE";
        break;
}
 ?>
   <option value="<?php echo $row['idamd'] ?>"> <?php echo $mes;?></option>
 <?php
}
 ?>
