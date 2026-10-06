<?php
include('../conection/bdcredito.php');
$fecha=date('Y-m-d');
$idU= $_COOKIE['user1'];
$da=extraer("SELECT b200, b100, b50, b20, b10, m5, m2, m1, m05, m02, m01, total,estado FROM tbilletaje where idU='$idU' and fecha='$fecha' order by idBille desc limit 1");
$r=mysqli_fetch_array($da);

echo json_encode(array("b200"=>$r['b200'],"b100"=>$r['b100'],"b50"=>$r['b50'],"b20"=>$r['b20'],"b10"=>$r['b10'],"b5"=>$r['m5'],"b2"=>$r['m2'],"b1"=>$r['m1'],"b05"=>$r['m05'],"b02"=>$r['m02'],"b01"=>$r['m01'],"total"=>$r['total'],"estado"=>$r['estado']));

?>
