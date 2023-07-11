<?php

use kcal\KsuCalendar;

require_once 'lib/KsuCalendar.php';
require_once 'lib/Availability.php';

header('Content-Type: text/plain; charset=UTF-8');
$year =  2023;
$month = 8;
$facility=  12216;
$year  = isset($_GET['y']) ? $_GET['y'] : $year;
$month = isset($_GET['m']) ? $_GET['m'] : $month;
$facility = isset($_GET['f'])  ? $_GET['f'] : $facility;

$dat_calendar = include('dat/php/dat_calendar.php');
$dat_reservation = include('dat/php/dat_reservation.php');   
$dat_facility = include('dat/php/dat_facilities.php');

$cal = new kcal\KsuCalendar($year, $month);
$avl = new kcal\Availability($cal, $facility);

$fac = $avl->parseFacility($dat_facility);

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

printf("%d年%d月\n=========\n", $cal->year, $cal->month);
$dates = $avl->getAvailability($dat_calendar, $dat_reservation);
$avl->output($dates);


echo "\n\n";
echo "Test filter(): \n";
echo "=============\n";
$week = [1,2,5];
$wday = [0,6];
printf("filter(\$week=[%s],\$wday=[%s])\n", implode(',', $week),implode(',', $wday));
$days = $cal->filter($week, $wday);
sort($days);
print_r($days);
