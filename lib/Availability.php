<?php
namespace kcal;

use Exception;
use function array_keys;
use function array_unique;
use function in_array;
use function sprintf;
use function explode;
use function implode;
use function substr;
use function array_merge;

class Availability{
    public $calendar;
    public $holiday;

    public $facility;

    public function __construct($calendar, $holiday ,$facility)
    {
        $this->calendar = $calendar;
        $this->holiday  = $holiday;
        $this->facility = $facility;
    }

    /** Calendar of business [week]days, non-business [week]days */
    public function parseCalendar($dat_calendar, $holiday='定休日',$workday='営業日')
    {
        $month = $this->calendar->month;
        foreach ($dat_calendar as $day){
            if (isset($day['days'])){// 日付で与えられた臨時休日・臨時営業日
                foreach ($day['days'] as $md => $name){
                    list($m, $d) = explode('-', $md);
                    if ($m == $month) {
                        $dates[$d][] = ['type' => $day['type'], 'name' => $name];
                    }
                }
            }else{ // 曜日で与えられた定休日・営業日
                if (!isset($day['month']) or (isset($day['month']) and 
                        in_array($month, $day['month']))){
                            $name = substr($day['type'],-7)=='holiday' ? $holiday : $workday;
                    $week = array_unique($day['week']);
                    $wday = array_unique($day['wday']);                
                    $days = $this->calendar->select($week, $wday);   
                    foreach ($days as $d){
                        $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                    }
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
                $rs['timeslots'] .= sprintf(" %d: %s - %s\n", $id, $v['start'], $v['end']);
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
        $year = $this->calendar->year;
        $month = $this->calendar->month;
        foreach ($reservation as $rev){
            if ($rev['facility_id'] != $this->facility) continue;
            list($y, $m, $d) = explode('-', $rev['date']);
            if ($y==$year and $m==$month){
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
        $year = $this->calendar->year;
        $month = $this->calendar->month;
        // 国民の休日・祝日
        $holidays = $this->holiday->getHolidays($month);
        foreach($holidays as $md => $name){
            list($_, $d) = explode('-', $md);
            $dates[(int)$d][] = ['type' => 'public_holiday', 'name' => $name];
        }
        // 定休日・営業日
        if (isset($calendar[$year])){
            $cal_dates = $this->parseCalendar($calendar[$year]);    
            $rev_dates = $this->parseReservation($reservation);
            foreach ($cal_dates as $d=>$v){
                $dates[(int)$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
            }
            foreach ($rev_dates as $d=>$v){
                $dates[(int)$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
            }
        } 
        ksort($dates);        

        return $dates;
    }

    function output($dates)
    {
        $days = range(1, $this->calendar->lastday);
        $wdays = array_map([$this->calendar,'d2w'], $days);
        $names =["日", "月", "火", "水", "木", "金", "土"];
        for ($i= 0; $i< $this->calendar->lastday; $i++){
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