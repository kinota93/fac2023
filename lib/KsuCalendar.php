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
        return $i * 7 - $this->firstwday + $wday + 1;
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
    
    public function getHolidays($dat_holiday)
    {
        $holidays = [];
        $ex_holiday = null;
        foreach ($dat_holiday as $month=>$days){
            foreach ($days as $d){
                $valid = true;
                if (isset($d['during'])){
                    $valid = $valid && $this->during($this->year, $d['during']);
                }
                if (isset($d['except'])){
                    $valid = $valid && !in_array($this->year, $d['except']);
                }
                if ($valid) {
                    $hday = $this->parseDay($month, $d['day']);
                    
                    if ($hday > 0){
                        $date = (new \DateTime)->setDate($this->year, $month, $hday);
                        
                        if ($ex_holiday!=null ){ // for a pending ex. holiday 
                            if ($ex_holiday === $date){
                                $ex_holiday->modify('+1 day');
                            }else{
                                $holidays[$ex_holiday->format('m-d')] = '振替休日';
                                $ex_holiday = null;
                            }
                        }
                        $holidays[$date->format('m-d')] = $d['name'];
                        $cal = new KsuCalendar($this->year, $month);   
                        $wday = $cal->day2wkday($hday);
                        if ($wday === 0) { // set a new ex. hodilday
                            $ex_holiday = (new \DateTime)->setDate($this->year, $month, $hday +1);
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
            if ($prev_date and $this->sandwiched($prev_date, $date)){
                $middle = $this->mkdate($prev_date, +1);
                $ex_holidays[$middle] = '国民の休日';
            }
            $prev_date = $date;
        }
        $holidays = array_merge($holidays, $ex_holidays);
        ksort($holidays);
    
        return $holidays;
    }

    function sandwiched($date1, $date2)
    {
        return $date2 === $this->mkdate($date1, +2);
    }
    function mkdate ($date, $days=0){
        list ($m, $d) = explode('-', $date);
        $time = mktime(0,0,0,$m, $d+$days, $this->year);
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
    function parseDay($month, $day)
    {
        $cal = new KsuCalendar($this->year, $month);
        if (is_integer($day))
            return $day;
        if (is_array($day))
            return $cal->wkday2day($day[1], $day[0]);
        if ($day==='springEquinox')
            return  $this->equinox();
        if ($day==='autumnEquinox')
            return  $this->equinox('autumn');
        return -1;    
    }
    /**   year         spring   autumn
        1851 - 1899   19.8277   22.2588
        1900 - 1979   20.8357   23.2588
        1980 - 2099   20.8431   23.2488
        2100 - 2150   21.8510   24.2488
    */
    function equinox($season='spring'){
        if (!$this->during($this->year, [1851, 2150])){
            return -1;
        }
        $alpha = [20.8431, 23.2488];
        if ($this->during($this->year, [1851, 1899]))
            $alpha = [19.8277, 22.2588];
        if ($this->during($this->year, [1900, 1979]))
            $alpha = [20.8357, 23.2588];
        if ($this->during($this->year, [2100, 2150]))
            $alpha = [21.8510, 24.2488];
        
        $beta = ($season=='spring') ? $alpha[0] : $alpha[1];

        return floor($beta + 0.242194 * ($this->year - 1980) - floor(($this->year - 1980) / 4));
    } 

}