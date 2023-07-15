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

// require_once 'KsCalendar.php';

class KsHoliday
{
    public $year;
    public $holidays;
    
    public function __construct($year, $dat_holiday)
    {
        $this->year = $year;
        $this->holidays = $this->_parseHolidays($dat_holiday);
    }
    
    /** get holidays of one month or a whole year (default)*/
    public function getHolidays($month = 0)
    {
        if ($month == 0) return $this->holidays;
        
        return array_filter($this->holidays, function ($x) use($month) {
                return (int)substr($x, 0, 2) === $month;
            }, ARRAY_FILTER_USE_KEY);
    }
   

    /** query by name, support pattern matching  */
    public function queryByname($name){
        return array_filter($this->holidays,function($v) use($name){
            return preg_match("/{$name}/", $v);
        });
    }

    /** query by date, guess date format */
    public function queryBydate($date){
        $date = self::guessDate($date);
        return array_filter($this->holidays,function($v) use($date){
            return $v === trim($date);
        }, ARRAY_FILTER_USE_KEY);                
    }

    /** guess date format, eg. '02-11', '0211', '2-11' all the same   */
    static function guessDate($date){
        if (preg_match('/[0-9]+-[0-9]+/', $date)){
            list($m, $d) = explode('-', $date);
            return sprintf("%02d-%02d", $m, $d);
        }
        if (preg_match('/[0-9]{4}/', $date)){
            return substr($date, 0, 2) .'-'.substr($date, 2, 2);
        }
    }

    /** check if there is exact one day between 2 dates, 
     * e.g., sandwiched('03-31', '05-02')
    */
    function sandwiched($date1, $date2)
    {
        return $date2 === $this->mkdate($date1, +2);
    }

    /** make a properly formatted new date by moving back/ahead some days, 
     * e.g., mkdate('2-14', 3) is '02-17', mkdate('3-31',2) = '04-02'
    */
    function mkdate ($date, $days=0)
    {
        list ($m, $d) = explode('-', $date);
        $time = mktime(0, 0, 0, $m, $d + $days, $this->year);
        return date('m-d',$time);
    }

    /** check if all elements of array $a are defined keys of array $b, 
     * e.g. is_defined(['tom','bob'], ['bob'=>23, 'abe'=>35, 'tom'=>56] is TRUE)
    */
    function is_defined($a, $b)
    {
        $diff = array_diff($a, array_keys($b));
        return empty($diff); 
    }

    /** check if $a is in range defeined by array $b, 
     * e.g. during(3, [2,4]) is TRUE  
    */
    function during($a, $b)
    {
        if (is_scalar($b))
            return $a === $b;
        if (sizeof($b) >= 2)
            return ($b[0] <= $a and $a <= $b[1]);
        return false;
    }

    /** parse and generate a holiday based the rule or definition.
     * * Basically, there're 3 types:
     * (1) fixed day, e.g., new year day,
     * (2) specified weekday, e.g., coming of age day = 2nd Monday of January
     * (3) others, e.g., spring/autumn equinox   
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

    /** caculate equinox days given the season and parameter $alpha, 
     * return -1 if there is no knowledge how to calculate it  
     * @parameter $alpha
     *  year          spring    autumn
        1851 - 1899   19.8277   22.2588
        1900 - 1979   20.8357   23.2588
        1980 - 2099   20.8431   23.2488
        2100 - 2150   21.8510   24.2488
    */
    function equinox($season='spring'){
        $year = $this->year;
        if (!$this->during($year, [1851, 2150])){
            return -1;
        }
        $alpha = [20.8431, 23.2488];
        if ($this->during($year, [1851, 1899]))
            $alpha = [19.8277, 22.2588];
        if ($this->during($year, [1900, 1979]))
            $alpha = [20.8357, 23.2588];
        if ($this->during($year, [2100, 2150]))
            $alpha = [21.8510, 24.2488];
        
        $beta = ($season=='spring') ? $alpha[0] : $alpha[1];

        return floor($beta + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    } 

    /**
     * _parseHolidays() : parse rules and generete all holidays for this year. 
     *
     * @param [type] $dat_holiday, rules that define various kinds of holidays
     * @param string $ex_name, supplementary holiday for coincident holidays   
     * @param string $sp_name, additional holiday for a noraml day sandwiched by 2 holidays
     * @return array a list of holidays,  [mm-dd] => name
     */
    private function _parseHolidays($dat_holiday, $ex_name='振替休日', $sp_name='国民の休日')
    {
        $holidays = [];
        $ex_holiday = null;
        foreach ($dat_holiday as $_month=>$_days){
            foreach ($_days as $d){
                $valid = true;
                if (isset($d['during'])){
                    $valid = $valid && $this->during($this->year, $d['during']);
                }
                if (isset($d['except'])){
                    $valid = $valid && !in_array($this->year, $d['except']);
                }
                if (isset($d['in'])){
                    $valid = $valid && in_array($this->year, $d['in']);
                }
                if ($valid) {
                    $hday = $this->parseDay($_month, $d['day']);
                    
                    if ($hday > 0){
                        $date = (new KsDateTime)->setDate($this->year, $_month, $hday);
                        
                        // supplementary holiday for coincident holidays
                        if ($ex_holiday != null ){ // for a pending ex_holiday 
                            if ($ex_holiday === $date){
                                $ex_holiday->modify('+1 day');
                            }else{
                                $holidays[$ex_holiday->format('m-d')] = $ex_name;
                                $ex_holiday = null;
                            }
                        }
                        $holidays[$date->format('m-d')] = $d['name'];
                        $cal = new KsCalendar($this->year, $_month);   
                        $wday = $cal->d2w($hday);
                        if ($wday === 0) { // prepare a new ex_hodilday
                            $ex_holiday = (new KsDateTime)->setDate($this->year, $_month, $hday +1);
                        }
                        
                    }    
                }
            }
        }
        ksort($holidays);
        
        // additional holiday, a normal day (rarely) sandwiched by two holidays
        $ex_holidays =[];
        $prev_date = null;
        foreach ($holidays as $date=>$name){
            if ($prev_date and $this->sandwiched($prev_date, $date)){
                $middle = $this->mkdate($prev_date, +1);
                $ex_holidays[$middle] = $sp_name;
            }
            $prev_date = $date;
        }
        $holidays = array_merge($holidays, $ex_holidays);
        ksort($holidays);
      
        return $holidays;
    }
}