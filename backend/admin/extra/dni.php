<?php
  extract($_POST);
  ini_set("allow_url_fopen", 1);
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  //$codi="71102174";
	$ap="";$am="";$nom="";
  $codi=$txtdni;
   $url="https://api.reniec.cloud/dni/".$codi;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL,$url);
   curl_setopt($ch, CURLOPT_POST, TRUE);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $remote_server_output = curl_exec ($ch);
   // cerramos la sesión cURL
   curl_close ($ch);
   $datos=$remote_server_output;
   //===cambio de especiales//
   function eldespertar($valor)
   {
     $conte=array(['&Uuml;','Ü'],['&Ntilde;','Ñ'],['&ntilde;','ñ'],['&Aacute;','Á'],['&Eacute;','É'],['&Iacute;','Í'],['&uuml;','ü'],['&Oacute;','Ó'],['&Uacute;','Ú'],['&aacute;','á'],['&eacute;','é'],['&iacute;','í'],['&oacute;','ó'],['&uacute;','ú']);
     for ($i=0; $i < count($conte); $i++)
     {
       $valor=str_replace($conte[$i][0], $conte[$i][1],$valor);
     }
     return $valor;
   }
   //=====================
   $datos=eldespertar($datos);
   $cadena="";
   $datos=substr($datos,2,-2);
   //==quitar los espacios en blanco
   function corregir($cadena)
   {
     $dato=preg_split("~:~",$cadena);
     $valor=substr($dato[1],2,-1);
     return $valor;
   }
   //==============================
   $juve=preg_split("~,~",$datos);
   $conteo=0;
   $drag;
   for ($i=2; $i < count($juve); $i++)
   {
     $drag[$conteo]=corregir($juve[$i]);
     $conteo++;
   }
   $ap=$drag[0];$am=$drag[1];$nom=$drag[2];

   echo $nom."|".$ap."|".$am;
?>
