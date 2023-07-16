<?php
namespace kcal;

require "../vendor/autoload.php";
use \kcal\KsCalendar;

include 'data_input.php';

$year  = isset($_GET['y']) ? $_GET['y'] : date('Y');
$month = isset($_GET['m']) ? $_GET['m'] : date('n');

$kcal = new KsCalendar($year, $month);

header('Content-Type: text/plain; charset=UTF-8');

echo "*** UNIT TESTS (KsCalendar) ***\n\n";

echo "year = ", $year, ", month = ", $month, "\n\n";
echo $kcal, "\n\n";

$week = [1,2,5];
$wday = [0,6];

printf("(*) KsCalendar::select([%s], [%s])\n",implode(',',$week),implode(',',$wday),);
echo "================================\n";
$days = $kcal->select($week, $wday);
sort($days);
print_r($days);

echo "\n\n";
printf("(*) KsCalendar::select([%s], [%s], KsCalendar::PREFER_TO_WDAY)\n", implode(',',$week),implode(',',$wday),);
echo "================================\n";
$days = $kcal->select($week, $wday, KsCalendar::PREFER_TO_WDAY);
sort($days);
print_r($days);

echo "\n\n";
printf("(*) KsCalendar::select([%s], [%s], KsCalendar::PREFER_TO_WEEK)\n", implode(',',$week),implode(',',$wday),);
echo "================================\n";
$days = $kcal->select($week, $wday, KsCalendar::PREFER_TO_WEEK);
sort($days);
print_r($days);
