<?php  require_once("conection/bdcredito.php");
	$f= date("d-m-Y ");
  $f=str_replace("-","/",$f);

 ?>

    <form method="post">
      <input type="date" class="form-control datepicker" name="fecha" value="<?php echo $f; ?>" required>
      <select name="tipo" required >
        <option value="" disabled selected>- - - - -</option>
        <option value="1">Diario</option>
        <option value="2">Semanal</option>
        <option value="3">Quincenal</option>
        <option value="4">Unico Pago</option>
      </select>
      <input type="text" name="tiempo" value="" required>
      <input type="submit" name="" value="Empezar">
    </form>
		<?php
		extract($_POST);
			if(isset($fecha))
		{
		  echo "$fecha.<br>";
		  //echo $fecha;
		  switch ($tipo) {
		    case '1':
		          $com="1 days";
		          break;
		    case '2':
		          $com="1 week";
		          break;
		    case '3':
		          $com="2 week";
		          break;
		    case '4':
		          $com="1 days";
		      break;
		  }
		  for ($i=0; $i < $tiempo; $i++)
		  {
		      $fecha=date("d-m-Y",strtotime($fecha."+".$com));

		      $dia=date("w", strtotime($fecha));

		      if($dia=='0')
		      {
		        $i--;
		      }
		      else
		      {
		        if(feriados($fecha)=='1')
		        {
		          $i--;
		        }
		        else
		        {
		          echo "<br>".$fecha;
		        }
		      }
		  }
		}
		function feriados($fec)
		{
		  $resul='0';
		  $fec1 = preg_split("~-~", $fec);
		  $fec="$fec1[0]/$fec1[1]";
		  $info=extraer("SELECT COUNT(*) AS con from tfecha where fecha='$fec'");
		  while($row =mysqli_fetch_array($info))
		  {
		    $dato=$row['con'];
		  }
		  if(!empty($dato))
		  {
		    $resul='1';
		  }
		  return $resul;
		}
		 ?>

<?php


 ?>
