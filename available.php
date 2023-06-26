<?php
require_once 'lib/KsuCalendar.php';
require_once 'lib/Availability.php';

header('Content-Type: text/plain; charset=UTF-8');
$year =  2023;
$month = 8;
$facility=  12216;
if (isset($_GET['y'])) $year = $_GET['y'];
if (isset($_GET['m'])) $month = $_GET['m'];
if (isset($_GET['f'])) $facility = $_GET['f'];

$kcal = new kcal\Availability($year, $month, $facility);
$dat_calendar = include('dat/php/dat_calendar.php');
$dat_reservation = include('dat/php/dat_reservation.php');   
$dat_facility = include('dat/php/dat_facilities.php');
$dates = $kcal->getAvailability($dat_calendar, $dat_reservation);

echo "facility: ", $facility, "\n";
$fac = $kcal->parseFacility($dat_facility, $facility);
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

printf("%d年%d月\n========\n", $kcal->cal->year, $kcal->cal->month);
$kcal->output($dates);

$days = $kcal->cal->slice([1,2,5]);
sort($days);
print_r($days);