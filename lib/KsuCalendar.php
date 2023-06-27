<?php
namespace kcal;

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

    /** cartersian() function: returns cartesion product of 2 arrays*/
    public static function cartesian($array1, $array2)
    {
        $product = [];
        $array1 = array_values(array_unique($array1));
        $array2 = array_values(array_unique($array2));
        foreach ($array1 as $e1)
            foreach ($array2 as $e2)
                $product[] = [$e1, $e2];
        return $product;
    } 

    /** filter() function: returns days of the specified weekdays  
     * ex) filter([1,3],[2,4]): the first and third Tuesday and Thursday 
    */
    public function filter($week, $wday=[])
    {    
        $days = [];
        $week = self::_valid_array($week, range(1, $this->n_weeks));
        $wday = self::_valid_array($wday, range(0, 6));
        foreach (self::cartesian($week, $wday) as $w){
            $d = $this->wk2day($w[1], $w[0]);
            if ($d <= $this->lastday) $days[] = $d;
        };
        return $days;
    }    

    /** slice() function: returns days of the specified weeks  
     * ex) slice([1,3], [2,4]) Tuesday and Thursday in the first and third weeks 
    */
    public function slice($week, $wday=[])
    {
        $days = [];
        $week = self::_valid_array($week, range(1, $this->n_weeks));
        $wday = self::_valid_array($wday, range(0, 6));    
        foreach (self::cartesian($week, $wday) as $w){
            $i = ($w[1] >= $this->firstwday) ? $w[0] : $w[0] -1;
            if ($i < 1) continue;
            $d = $this->wk2day($w[1], $i);
            if ($d <= $this->lastday) $days[] = $d;
        };
        return $days;
    }

    private static function _valid_array($array, $domain)
    {
        if (!$array) return $domain;
        return array_intersect($array, $domain);
    } 

    /** day() function: return the day of the `$i` th `$wday` */
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