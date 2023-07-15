<?php
namespace kcal;

require "vendor/autoload.php";

use \Symfony\Component\Yaml\Yaml;
use \kcal\KsCalendar;
use \kcal\KsHoliday;
use \kcal\Availability;

define ('LIB_DIR', 'lib');
define ('DAT_DIR', 'dat');

// require LIB_DIR . '/KsCalendar.php';
// require LIB_DIR . '/KsHoliday.php';
// require LIB_DIR . '/Availability.php';

// $dat_calendar = include(DAT_DIR . '/php/dat_calendar.php');
// $dat_facility = include(DAT_DIR . '/php/dat_facility.php');
// $dat_holiday = include(DAT_DIR . '/php/dat_holiday.php');
// $dat_reservation = include(DAT_DIR . '/php/dat_reservation.php');   

$input = file_get_contents(DAT_DIR . "/calendar.yaml");
$dat_calendar = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/facility.yaml");
$dat_facility = Yaml::parse($input)['facility'];

$input = file_get_contents(DAT_DIR . "/holiday.yaml");
$dat_holiday = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/reservation.yaml");
$dat_reservation = Yaml::parse($input);

header('Content-Type: text/plain; charset=UTF-8');
$year  = isset($_GET['y']) ? $_GET['y'] : 2023;
$month = isset($_GET['m']) ? $_GET['m'] :8;
$facility = isset($_GET['f'])  ? $_GET['f'] : 12216;


$hday = new KsHoliday($year, $dat_holiday );
$kcal = new KsCalendar($year, $month);
$avil = new Availability($kcal, $hday, $facility);

$fac = $avil->parseFacility($dat_facility);

echo "facility: ", $facility, "\n";
if ($fac){    
    if (isset($fac['time'])){
        echo "time: " , $fac['time'], "\n";
    }
    if (isset($fac['timeslots'])){
        echo "timeslots: " , $fac['timeslots'], "\n";
    }
}else{
    echo "no such facility\n";
}
echo "\n";

printf("%d年%d月\n=========\n", $kcal->year, $kcal->month);

$dates = $avil->getAvailability($dat_calendar, $dat_reservation);
$avil->output($dates);
