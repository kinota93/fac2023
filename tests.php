<?php
include "lib/KsuCalendar.php";

$dat_holiday = include('dat/php/dat_holiday.php');
header('Content-Type: text/plain; charset=UTF-8');
$year = 2025;
if (is_defined(['y'], $_GET)){
    $year = $_GET['y']; 
}
echo "{$year}年\n";
$holidays = parseHoliday($year, $dat_holiday);
echo 'Total: ', count($holidays), " days\n\n";
foreach ($holidays as $date=>$name){
    echo $date, ': ', $name, "\n";
}
function parseHoliday($year, $dat_holiday){
    $holidays = [];
    $ex_holiday = null;
    foreach ($dat_holiday as $month=>$days){
        $cal = new \kcal\KsuCalendar($year, $month);
        foreach ($days as $d){
            $valid = true;
            if (isset($d['during'])){
                $valid = $valid && during($year, $d['during']);
            }
            if (isset($d['except'])){
                $valid = $valid && !in_array($year, $d['except']);
            }
            if ($valid) {
                $hday = parseDay($cal, $d['day']);
                
                if ($hday > 0){
                    $date = (new \DateTime)->setDate($year, $month, $hday);

                    if ($ex_holiday!=null ){ // for a pending ex. holiday 
                        if ($ex_holiday === $date){
                            $ex_holiday->modify('+1 day');
                        }else{
                            $holidays[$ex_holiday->format('m-d')] = '振替休日';
                            $ex_holiday = null;
                        }
                    }
                    $holidays[$date->format('m-d')] = $d['name'];

                    $wday = $cal->day2wkday($hday);
                    if ($wday === 0) { // set a new ex. hodilday
                        $ex_holiday = (new \DateTime)->setDate($year, $month, $hday +1);
                    }
                    
                }    
            }
        }
    }
    /** 
     * special holiday, a day sandwiched by two holidays
     * */
    $ex_holidays =[];
    $prev_date = null;
    foreach ($holidays as $date=>$name){
        if ($prev_date and sandwiched($prev_date, $date)){
            $middle = mkdate($prev_date, +1);
            $ex_holidays[$middle] = '国民の休日*';
        }
        $prev_date = $date;
    }
    $holidays = array_merge($holidays, $ex_holidays);
    ksort($holidays);

    return $holidays;
}


function sandwiched($date1, $date2)
{
    return $date2 === mkdate($date1, +2);
}
function mkdate ($date, $days=0){
    global $year;
    list ($m, $d) = explode('-', $date);
    $time = mktime(0,0,0,$m, $d+$days, $year);
    return date('m-d',$time);
}
function is_defined($a, $b)
{
    $diff = array_diff($a, array_keys($b));
    return empty($diff); 
}

function during($a, $b)
{
    if (is_scalar($b))
        return $a === $b;
    if (sizeof($b) >= 2)
        return ($b[0] <= $a and $a <= $b[1]);
    return false;
}
function parseDay($cal, $day)
{
    $year = $cal->year;
    if (is_integer($day))
        return $day;
    if (is_array($day))
        return $cal->wkday2day($day[1], $day[0]);
    if ($day==='springEquinox')
        return  equinox($year);
    if ($day==='autumnEquinox')
        return  equinox($year, 'autumn');
    return -1;    
}
/**   year         spring   autumn
    1851 - 1899   19.8277   22.2588
    1900 - 1979   20.8357   23.2588
    1980 - 2099   20.8431   23.2488
    2100 - 2150   21.8510   24.2488
 */
function equinox($year, $season='spring'){
    if (!during($year, [1851, 2150])){
        return -1;
    }
    $alpha = [20.8431, 23.2488];
    if (during($year, [1851, 1899]))
        $alpha = [19.8277, 22.2588];
    if (during($year, [1900, 1979]))
        $alpha = [20.8357, 23.2588];
    if (during($year, [2100, 2150]))
        $alpha = [21.8510, 24.2488];
    
    $beta = ($season=='spring') ? $alpha[0] : $alpha[1];

    return floor($beta + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
} 

