<?php
namespace kcal;

require "../vendor/autoload.php";
use \Symfony\Component\Yaml\Yaml;

const DAT_DIR = '../dat';

$input = file_get_contents(DAT_DIR . "/calendar.yaml");
$dat_calendar = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/facility.yaml");
$dat_facility = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/holiday.yaml");
$dat_holiday = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/reservation.yaml");
$dat_reservation = Yaml::parse($input);
