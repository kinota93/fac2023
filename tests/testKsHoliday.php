<?php
namespace kcal;

require "../vendor/autoload.php";
use \kcal\KsHoliday;
use \kcal\KsCalendar;

include 'data_input.php';

$year  = isset($_GET['y']) ? $_GET['y'] : date('Y');
$month = isset($_GET['m']) ? $_GET['m'] : date('n');

$hday = new KsHoliday($year, $dat_holiday);
$kcal = new KsCalendar($year, $month);

header('Content-Type: text/plain; charset=UTF-8');

echo "*** UNIT TESTS (KsHoliday) ***\n\n";

echo "year = ", $year, ", month = ", $month, "\n\n";
echo $kcal;

echo "\n\n";
echo "(*) KsHoliday::getHolidays():\n";
echo "==========================\n";
$holidays = $hday->getHolidays(); // one year
print_r($holidays);
echo "Total ", count($holidays), " days\n";

echo "\n";
echo "(*) KsHoliday::getHolidays({$month}):\n";
echo "===========================\n";
$holidays = $hday->getHolidays($month); // one month
print_r($holidays);

echo "\n";
echo "(*) KsHoliday::queryByname('休日'): [partial martching] \n";
echo "================================\n";
$holidays = $hday->queryByname('休日'); // pattern match
print_r($holidays);

echo "\n";
echo "(*) KsHoliday::queryBydate('2-11'): [format inference]\n";
echo "================================\n";
$holidays = $hday->queryBydate('2-11');// figure out '02-11', '0211', '2-11'.  
print_r($holidays);
