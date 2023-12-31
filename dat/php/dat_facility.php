<?php
$ksuslots = 	[
    1 => [
	    'start' => '9:00',
	    'end' => '10:40',
    ],

    2 => [
        'start' => '11:00',
        'end' => '12:40',
    ],

    3 => [
        'start' => '13:40',
        'end' => '15:20',
    ],
    4 => [
        'start' => '15:40',
        'end' => '17:20',
    ],

    5 => [
        'start' => '17:40',
        'end' => '19:20',
    ],
]; // end of timeslots

return  [
    '12107'=>[
        'name' => '12107番教室',
        'building' => '12号館',
        'floor' => 1,
        'timeslots' => $ksuslots,
	],

	'12216'=>[ 
        'name' => '12216番教室',
        'building' => '12号館',
        'floor' => 2,
        'timeslots' =>  $ksuslots,  
    ],

    '12311'=>[
        'name' => '12号館大会議室',
        'building' => '12号館',
        'floor' => 3,
        'time' => ['9:00','21:00'],          
        'timeunit' => [
            'length' => 10,
            'unit' => 'minute',
        ]
	],

]; // end of facilities

