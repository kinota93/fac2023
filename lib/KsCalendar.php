<?php

namespace kcal;

require "vendor/autoload.php";

use Exception;
use function array_unique;
use function is_scalar;
use function ceil;
use function range;
use function date;


class KsCalendar
{
    public $year;// @var year
    public $month;// @var month
    public $lastday;// @var lastday of the month
    public $n_weeks;// @var number of weeks
    public $firstwday;// @var weekday of the first day 
    public $lastwday;// @var weekday of the last day
    
    public function __construct($year, $month)
    {
        $time = mktime(0, 0, 0, $month, 1, $year);
        $this->year = (int)date('Y', $time); 
        $this->month = (int)date('m', $time);         
        $this->lastday = (int)date('t', $time);
        $this->firstwday = (int)date('w', $time);
        $this->lastwday = $this->d2w($this->lastday);
        $this->n_weeks = ceil(($this->firstwday + $this->lastday) / 7.0 ); 
    }

    /** select() : extract dates of the specified weekdays  
     * ex) select([1,3],[2,4]): the first and third Tuesday and Thursday
     * ex) select(2, [1,3]) : the second Monday and Wednesday
     * ex) select(3, 4) :  the third Thursday
    */
    public function select($week, $wday=[])
    {    
        $days = [];
        if (is_scalar($week)) $week = [$week];
        if (is_scalar($week)) $wday = [$wday];
        $wday =  empty($wday) ? range(0, 6) : array_unique($wday);
        foreach ($week as $wk ){
            foreach ($wday as $wd){
                $day = $this->w2d($wd, $wk);
                if ( $this->is_valid($day) ) $days[] = $day;
            }
        };
        return $days;
    } 

    /** w2d() : transform weekday to day  */
    public function w2d($wday, $i = 1)
    {   
        $i = ($wday >= $this->firstwday) ?  $i - 1 : $i;
        return $i * 7 + $wday - $this->firstwday + 1;
    }

    /** d2w(): transform day to weekday */
    public function d2w($day)
    {
        return ($this->firstwday + $day -1) % 7;
    }

    /** is_valid(): check if it is in the valid range */
    public function is_valid($d, $flag='DAY')
    {
        if ($flag==='DAY')
            return (1 <= $d and $d <= $this->lastday);
        if ($flag==='WEEK')
            return (1 <= $d and $d <= $this->n_weeks);
        if ($flag==='WDAY')
            return (0 <= $d and $d <= 6);
    }
}