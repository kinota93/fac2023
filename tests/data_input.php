<?php
namespace kcal;

require "../vendor/autoload.php";
use \Symfony\Component\Yaml\Yaml;

const DAT_DIR = '../dat';

// $input = file_get_contents(DAT_DIR . "/calendar.yaml");
// $dat_calendar = Yaml::parse($input);

$dat_calendar = Yaml::parseFile(DAT_DIR . "/calendar.yaml");
$dat_facility = Yaml::parseFile(DAT_DIR . "/facility.yaml");
$dat_holiday = Yaml::parseFile(DAT_DIR . "/holiday.yaml");
$dat_reservation = Yaml::parseFile(DAT_DIR . "/reservation.yaml");