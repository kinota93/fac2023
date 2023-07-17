<?php

namespace kcal;

require "../vendor/autoload.php";
use \kcal\KsDateTime;

$dt = new KsDateTime('1965/3/18 16:10');

header('Content-Type: text/plain; charset=UTF-8');

echo "*** UNIT TESTS (KsDateTime) ***\n";

echo "\n";
echo "====Help===========================================================\n";
echo KsDateTime::help();
echo "\n";
echo "===================================================================\n";

echo "\n\n";
echo "format: default, call `__toString()`\n";
echo "output : ", $dt;

echo "\n\n";
$format = 'Y年n月j日(b)';
echo "format : '{$format}'\n";
echo "output : ", $dt->format($format); // 昭和40年3月18日(木)

echo "\n\n";
$format = 'JK年n月j日(b) Eg:i';
echo "format : '{$format}' \n";
echo "output : ", $dt->format($format); // 昭和40年3月18日(木) 午後4:10

echo "\n\n";
$format = 'Q年度n月j日(b) Eg:i';
echo "format : '{$format}' \n";
echo "output : ", $dt->format($format); 

echo "\n\n";
$format = 'Jq年度n月j日(b) Eg:i';
echo "format : '{$format}' \n";
echo "output : ", $dt->format($format); 

echo "\n\n";
$format = 'Rk.m.d';
echo "format : '{$format}' \n";
echo "output : ", $dt->format($format); 

echo "\n\n";
$format = 'Y年n月j日(b)\j\K';
echo "format : '{$format}'\n";
echo "output : ", $dt->format($format); // 昭和40年3月18日(月)jK 





