<?php
  if(!isset($_COOKIE['user1']))
  {
   echo "<script>location.href='../'</script>";
  }
  include('conection/bdcredito.php');
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTCP | Empty Page</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <link href="../extra/neo.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logocreditos.png"/>
    <!-- probando -->
    <!--<script src="jquery/jquery.min.js"></script>-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!---->
    <link href="../css/plugins/select2/select2.min.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="../fonts/glyphicons-halflings-regular.woff">-->
<!--profile word-->
    <link href="../css/plugins/summernote/summernote.css" rel="stylesheet">
    <link href="../css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
    <link href="../css/plugins/datapicker/datepicker3.css" rel="stylesheet">



</head>