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

    private const BUSINESS_DAY = '営業日';
    private const NON_BUSINESS_DAY = '定休日';    

    public function __construct($calendar, $holiday ,$facility)
    {
        $this->calendar = $calendar;
        $this->holiday  = $holiday;
        $this->facility = $facility;
    }

    /** Calendar of business [week]days, non-business [week]days */
    public function parseCalendar($dat_calendar)
    {
        $month = $this->calendar->month;
        foreach ($dat_calendar as $day){
            if (isset($day['days'])){ // 日付で与えられた臨時休日・臨時営業日
                foreach ($day['days'] as $md => $name){
                    list($m, $d) = explode('-', $md);
                    if ($m == $month) {
                        $dates[(int)$d][] = ['type'=>$day['type'], 'name'=>$name];
                    }
                }
            }elseif (!isset($day['month']) or // 曜日で与えられた定休日・営業日
                (isset($day['month']) and in_array($month, $day['month']))){
                $name = substr($day['type'],-7)=='holiday' ? self::NON_BUSINESS_DAY : self::BUSINESS_DAY;
                $week = array_unique($day['week']);
                $wday = array_unique($day['wday']);                
                $days = $this->calendar->select($week, $wday);   
                foreach ($days as $d){
                    $dates[$d][] = ['type'=>$day['type'], 'name'=>$name];
                }
            }
        }
        return $dates;
    }

    public function parseFacility($dat_facility)
    {
        $the_facility = $dat_facility['facility'];
        if (!isset($the_facility[$this->facility])) {
            return [];
        }
        
        $fac = $the_facility[$this->facility];
        $rs = [];
        if (isset($fac['timeslots'])) {
            $rs['timeslots'] = sprintf("[%s]\n", implode(',',array_keys($fac['timeslots'])));
            foreach ($fac['timeslots'] as $id=>$v){
                $rs['timeslots'] .= sprintf(" %d: %s - %s\n", $id, $v['start'], $v['end']);
            }
            $rs['timeslots'] = trim($rs['timeslots']);
        }
        if (isset($fac['timeunit'])) {
            $rs['timeslots'] = sprintf("every %d %s(s)", 
                $fac['timeunit']['length'], 
                $fac['timeunit']['unit']);
        }
        if (isset($fac['time'])){ // local business time
            $rs['time'] = $fac['time']['open'] . ' - ' . $fac['time']['close'] ;
        }elseif (isset($dat_facility['business'])){ // global business time
            $rs['time'] = $dat_facility['business']['open'] . ' - ' . $dat_facility['business']['close'] ;
        }
        return $rs;
    }

    public function parseReservation($reservation)
    {
        $year = $this->calendar->year;
        $month = $this->calendar->month;
        $dates = [];
        foreach ($reservation as $rev){
            if ($rev['facility_id'] != $this->facility) continue;
            list($y, $m, $d) = explode('-', $rev['date']);
            if ($y==$year and $m==$month){
                $rs = ['type'=>'event', 'name'=>$rev['event']];
                if (isset($rev['timeslot'])) $rs['timeslot'] = $rev['timeslot'];
                if (isset($rev['timespan'])) $rs['timespan'] = $rev['timespan'];
                $dates[(int)$d][] =  $rs;
            }
        }
        return $dates;
    }
    
    public function getAvailability($calendar, $reservation)
    {
        $year = $this->calendar->year;
        $month = $this->calendar->month;

        $dates = []; // national holidays
        $holidays = $this->holiday->getHolidays($month);
        foreach($holidays as $md => $name){
            list($_, $d) = explode('-', $md);
            $dates[(int)$d][] = ['type'=>'national_holiday', 'name'=>$name];
        }
        // Business days and non-business days
        if (isset($calendar[$year])){
            $cal_dates = $this->parseCalendar($calendar[$year]);    
            foreach ($cal_dates as $d=>$v){
                $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
            }
        }        
        // Reservations
        $rev_dates = $this->parseReservation($reservation);
        foreach ($rev_dates as $d=>$v){
            $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
        }
        ksort($dates); 
        return $dates;
    }

    function output($dates)
    {
        $days = range(1, $this->calendar->lastday);
        $wdays = array_map([$this->calendar,'d2w'], $days);
        $names =["日", "月", "火", "水", "木", "金", "土"];
        for ($i = 0; $i < $this->calendar->lastday; $i++){
            $d = $days[$i];
            $w = $wdays[$i];
            printf( "%02d(%s):\n", $d, $names[$w]);
            if (! isset($dates[$d])) continue;
            foreach ($dates[$d] as $r){
                echo " * name: " . $r['name'] . "\n";
                echo " - type: " . $r['type'] . "\n";
                if (isset($r['timeslot'])){
                    $timeslots = implode(',' , $r['timeslot']) ; 
                    echo " - time: " . $timeslots . " (slots)\n";
                }
                if (isset($r['timespan'])){
                    $timespan = implode(' - ' , $r['timespan']) ; 
                    echo " - time: " . $timespan . "\n";
                }
            }
        }
    }
}