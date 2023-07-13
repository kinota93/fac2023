<?php
namespace kcal;

include_once 'Utility.php';
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

    /** filter() : extact dates of the specified weekdays  
     * ex) filter([1,3],[2,4]): the first and third Tuesday and Thursday in the month
     * ex) filter(2, [1,3]) : get the date of the second Monday and Wednesday
     * ex) filter(2, 1) : get the date of the second Monday
    */
    public function filter($week, $wday=[])
    {    
        $days = [];
        $week = Util::valid_array($week, range(1, $this->n_weeks));
        $wday = Util::valid_array($wday, range(0, 6));
        foreach (Util::product($week, $wday) as [$wk, $wd]){
            $day = $this->wkday2day($wd, $wk);
            if ( $this->isValid($day) ) $days[] = $day;
        };
        return $days;
    } 

    /** wkday2day() : compute the day of the `$i`th `$wday` */
    public function wkday2day($wday, $i = 1)
    {   
        $i = ($wday >= $this->firstwday) ?  $i - 1 : $i;
        return $i * 7 + $wday - $this->firstwday + 1;
    }

    /** day2wkday(): compute the weekday of `$day` */
    public function day2wkday($day)
    {
        return ($this->firstwday + $day -1) % 7;
    }

    public function isValid($d, $flag='DAY')
    {
        if ($flag==='DAY')
            return (1 <= $d and $d <= $this->lastday);
        if ($flag==='WEEK')
            return (1 <= $d and $d <= $this->n_weeks);
        if ($flag==='WDAY')
            return (0 <= $d and $d <= 6);
    }
}