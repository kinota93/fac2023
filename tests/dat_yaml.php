<?php 
require "../vendor/autoload.php";
use \Symfony\Component\Yaml\Yaml;

define ('DAT_DIR', '../dat');

header('Content-Type: text/plain; charset=UTF-8');

echo "=======dat/holiday.yaml============\n";
$input = file_get_contents(DAT_DIR . "/holiday.yaml");
$result = Yaml::parse($input);
print_r($result); //var_dump($result);

echo "\n\n";
echo "=======dat/calendar.yaml============\n";
$input = file_get_contents(DAT_DIR . "/calendar.yaml");
$result = Yaml::parse($input);
print_r($result); //var_dump($result);

echo "\n\n";
echo "=======dat/facility.yaml============\n";
$input = file_get_contents(DAT_DIR . "/facility.yaml");
$result = Yaml::parse($input);
print_r($result); //var_dump($result);

echo "\n\n";
echo "=======dat/reservation.yaml============\n";
$input = file_get_contents(DAT_DIR . "/reservation.yaml");
$result = Yaml::parse($input);
print_r($result); //var_dump($result);