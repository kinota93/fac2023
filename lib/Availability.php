<?php
namespace kcal;

// require_once 'KsuCalendar.php';
require_once 'Utility.php';

class Availability{
    public $cal;
    public $facility;

    public function __construct($calendar, $facility)
    {
        $this->cal = $calendar;
        $this->facility = $facility;
    }
    
    public function parseCalendar($dat_calendar, $holiday='定休日',$workday='営業日')
    {
        $dates = [];
        foreach ($dat_calendar as $day){
            // 日付で与えられた祝日・休日・営業日
            if (isset($day['days'])){
                foreach ($day['days'] as $md=>$name){
                    list($m, $d) = explode('-', $md);
                    if ($m == $this->cal->month) {
                        $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                    }
                }
            } else
            if (!isset($day['month']) or
                (isset($day['month']) and in_array($this->cal->month, $day['month'])) ){
                // 曜日で与えられた定休日・営業日    
                $name = substr($day['type'],-7)=='holiday' ? $holiday : $workday;
                $wday = Util::valid_array($day['weekday'], range(0,6));
                $week = Util::valid_array($day['week'], range(1, $this->cal->n_weeks)) ;
                $days = $this->cal->filter($week, $wday);   
                foreach ($days as $d){
                    $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                }
            }
        }
        return $dates;
    }

    public function parseFacility($dat_facility)
    {
        if (!isset($dat_facility[$this->facility])) return null;
        $rs = [];
        $fac = $dat_facility[$this->facility];
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

    public function parseReservation($reservation)
    {
        $dates = [];
        foreach ($reservation as $rev){
            if ($rev['facility_id'] != $this->facility) continue;
            list($y, $m, $d) = explode('-', $rev['date']);
            if ($y==$this->cal->year and $m==$this->cal->month){
                $rs = ['type'=>'event', 'name'=>$rev['event']];
                if (isset($rev['timeslot'])) $rs['timeslot'] = $rev['timeslot'];
                if (isset($rev['timespan'])) $rs['timespan'] = $rev['timespan'];
                $dates[$d][] =  $rs;
            }
        }
        return $dates;
    }
    
    public function getAvailability($calendar, $reservation)
    {
        $dates = [];
        $year = $this->cal->year;
        if (isset($calendar[$year])){
            $cal_dates = $this->parseCalendar($calendar[$year]);    
            $rev_dates = $this->parseReservation($reservation);
            $dates = $cal_dates;
            foreach ($rev_dates as $d=>$v){
                $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
            }
        } 
        ksort($dates);
        return $dates;
    }

    function output($dates)
    {
        $days = range(1, $this->cal->lastday);
        $wdays = array_map([$this->cal,'day2wkday'], $days);
        $names =["日", "月", "火", "水", "木", "金", "土"];
        for ($i= 0; $i< $this->cal->lastday; $i++){
            $d = $days[$i];
            $w = $wdays[$i];
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