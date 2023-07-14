<?php
namespace kcal;

require_once 'KsuCalendar.php';
class Holiday
{
    public $year;
    public $holidays;
    public function __construct($year, $dat_holiday)
    {
        $this->year = $year;
        $this->holidays = $this->_parseHolidays($dat_holiday);
    }
    
    public function getHolidays($month = 0)
    {
        if ($month == 0) {
            return $this->holidays;
        }
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

    function sandwiched($date1, $date2)
    {
        return $date2 === $this->mkdate($date1, +2);
    }
    function mkdate ($date, $days=0)
    {
        list ($m, $d) = explode('-', $date);
        $time = mktime(0, 0, 0, $m, $d + $days, $this->year);
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
        $cal = new \kcal\KsuCalendar($this->year, $month);
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
    /** year          spring    autumn
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
                if ($valid) {
                    $hday = $this->parseDay($_month, $d['day']);
                    
                    if ($hday > 0){
                        $date = (new \DateTime)->setDate($this->year, $_month, $hday);
                        
                        if ($ex_holiday!=null ){ // for a pending ex. holiday 
                            if ($ex_holiday === $date){
                                $ex_holiday->modify('+1 day');
                            }else{
                                $holidays[$ex_holiday->format('m-d')] = $ex_name;
                                $ex_holiday = null;
                            }
                        }
                        $holidays[$date->format('m-d')] = $d['name'];
                        $cal = new KsuCalendar($this->year, $_month);   
                        $wday = $cal->d2w($hday);
                        if ($wday === 0) { // set a new ex. hodilday
                            $ex_holiday = (new \DateTime)->setDate($this->year, $_month, $hday +1);
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
                $ex_holidays[$middle] = $sp_name;
            }
            $prev_date = $date;
        }
        $holidays = array_merge($holidays, $ex_holidays);
        ksort($holidays);
      
        return $holidays;
    }
}