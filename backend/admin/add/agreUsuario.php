<?php

require_once ("../conection/bdcredito.php");
//editar
extract($_POST);
include "../extra/class.upload.php";
if(!empty($idue))
{
	$cosul="";
	$txtdirec= strtoupper($txtdirec);

		$handle = new Upload($_FILES["img"]);
		$handle->uploaded;
		$handle->Process("../img2/user/");
		$img=$handle->file_dst_name;
		if(empty($img))
		{
			enviar("UPDATE tusuario SET dniU='$txtdni', apU='$txtap', amU='$txtam', nomU='$txtnom', celU='$txtcel', direcU='$txtdirec', correoU='$txtema', tipoU='$txttipo',idO='$txtofi1', estadoU='$txtestado' where idU='$idue'");
		}
		else
		{
			enviar("UPDATE tusuario SET dniU='$txtdni', apU='$txtap', amU='$txtam', nomU='$txtnom', celU='$txtcel', direcU='$txtdirec', correoU='$txtema', tipoU='$txttipo',idO='$txtofi1', estadoU='$txtestado',img='$img' where idU='$idue'");
		}
}
else
{
	//agregar
	if (!empty($_POST['txtdni'])
		&& !empty($_POST['txtap'])
		&&!empty($_POST['txtam'])
		&&!empty($_POST['txtnom'])
		&&!empty($_POST['txtcel'])
		&&!empty($_POST['txtema'])
		&&!empty($_POST['txtdirec'])
		&&!empty($_POST['txttipo']))
		{
		$handle = new Upload($_FILES["img"]);
		$handle->uploaded;
		$handle->Process("../img2/user/");
		$txtdirec= strtoupper($txtdirec);
			$clave=md5('123456');
			$img=$handle->file_dst_name;
			enviar("INSERT INTO tusuario(dniU, pass, apU, amU, nomU, celU, direcU, correoU, tipoU,idO, estadoU,img)
						VALUES ('$txtdni','$clave','$txtap','$txtam','$txtnom','$txtcel','$txtdirec','$txtema','$txttipo','$txtofi1','$txtestado','$img')");
		}
	}
?>
