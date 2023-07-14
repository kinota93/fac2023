<?php

use kcal\KsuCalendar;

require 'lib/KsuCalendar.php';
require 'lib/Holiday.php';
require 'lib/Availability.php';

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
$dat_holiday = include('dat/php/dat_holiday.php');

$h = new kcal\Holiday($year, $dat_holiday );
$cal = new kcal\KsuCalendar($year, $month);
$avl = new kcal\Availability($cal,$h, $facility);

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
echo "*** UNIT TESTS ***\n\n";
echo "(*) Holiday::getHolidays():\n";
echo "==========================\n";
$holidays = $h->getHolidays(); // one year
print_r($holidays);
echo "Total ", count($holidays), "days\n";

echo "\n\n";
echo "(*) Holiday::getHolidays(5):\n";
echo "===========================\n";
$holidays = $h->getHolidays(5); // one month
print_r($holidays);

echo "\n\n";
echo "(*) Holiday::queryByname('休日'): [partial martching] \n";
echo "================================\n";
$holidays = $h->queryByname('休日'); // pattern match
print_r($holidays);

echo "\n\n";
echo "(*) Holiday::queryBydate('2-11'): [format inference]\n";
echo "================================\n";
$holidays = $h->queryBydate('2-11');// figure out '02-11', '0211', '2-11'.  
print_r($holidays);

echo "\n\n";
$week = [1,2,5];
$wday = [0,6];
printf("(*) KsuCalendar::select([%s], [%s])\n",implode(',',$week),implode(',',$wday),);
echo "================================\n";
$days = $cal->select($week, $wday);
sort($days);
print_r($days);
