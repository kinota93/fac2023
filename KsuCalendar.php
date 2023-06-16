<?php
class KsuCalendar
{
    public $year;// @var 
    public $month;// @var 
    public $lastday;// @var 
    public $n_weeks;// @var 
    public $firstwday, $lastwday;// @var 

    
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
    
    /** filter function: returns days of the specified weekdays and weeks  */
    public function filter($week=[], $wday=[])
    {    
        $days = [];
        $wday = self::_valid_array($wday, range(0, 6)) ;
        $week = self::_valid_array($week, range(1, $this->n_weeks));
        foreach ($wday as $w){
            for ($d = $this->firstweek($w); $d <= $this->lastday; $d +=7){
                foreach ($week as $j){
                    if ($d == $this->firstweek($w) + 7*($j-1)){
                        $days[] = $d;
                    }
                }  
            }
        };
        sort($days);
        return $days;
    }

    /** slice() function: returns slice of the n'th week */
    public function slice($n)
    {
        $upper = 7 * $n - $this->firstwday;
        $lower = max($upper - 6, 1);
        $upper = min($upper, $this->lastday);
        if ($lower <= $upper) {
            return range($lower, $upper);
        }
        return [];
    }

    /** firstweek() function: return first day of weekday `$wday` */
    public function firstweek($wday)
    {
        if ($wday >= $this->firstwday) {
            return $wday - $this->firstwday + 1;
        }
        return $this->firstwday + $wday;
    }

    /** wday() function: return weekday of `$day` */
    public function wday($day)
    {
        return ($this->firstwday + $day -1) % 7;
    }
    /** getWeekday function: returns wdays of $days */
    public function wdays($days){
        return array_combine($days, array_map([$this,'wday'], $days));
    }

    public function parseCalendar($calendar)
    {
        $dates = [];
        foreach ($calendar as $day){
            // 日付で与えられた祝日・休日・営業日
            if (isset($day['days'])){
                foreach ($day['days'] as $md=>$name){
                    list($m, $d) = explode('-', $md);
                    if ($m == $this->month) {
                        $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                    }
                }
            }
            else if (!isset($day['month']) or
                     (isset($day['month']) and in_array($this->month, $day['month'])) ){
            // 定休日[定休曜日]・営業日[営業曜日]
                $name = substr($day['type'],-7)=='holiday' ? '定休日' : '営業日';
                $wday = self::_valid_array($day['weekday']);
                $week = self::_valid_array($day['week']) ;
                $days = $this->filter($week, $wday);   
                foreach ($days as $d){
                    $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                }
            }
        }
        return $dates;
    }

    private static function _valid_array($array, $empty=[])
    {
        if (!is_array($array)) return [$array];
        return empty($array) ? $empty : $array;
    }

    public function parseFacility($dat_facility, $facility)
    {
        if (!isset($dat_facility[$facility])) return null;
        $rs = [];
        $fac = $dat_facility[$facility];
        if (isset($fac['timeslots'])) {
            $rs['timeslots'] = sprintf("[%s]\n", implode(',',array_keys($fac['timeslots'])));
            foreach ($fac['timeslots'] as $id=>$v){
                $rs['timeslots'] .= sprintf(" %d: %s - %s\n", $id, $v['start_time'], $v['end_time']);
            }
        }
        if (isset($fac['timeunit'])) {
            $rs['timeslots'] = sprintf("every %d %s(s)", 
                $fac['timeunit']['length'], 
                $fac['timeunit']['unit']);
        }
        if (isset($fac['time'])){
            $rs['time'] = implode(' - ',$fac['time']) ;
        }
        return $rs;
    }

    public function parseReservation($reservation, $facility)
    {
        $dates = [];
        foreach ($reservation as $rev){
            if ($rev['facility_id'] == $facility){
                list($y, $m, $d) = explode('-', $rev['date']);
                if ($y==$this->year and $m==$this->month){
                    $rs = ['type'=>'event', 'name'=>$rev['event']];
                    if (isset($rev['timeslot'])) $rs['timeslot']=$rev['timeslot'];
                    if (isset($rev['timespan'])) $rs['timespan']=$rev['timespan'];
                    $dates[$d][] =  $rs;
                }
            }
        
        }
        return $dates;
    }

    public function output($dates)
    {
        $days = $this->wdays(range(1, $this->lastday));     
        $names =["日", "月", "火", "水", "木", "金", "土"];
        foreach ($days as $d=>$w){
            printf( "%02d(%s):\n",$d, $names[$w]);
            if (! isset($dates[$d])) continue;
            foreach ($dates[$d] as $r){
                echo " * name: " . $r['name'] . "\n";
                echo " - type: " . $r['type'] . "\n";
                if (isset($r['timeslot'])){
                    $timeslots = implode(',',$r['timeslot']) ; 
                    echo " - time: ". $timeslots . " (slots)\n";
                }
                if (isset($r['timespan'])){
                    $timespan = implode(' - ',$r['timespan']) ; 
                    echo " - time: ". $timespan . "\n";
                }
            }
        }
    }
}