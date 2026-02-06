<?php
require 'forecast_calc.php';

$forecast = calculateForecasts();
echo "<pre>";
print_r($forecast);
echo "</pre>";
?>
