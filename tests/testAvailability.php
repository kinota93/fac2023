<?php
namespace kcal;

require "../vendor/autoload.php";
use \kcal\KsCalendar;
use \kcal\KsHoliday;
use \kcal\Availability;

include 'data_input.php';

$year  = isset($_GET['y']) ? $_GET['y'] : date('Y');
$month = isset($_GET['m']) ? $_GET['m'] : date('n');
$facility = isset($_GET['f'])  ? $_GET['f'] : 12216;

$hday = new KsHoliday($year, $dat_holiday );
$kcal = new KsCalendar($year, $month);
$avil = new Availability($kcal, $hday, $facility);

$fac = $avil->parseFacility($dat_facility);

header('Content-Type: text/plain; charset=UTF-8');

echo "*** UNIT TESTS (Availability) ***\n\n";

echo "year= ", $year, ", month = ", $month, "\n\n";
echo $kcal,  "\n\n";

echo "======\n";
echo "facility: ", $facility, "\n";
if ($fac){    
    if (isset($fac['time'])){
        echo "time: " , $fac['time'], "\n";
    }
    if (isset($fac['timeslots'])){
        echo "timeslots: " , $fac['timeslots'], "\n";
    }
    echo "capacity: " , $fac['capacity'], " / day\n";
}else{
    echo "no such facility\n";
}

echo "\n";
echo "======\n";
$dates = $avil->getAvailability($dat_calendar, $dat_reservation);
$avil->output($dates);
