<?php
namespace kcal;

require "vendor/autoload.php";

use \Symfony\Component\Yaml\Yaml;
use \kcal\KsCalendar;
use \kcal\KsHoliday;
// use \kcal\Availability;

define ('LIB_DIR', 'lib');
define ('DAT_DIR', 'dat');

// require LIB_DIR. '/KsCalendar.php';
// require LIB_DIR. '/KsHoliday.php';
// require LIB_DIR. '/Availability.php';

// $dat_calendar = include(DAT_DIR. '/php/dat_calendar.php');
// $dat_reservation = include(DAT_DIR. '/php/dat_reservation.php');   
// $dat_facility = include(DAT_DIR. '/php/dat_facility.php');
// $dat_holiday = include(DAT_DIR. '/php/dat_holiday.php');

$input = file_get_contents(DAT_DIR . "/calendar.yaml");
$dat_calendar = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/facility.yaml");
$dat_facility = Yaml::parse($input)['facility'];

$input = file_get_contents(DAT_DIR . "/holiday.yaml");
$dat_holiday = Yaml::parse($input);

$input = file_get_contents(DAT_DIR . "/reservation.yaml");
$dat_reservation = Yaml::parse($input);

header('Content-Type: text/plain; charset=UTF-8');
$year =  2023;
$month = 8;
$facility=  12216;
$year  = isset($_GET['y']) ? $_GET['y'] : $year;
$month = isset($_GET['m']) ? $_GET['m'] : $month;
$facility = isset($_GET['f'])  ? $_GET['f'] : $facility;

$hday = new KsHoliday($year, $dat_holiday);
$kcal = new KsCalendar($year, $month);

echo "*** UNIT TESTS ***";

echo "\n\n";
$week = [1,2,5];
$wday = [0,6];
printf("(*) KsCalendar::select([%s], [%s])\n",implode(',',$week),implode(',',$wday),);
echo "================================\n";
$days = $kcal->select($week, $wday);
sort($days);
print_r($days);

echo "(*) KsHoliday::getHolidays():\n";
echo "==========================\n";
$holidays = $hday->getHolidays(); // one year
print_r($holidays);
echo "Total ", count($holidays), "days\n";

echo "\n\n";
echo "(*) KsHoliday::getHolidays(5):\n";
echo "===========================\n";
$holidays = $hday->getHolidays(5); // one month
print_r($holidays);

echo "\n\n";
echo "(*) KsHoliday::queryByname('休日'): [partial martching] \n";
echo "================================\n";
$holidays = $hday->queryByname('休日'); // pattern match
print_r($holidays);

echo "\n\n";
echo "(*) KsHoliday::queryBydate('2-11'): [format inference]\n";
echo "================================\n";
$holidays = $hday->queryBydate('2-11');// figure out '02-11', '0211', '2-11'.  
print_r($holidays);
