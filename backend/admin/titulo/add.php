<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';
//require_once('../extra/class.upload.php');

$business = $database->table('tdatos')->first();

extract($_POST);
$logoUrl = $business?->logo;

if($_FILES["img"]["name"] != null){
    /*$handle = new Upload($_FILES["img"]);
	$handle->uploaded;
	$handle->Process("img");
	$logoUrl=$handle->file_dst_name;*/
	
	$fileExtension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
    $logoUrl = time() . '.' . $fileExtension;
    
    move_uploaded_file($_FILES['img']['tmp_name'], 'img/' . $logoUrl);
}

if(is_null($business)){
    $database->table('tdatos')->insert([
        'logo' => $logoUrl,
        'titulo' => $txttitulo,
        'nombreEmpresa' => $txtnombre,
        'siglas' => $txttipo,
        'subnombre' => $txtsub,
        'color' => $txtcolor,
        'comentario' => $txtcomentario,
        'abre' => $txtabre,
        'ruc'=>$txtruc,
        'dnir' => $txtdni,
        'representante' => $txtdatos,
        'direccion' => $txtdireccion,
        'partida' => $txtpartida
    ]);
}else{
    $database->table('tdatos')
    ->where('id', $business->id)
    ->update([
        'logo' => $logoUrl,
        'titulo' => $txttitulo,
        'nombreEmpresa' => $txtnombre,
        'siglas' => $txttipo,
        'subnombre' => $txtsub,
        'color' => $txtcolor,
        'comentario' => $txtcomentario,
        'abre' => $txtabre,
        'ruc'=>$txtruc,
        'dnir' => $txtdni,
        'representante' => $txtdatos,
        'direccion' => $txtdireccion,
        'partida' => $txtpartida
    ]);
}

header('location: ' . $_SERVER['HTTP_REFERER']);



