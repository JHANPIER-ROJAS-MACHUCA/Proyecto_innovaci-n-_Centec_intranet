<?php
switch (1) {
  case 1:
    $diaPasa="1";
    break;
  case 2:
    $diaPasa="7";
    break;
  case 3:
    $diaPasa="4";

    break;
  case 4:
    $diaPasa="30";
    break;
}
//between cast('" + datocad + "' as date) and cast('" + datocad2 + "' as date)
echo $diaPasa."<br>";
$year_start = strtotime('first day of January', time());
echo date('Y-m-d', $year_start);
echo "<br>";
echo time();
echo "<br>";
$year_end = strtotime('last day of February', time());
echo date('Y-m-d', $year_end);

 ?>
