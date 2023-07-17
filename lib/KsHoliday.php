<?php

namespace kcal;

/*
 * Created on Sat Jul 15 2023
 *
 * Copyright (c) 2023 Your Company
 */

use kcal\KsCalendar;
use kcal\KsDateTime;

use Exception;
use function array_filter;
use function array_diff;
use function in_array;
use function is_array;
use function sprintf;
use function preg_match;
use function explode;
use function substr;
use function trim;
use function mktime;
use function floor;

class KsHoliday
{
    public $year;
    public $holidays;

    private const LAW_ENFORCEMENT_YEAR = 1948;
    private const SUPPLEMENTARY_HOLIDAY = '振替休日';
    private const ADDITIONAL_HOLIDAY = '国民の休日';
    
    // Valid keys in holiday definition
    private const NAME = 'name'; // holiday name
    private const DAY = 'day';   // day definition
    private const FOR = 'for';  // valid for a period
    private const IN = 'in';    // valid in some years
    private const EXCEPT = 'except'; // valid except some years

    private const DATE_FORMAT ='m-d'; // '01-07' for January 7 
    
    public function __construct($year, $dat_holiday)
    {
        $this->year = $year;
        $this->holidays = $this->parseHolidays($dat_holiday);
    }
    
    /** get holidays of one month or a whole year (default)*/
    public function getHolidays($month = 0)
    {
        if ($month == 0) return $this->holidays;
        
        return array_filter($this->holidays, function ($x) use($month) {
                return (int)substr($x, 0, 2) === (int)$month;
            }, ARRAY_FILTER_USE_KEY);
    }
   

    /** query by name, support pattern matching  */
    public function queryByname($name){
        return array_filter($this->holidays, function($v) use($name){
            return preg_match("/{$name}/", $v);
        });
    }

    /** query by date, support date format inference */
    public function queryBydate($date){
        $date = $this->mkdate($date);
        return array_filter($this->holidays, function($v) use($date){
            return $v === trim($date);
        }, ARRAY_FILTER_USE_KEY);                
    }

    /** check if there is exact one day between 2 dates, 
     * e.g., sandwiched('03-31', '05-02')
    */
    function sandwiched($date1, $date2)
    {
        return $date2 === $this->mkdate($date1, +2);
    }

    /** Normalize date format while shifting back/forth some days 
     * e.g., mkdate('2-14', 3) => '02-17', mkdate('3-31',2) => '04-02'
    */
    function mkdate ($date, $days = 0)
    {
        if (preg_match('/^[0-9]+-[0-9]+$/', $date)){
            list ($m, $d) = explode('-', $date);
        }elseif (preg_match('/^[0-9]{4}$/', $date)){
            $m = substr($date, 0, 2);
            $d = substr($date, 2, 2);
        }
        if (!isset($m, $d)) {
            throw new Exception('Invalid date format!');
        }
        $time = mktime(0, 0, 0, $m, $d + $days, $this->year);
        return date(self::DATE_FORMAT, $time);
    }

    /** check if all elements of array $a are defined keys of array $b, 
     * e.g. is_defined(['tom','bob'], ['bob'=>23, 'abe'=>35, 'tom'=>56]) => TRUE
    */
    function is_defined($a, $b)
    {
        $diff = array_diff($a, array_keys($b));
        return empty($diff); 
    }

    /** check if $a is in range defeined by array $b, 
     * e.g. during(3, [2,4]) => TRUE  
    */
    function during($a, $b)
    {
        if (is_scalar($b))
            return $a === $b;
        if (sizeof($b) >= 2)
            return ($b[0] <= $a and $a <= $b[1]);
        return false;
    }

    /** parse the day definition: (1) a day, (2) a weekday, (3) others   
    */
    function parseDay($month, $day)
    {
        $cal = new KsCalendar($this->year, $month);
        if (is_integer($day))
            return $day;
        if (is_array($day))
            return $cal->w2d($day[1], $day[0]);
        if ($day==='springEquinox')
            return  $this->equinox('spring');
        if ($day==='autumnEquinox')
            return  $this->equinox('autumn');
        return -1;    
    }

    /** caculate spring and autumn equinox days  
     *  valid for years between 1851 and 2150. return -1 otherwise   
    */
    function equinox($season='spring'){
        $year = $this->year;

        if (!$this->during($year, [1851, 2150])){
            return -1;
        }
        
        $delta = [20.8431, 23.2488];
        if ($this->during($year, [1851, 1899]))
            $delta = [19.8277, 22.2588];
        if ($this->during($year, [1900, 1979]))
            $delta = [20.8357, 23.2588];
        if ($this->during($year, [2100, 2150]))
            $delta = [21.8510, 24.2488];
        
        $alpha = ($season=='spring') ? $delta[0] : $delta[1];
        return floor($alpha + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    } 

    /** check if the holiday definition is valid for this year  */
    private function validateYear($day)
    {
        $valid = true;
        if (isset($day[self::FOR])){
            $valid = $valid && $this->during($this->year, $day[self::FOR]);
        }
        if (isset($day[self::EXCEPT])){
            $valid = $valid && !in_array($this->year, $day[self::EXCEPT]);
        }
        if (isset($day[self::IN])){
            $valid = $valid && in_array($this->year, $day[self::IN]);
        }
        return $valid;
    }

    /** parse holiday definictions and geerate holidays*/
    private function parseHolidays($dat_holiday)
    {
        $holidays = [];
        if ($this->year < self::LAW_ENFORCEMENT_YEAR){
            return $holidays;
        }
        $sp_holiday = null; // supplementary holiday for coincident holidays
        foreach ($dat_holiday as $_month=>$_days){
            foreach ($_days as $d){
                if (! $this->validateYear($d)){
                    continue;
                } 
                $hday = $this->parseDay($_month, $d[self::DAY]);                    
                if ($hday > 0){
                    $date = (new KsDateTime)->setDate($this->year, $_month, $hday);
                    if ($sp_holiday != null ){ // for a pending ex_holiday 
                        if ($sp_holiday === $date){
                            $sp_holiday->modify('+1 day');
                        }else{
                            $holidays[$sp_holiday->format(self::DATE_FORMAT)] = self::SUPPLEMENTARY_HOLIDAY;
                            $sp_holiday = null;
                        }
                    }
                    $holidays[$date->format(self::DATE_FORMAT)] = $d[self::NAME];
                    $cal = new KsCalendar($this->year, $_month);   
                    $wday = $cal->d2w($hday);
                    if ($wday === 0) { // prepare a new ex_hodilday
                        $sp_holiday = (new KsDateTime)->setDate($this->year, $_month, $hday +1);
                    }                        
                }    
            }
        }
        ksort($holidays);
        
        $ex_holidays =[]; // additional holiday, a normal day sandwiched by two holidays
        $prev_date = null;
        foreach ($holidays as $date=>$name){
            if ($prev_date and $this->sandwiched($prev_date, $date)){
                $middle = $this->mkdate($prev_date, +1);
                $ex_holidays[$middle] = self::ADDITIONAL_HOLIDAY;
            }
            $prev_date = $date;
        }
        $holidays = array_merge($holidays, $ex_holidays);
        ksort($holidays);      
        return $holidays;
    }
}