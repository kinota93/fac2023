<?php
namespace kcal;

use Exception;
use function array_keys;
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
        $dates = [];
        foreach ($dat_calendar as $day){
            if (isset($day['day'])){ // 日付で与えられた臨時休日・臨時営業日
                foreach ($day['day'] as $md => $name){
                    list($m, $d) = explode('-', $md);
                    if ($m == $month) {
                        $dates[(int)$d][] = ['type'=>$day['type'], 'name'=>$name];
                    }
                }
            }elseif (!isset($day['month']) or // 曜日で与えられた定休日・営業日
                (isset($day['month']) and in_array($month, $day['month']))){
                $name = substr($day['type'],-7)=='holiday' ? self::NON_BUSINESS_DAY : self::BUSINESS_DAY;             
                $days = $this->calendar->select($day['week'], $day['wday']);   
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

        $bz_time = [];
        if (isset($fac['time'])){ // local business time
            $bz_time = $fac['time'];
        }elseif (isset($dat_facility['business'])){ // global business time
            $bz_time = $dat_facility['business'];
        }
        if ($bz_time){
            $rs['time'] = $bz_time['open'] . ' - ' . $bz_time['close'] ;
        }

        if (isset($fac['timeslots'])) {
            $rs['timeslots'] = sprintf("[%s]\n", implode(',' , array_keys($fac['timeslots'])));
            foreach ($fac['timeslots'] as $id=>$v){
                $rs['timeslots'] .= sprintf(" %d: %s - %s\n", $id, $v['start'], $v['end']);
            }
            $rs['timeslots'] = trim($rs['timeslots']);
            $rs['capacity'] = count($fac['timeslots']);
        }

        if (isset($fac['timeunit'])) {
            $rs['timeslots'] = sprintf("every %d %s(s)", 
                $fac['timeunit']['length'], $fac['timeunit']['unit']);
        }
        if (!isset($rs['capacity']))
            $rs['capacity'] = KsDateTime::delta($bz_time['open'], $bz_time['close']);
     
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
                if (isset($rev['timeslot'])) {
                    $rs['timeslot'] = $rev['timeslot'];
                    $rs['reserved'] = count($rev['timeslot']);
                }
                if (isset($rev['timeslice'])) {
                    $rs['timeslice'] = $rev['timeslice'];
                    $rs['reserved'] =  KsDateTime::delta($rev['timeslice'][0],$rev['timeslice'][1]);
                }
                $dates[(int)$d][] =  $rs;
            }
        }
        return $dates;
    }
    
    public function getAvailability($calendar, $reservation)
    {
        $year = $this->calendar->year;
        $month = $this->calendar->month;

        $dates = []; 
        $holidays = $this->holiday->getHolidays($month);
        foreach($holidays as $md => $name){ // national holidays
            list($_, $d) = explode('-', $md);
            $dates[(int)$d][] = ['type'=>'national_holiday', 'name'=>$name];
        }
        
        if (isset($calendar[$year])){ // business calendar
            $cal_dates = $this->parseCalendar($calendar[$year]);    
            foreach ($cal_dates as $d=>$v){
                $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
            }
        }        

        $rev_dates = $this->parseReservation($reservation);
        foreach ($rev_dates as $d=>$v){
            $dates[$d] = isset($dates[$d]) ? array_merge($dates[$d], $v) : $v;      
        }
        ksort($dates); 
        return $dates;
    }

    function output($dates)
    {
        foreach (range(1, $this->calendar->lastday) as $d){
            printf( "%02d(%s):", $d, $this->calendar->d2w($d, 'JP'));
            if (!isset($dates[$d])) {
                echo " ◎\n";
                continue;    
            }
            $reserved = 0;
            echo "\n";
            foreach ($dates[$d] as $r){
                echo " * name: " . $r['name'] . "\n";
                echo " - type: " . $r['type'] . "\n";
                if (isset($r['timeslot'])){
                    echo " - time: " . implode(',' , $r['timeslot']) . " (slots)\n";
                }
                if (isset($r['timeslice'])){
                    echo " - time: " . implode(' - ' , $r['timeslice']) . "\n";
                }
                if (isset($r['reserved'])) $reserved += $r['reserved'];
            }
            if ($reserved > 0)
                echo " - reserved : {$reserved} △\n";
            
        }
    }
}