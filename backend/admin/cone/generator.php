<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php $valor="";
    function generar($cade=10)
    {
      return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,$cade);
    }
    echo generar();
    //segundo
    //cadenasd
    //=>0123456789abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ
    function gene($cade=10)
    {
      $cadena='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $c=strlen($cadena);
      $resul="";
      for ($i=0; $i < $cade; $i++)
      {
        $resul.=$cadena[rand(0,$c-1)];
      }
      return $resul;
    }
    echo "<br>";
    echo gene();
     ?>
  </body>
</html>
