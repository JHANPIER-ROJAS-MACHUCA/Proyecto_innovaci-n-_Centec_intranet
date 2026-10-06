<?php
if (isset($_GET['term'])){
include("../../conection/db.php");
include("../../conection/conexion2.php");
$return_arr = array();
/* If connection to database, run sql statement. */
if ($con)
{
	
	$fetch = mysqli_query($con,"SELECT * FROM tclie_general where dni like '%". mysqli_real_escape_string($con,($_GET['term'])) . "%' LIMIT 0 ,50"); 
	
	/* Retrieve and store in array the results of the query.*/
	while ($row = mysqli_fetch_array($fetch)) {
		$idproveedor=$row['idCG'];
		$row_array['value'] = $row['dni']." ".$row['ap']." ".$row['am']." ".$row['nom'];
		$row_array['idCG']=$idproveedor;
		$row_array['dni']=$row['dni'];
		$row_array['ap']=$row['ap'];
		$row_array['nom']=$row['nom'];
		array_push($return_arr,$row_array);
    }
	
}

/* Free connection resources. */
mysqli_close($con);

/* Toss back results as json encoded array. */
echo json_encode($return_arr);

}
?>