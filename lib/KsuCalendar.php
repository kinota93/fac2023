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
        $this->lastwday = ($this->firstwday + $this->lastday -1) % 7;
        $this->n_weeks = ceil(($this->firstwday + $this->lastday) / 7.0 ); 
    }

    /** filter() function: returns days of the specified weekdays  
     * ex) filter([1,3],[2,4]): the first and third Tuesday and Thursday in the month
    */
    public function filter($week, $wday=[])
    {    
        $days = [];
        $week = Util::valid_array($week, range(1, $this->n_weeks));
        $wday = Util::valid_array($wday, range(0, 6));
        foreach (Util::product($week, $wday) as [$wk, $wd]){
            $d = $this->wk2day($wd, $wk);
            if ($d <= $this->lastday) $days[] = $d;
        };
        return $days;
    } 

    /** slice() function: returns days of the specified weeks  
     * ex) slice([1,3], [2,4]) Tuesday and Thursday in the first and third weeks if exists
    */
    public function slice($week, $wday=[])
    {
        $days = [];
        $week = Util::valid_array($week, range(1, $this->n_weeks));
        $wday = Util::valid_array($wday, range(0, 6));    
        foreach (Util::product($week, $wday) as [$wk,$wd]){
            $wk = ($wd >= $this->firstwday) ? $wk : $wk -1;
            if ($wk < 1) continue;
            $d = $this->wk2day($wd, $wk);
            if ($d <= $this->lastday) $days[] = $d;
        };
        return $days;
    }

      /** wk2day() function: return the day of the `$i` th `$wday` */
    public function wk2day($wday, $i=1)
    {   
        if ($wday >= $this->firstwday) {
            return $i * 7 - $this->firstwday + $wday - 6;
        }
        return $i * 7 - $this->firstwday + $wday + 1;
    }

    /** day2wd() function: return weekday of `$day` */
    public function day2wk($day)
    {
        return ($this->firstwday + $day -1) % 7;
    }
}