<?php
  include("../conection/bdcredito.php");

  $identificador=$_REQUEST['identi'];

  extract($_POST);
    $fe13 = date("Y-m-d",strtotime($Afecha));
    if($AfechaP!="")
    {
      $fe=preg_split("~/~", $AfechaP);
      $Afecha=$fe['2'].'/'.$fe['1'].'/'.$fe['0'];
      //convertimos de cadena a tipo date
      $fe13 = date("Y-m-d",strtotime($Afecha));
    }

  //luz
  if($txtluz==""){$txtluz="0";}else if($txtluz=="on"){$txtluz="1";}
  //contarto de alquiler
  if($txtalqui==""){$txtalqui="0";}else if($txtalqui=="on"){$txtalqui="1";}
  //Fotos
  if($txtfoto==""){$txtfoto="0";}else if($txtfoto=="on"){$txtfoto="1";}
  //Documentos******
  //fwchA

  //obtener id de clientes
  function cliente($dni,$ap,$am,$nom)
  {
    $ide="";
    $deat=extraer("select count(*) as dato,idCG as id from tclie_general where dni='$dni'");
    $row=mysqli_fetch_array($deat);
    if($row['dato']=="0")
    {
      enviar("INSERT INTO tclie_general (dni, ap, am,nom) values ('$dni','$ap','$am','$nom')");
      $deat1=extraer("select idCG as id from tclie_general where dni='$dni'");
      $row1=mysqli_fetch_array($deat1);
      $ide=$row1['id'];
    }
    else
    {
      $ide=$row['id'];
    }
    return $ide;
  }
  //agreagr nac y sexo a conyugue
  $idCon="";
  $idAval="";
  if(!empty($txtdni))
  {
    //agregar conyugue
    $idCon=cliente($txtdni,$txtap,$txtam,$txtnom);
    enviar("UPDATE tclie_general SET fec_nac='$Afecha', sexo='$sexo' where idCG='$idCon'");
  }
  if(!empty($txtdni1))
  {
    //agregar AVAL
    $idAval=cliente($txtdni1,$txtap1,$txtam1,$txtnom1);
    $come="Ocupación: ".$txtocu.", direccion de centro de Trabajo: ".$txtdirecTra;
    enviar("UPDATE tclie_general SET direc='$txtdirec',cel='$txtcel',comentario='$come' where idCG='$idAval'");
 }
  //***********vinculacion

  $consulta="";
  $consulta1="";

  $consulta.="titular";
  $consulta1.="'".$identificador."'";
  if($txtdni!="")
  {
    $consulta.=",conyugue";
    $consulta1.=",'".$idCon."'";
    $dato=moverDoc('img',$identificador);

    $consulta.=",croquisT";
    $consulta1.=",'".$dato."'";

    $dato1=moverDoc('img2',$identificador);
    $consulta.=",croquisC";
    $consulta1.=",'".$dato1."'";
  }
  if($txtdni1!="")
  {
    $consulta.=",aval";
    $consulta1.=",'".$idAval."'";

    $dato2=moverDoc('img4',$identificador);
    $consulta.=",croquisA";
    $consulta1.=",'".$dato2."'";
  }
  function moverDoc($nw,$ide)
    {
      $doc="";
    /*  $nombre_file = $ide.'_'.mktime();
      $posicion = 0;
      $origen = $_FILES[$nomw]['tmp_name'];
      $destino = "../documentPre/$nombre_file";
      move_uploaded_file($origen,$destino);
      $doc=$nombre_file;
      return $doc;
      */
      $ruta_nueva="../documentPre/".$ide."_".mktime().$_FILES[$nw]['name'];//[$i];
      $ruta_temporal=$_FILES[$nw]['tmp_name'];//[$i];
	    move_uploaded_file($ruta_temporal,$ruta_nueva);
      $doc=$ide."_".mktime().$_FILES[$nw]['name'];
      return $doc;
    }
  //docus//

  enviar("INSERT INTO tvinculacion ($consulta) VALUES ($consulta1)");
  //obtenemos el id de tvinculacion
  $idvincu=extraer("SELECT idV from tvinculacion where titular='$identificador' order by idV desc limit 1");
  $row=mysqli_fetch_array($idvincu);
  $idvin=$row['idV'];
///*******Documentos

  $docu12=moverDoc('txtdocus',$identificador);
  $cosulDoc="";
  $cosulDoc1="";

  $cosulDoc.="reciboLuz";//, documentos";
  $cosulDoc.=",alquilerLocal";
  $cosulDoc.=",fotos,documentos";

  $cosulDoc1.="'".$txtluz."'";
  $cosulDoc1.=",'".$txtalqui."'";
  $cosulDoc1.=",'".$txtfoto."','".$docu12."'";
  if(!empty($txtdecrip1))
  {
    $cosulDoc.=",descripcion";
    $cosulDoc1.=",'".$txtdecrip1."'";
  }

  enviar("INSERT INTO tpres_doc ($cosulDoc) values ($cosulDoc1)");
  $asd=extraer("SELECT idCDOC FROM tpres_doc where documentos='$docu12' order by idCDOC desc limit 1");
  $rw=mysqli_fetch_array($asd);
  $idDocM=$rw['idCDOC'];

  //obtener dias pasados
  $diaPasa="0";

  switch ($txtpago) {
    case 1:
      $diaPasa="1";
      break;
    case 2:
      $diaPasa="7";
      break;
    case 3:
      $diaPasa="1";
      break;
    case 4:
      $diaPasa="30";
      break;
  }
  //crear prestamo

  $user_id = isset($_POST['user_id']) && !empty($_POST['user_id']) ? $_POST['user_id'] : null;
  $started_at = isset($_POST['started_at']) && !empty($_POST['started_at']) ? $_POST['started_at'] : null;

  enviar("INSERT INTO tprestamo (idCG,idV,idDOC,montoPropuesto,cuota,taza,pago,plazo,n_cuota,diasPasados,estado,tipoP,mora,fechaTermino, user_id, started_at) VALUES ('$identificador','$idvin','$idDocM','$txtmonto','$txtcuotaf1','$txtinteres','$txtpago','$txtplazo','$txtplazo','$diaPasa','1','$txttipoPresta','$txtmora','$fe13', $user_id, $started_at)");
 ?>
