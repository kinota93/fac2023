<?php
namespace kcal;

include 'Utility.php';
class KsuCalendar
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
        $this->lastwday = $this->day2wkday($this->lastday);
        $this->n_weeks = ceil(($this->firstwday + $this->lastday) / 7.0 ); 
    }

    /** filter() : extact days of the specified weekdays  
     * ex) filter([1,3],[2,4]): the first and third Tuesday and Thursday in the month
    */
    public function filter($week, $wday=[])
    {    
        $days = [];
        $week = Util::valid_array($week, range(1, $this->n_weeks));
        $wday = Util::valid_array($wday, range(0, 6));
        foreach (Util::product($week, $wday) as [$wk, $wd]){
            $day = $this->wkday2day($wd, $wk);
            if ($day <= $this->lastday) $days[] = $day;
        };
        return $days;
    } 

    /** slice() function: extract weekdays of the specified weeks  
     * ex) slice([1,3], [2,4]) Tuesday and Thursday in the first and third weeks (if exists)
    */
    public function slice($week, $wday=[])
    {
        $days = [];
        $week = Util::valid_array($week, range(1, $this->n_weeks));
        $wday = Util::valid_array($wday, range(0, 6));    
        foreach (Util::product($week, $wday) as [$wk, $wd]){
            $wk = ($wd >= $this->firstwday) ? $wk : $wk -1;
            if ($wk >= 1) {
                $day = $this->wkday2day($wd, $wk);
                if ($day <= $this->lastday) $days[] = $day;
            }
        };
        return $days;
    }

    /** wkday2day() : compute the day of the `$i`th `$wday` */
    public function wkday2day($wday, $i=1)
    {   
        if ($wday >= $this->firstwday) {
            return $i * 7 - $this->firstwday + $wday - 6;
        }
        return $i * 7 - $this->firstwday + $wday + 1;
    }

    /** day2wkday(): compute the weekday of `$day` */
    public function day2wkday($day)
    {
        return ($this->firstwday + $day -1) % 7;
    }
}