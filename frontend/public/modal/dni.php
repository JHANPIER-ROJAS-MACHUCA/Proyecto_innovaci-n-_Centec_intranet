<?php
  $codi=$_REQUEST['txtdni'];
	$ap="";$am="";$nom="";
  $url="http://aplicaciones007.jne.gob.pe/srop_publico/Consulta/Afiliado/GetNombresCiudadano?DNI=".$codi;
   $json=file_get_contents($url);
   //$datos=json_encode($json,true);
   $datos=$json;
   $cadena="";
   $cadena=str_replace("|",",",$datos);
   //$cadena= substr($cadena,1);
   //$cadena=substr($cadena,0,-1);
   $juve = preg_split("~,~", $cadena);
   $ap=$juve[0];$am=$juve[1];$nom=$juve[2];
	echo json_encode(array("nom"=>"$nom", "ap"=>"$ap", "am"=>"$am"));
?>
