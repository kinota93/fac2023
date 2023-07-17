<?php

namespace kcal;

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
    public $lastday;// @var lastday of the month, days of the month
    public $n_weeks;// @var number of weeks
    public $firstwday;// @var weekday of the first day 
    public $lastwday;// @var weekday of the last day
    
    public const PREFER_TO_WDAY = 1;    
    public const PREFER_TO_WEEK = 2;    

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

    /** select() : selects dates of the specified weekdays  
     * When PREFER_TO_WDAY (default), selects n'th weekday 
     *   ex) select([1,3],[2,4]): 1st and 3rd Tuesday and Thursday
     *   ex) select(2, [1,3]) : 2nd Monday and Wednesday
     *   ex) select(3, 4) :  3rd Thursday
     * When PREFER_TO_WEEK ($prefer==2), selects a weekday in n'th week
     *   ex) select([1,3],[2,4], PREFER_TO_WEEK): Tuesday and Thursday in 1st and 3rd weeks 
     *   ex) select(2, [1,3], PREFER_TO_WEEK) : Monday and Wednesday in 2nd week
     *   ex) select(3, 4, PREFER_TO_WEEK): Thursday in 3rd week
    */
    public function select($week, $wday=[], $prefer = 1)
    {    
        $days = [];
        if (is_scalar($week)) $week = [$week];
        if (is_scalar($week)) $wday = [$wday];
        $wday =  empty($wday) ? range(0, 6) : array_unique($wday);
        foreach ($wday as $wd){
            foreach ($week as $wk ){
                if ($prefer == self::PREFER_TO_WEEK){
                    $wk = ($wd < $this->firstwday) ? $wk - 1 : $wk;
                }
                if ($wk < 1)  continue;
                $day = $this->w2d($wd, $wk);
                if ( $this->is_valid($day) ) $days[] = $day;
            }
        };
        return $days;
    } 

    /** w2d() : transform the i'th weekday to a day number */
    public function w2d($wday, $i = 1)
    {   
        $i = ($wday >= $this->firstwday) ?  $i - 1 : $i;
        return $i * 7 + $wday - $this->firstwday + 1;
    }

    /** d2w(): transform a day number to weekday */
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

    public function __toString()
    {
        $out = "Sun Mon Tue Wed Thu Fri Sat\n";
        for ($i = 0; $i < $this->firstwday; $i++){
            $out .= "    "; // 4 spaces
        }
        for ($i = 0; $i < $this->lastday; $i++){
            $out .= sprintf("% 2d  ", $i + 1);
            if (($i + $this->firstwday + 1) % 7 == 0){
                $out .= "\n";
            }
        }
        return trim($out); // trim trailing \n chracters
    }
}