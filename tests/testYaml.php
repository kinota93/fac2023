<?php 
require "../vendor/autoload.php";
use \Symfony\Component\Yaml\Yaml;

const DAT_DIR = '../dat';

header('Content-Type: text/plain; charset=UTF-8');

echo "*** UNIT TESTS (Symfony/Yaml)***\n";

echo "\n\n";
echo "=======Yaml::parse('dat/holiday.yaml')============\n";
$input = file_get_contents(DAT_DIR . "/calendar.yaml");
$dat_calendar = Yaml::parse($input);
echo json_encode($dat_calendar , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 

echo "\n\n";
echo "=======Yaml::parseFile('dat/holiday.yaml')============\n";
$dat_holiday = Yaml::parseFile(DAT_DIR . "/calendar.yaml");
echo json_encode($dat_holiday, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 

echo "\n\n";
echo "=======Yaml::parseFile('dat/facility.yaml')============\n";
$dat_facility = Yaml::parseFile(DAT_DIR . "/facility.yaml");
echo json_encode($dat_facility, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 

echo "\n\n";
echo "=======Yaml::parseFile('dat/reservation.yaml')============\n";
$dat_reservation = Yaml::parseFile(DAT_DIR . "/reservation.yaml");
echo json_encode($dat_reservation, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
