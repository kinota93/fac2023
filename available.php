<?php
require 'KsuCalendar.php';

header('Content-Type: text/plain; charset=UTF-8');
$year =  2023;
$month = 8;
$facility=  12216;
if (isset($_GET['y'])) $year = $_GET['y'];
if (isset($_GET['m'])) $month = $_GET['m'];
if (isset($_GET['f'])) $facility = $_GET['f'];

$cal = new KsuCalendar($year, $month);
$dates = getAvailability($year, $month,$facility);

$dat_facility = include('dat/php/dat_facilities.php');
echo "facility: ", $facility, "\n";
$fac = $cal->parseFacility($dat_facility, $facility);
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

printf("%d年%d月\n========\n", $cal->year, $cal->month);
$cal->output($dates);

// print_r($cal->getWeekSlice(1));
// print_r($cal->getWeekSlice(4));
// print_r($cal->getWeekSlice(5));

function getAvailability($year, $month, $facility)
{
    $dat_calendar = include('dat/php/dat_calendar.php');
    $dat_reservation = include('dat/php/dat_reservation.php');   
    $cal = new KsuCalendar($year, $month);
    $dates = [];
    if (isset($dat_calendar[$year])){
        $cal_dates = $cal->parseCalendar($dat_calendar[$year]);    
        $rev_dates = $cal->parseReservation($dat_reservation, $facility);
        $dates = $cal_dates;
        foreach ($rev_dates as $d=>$v){
            $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
        }
    } 
    ksort($dates);
    return $dates;
}