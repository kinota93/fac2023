<?php
include "lib/KsuCalendar.php";

$def = include('dat/php/dat_holiday.php');
header('Content-Type: text/plain; charset=UTF-8');
$year = 2025;
echo "{$year}年\n";
$holiday = [];
$extra_holiday = null;
foreach ($def as $month=>$days){
    $cal = new \kcal\KsuCalendar($year, $month);
    foreach ($days as $d){
        $valid = true;
        if (isset($d['range'])){
            $valid = $valid && in_range($year, $d['range']);
        }
        if (isset($d['except'])){
            $valid = $valid && !in_array($year, $d['except']);
        }
        if ($valid) {
            $hday = check($cal, $d['day']);
            $date = $month .'-'.$hday;
            if ($extra_holiday!=null ){
                $extra_date = $extra_holiday->format('n-j');
                if ($extra_date===$date){
                    $extra_holiday->modify('+1 day');
                }else{
                    $holiday[$extra_date] = '振替休日';
                    $extra_holiday = null;
                }
            }
            if ($hday > 0){
                $wday = $cal->day2wkday($hday);
                if ($wday===0) $extra_holiday = (new \DateTime)->setDate($year, $month, $hday +1);
                $holiday[$date] = $d['name'];
            }
        }
    }
}

foreach ($holiday as $date=>$name){
    echo $date, ':', $name, "\n";
}

function all_exist($a, $b){
    $diff = array_diff($a, array_keys($b));
    return empty($diff); 
}

function in_range($a, $b){
    if (is_scalar($b))
        return $a === $b;
    if (sizeof($b) >= 2)
        return ($b[0] <= $a and $a <= $b[1]);
    return false;
}
function check($cal, $day){
    $year = $cal->year;
    if (is_integer($day))
        return $day;
    if (is_array($day) and all_exist(['week','wday'], $day))
        return $cal->wkday2day($day['wday'], $day['week']);
    if ($day==='autumnEquinox')
        return  autumnEquinox($year);
    if ($day==='springEquinox')
        return  springEquinox($year);
    return -1;    
}
/**            spring   autumn
1851 - 1899   19.8277   22.2588
1900 - 1979   20.8357   23.2588
1980 - 2099   20.8431   23.2488
2100 - 2150   21.8510   24.2488
 */
function springEquinox($year){
    if (!in_range($year, [1851,2150])){
        return -1;
    }
    $alpha = 20.8431;
    if (in_range($year, [1851, 1899]))
        $alpha = 19.8277;
    if (in_range($year, [1900, 1979]))
        $alpha = 20.8357;
    if (in_range($year, [2100, 2150]))
        $alpha = 21.8510;

    return floor($alpha + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
} 
function autumnEquinox($year){
    if (!in_range($year, [1851,2150])){
        return -1;
    }
    $alpha = 23.2488;
    if (in_range($year, [1851, 1899]))
        $alpha = 22.2588;
    if (in_range($year, [1900, 1979]))
        $alpha = 23.2588;
    if (in_range($year, [2100, 2150]))
        $alpha = 24.2488;
      
    return floor($alpha + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
}

