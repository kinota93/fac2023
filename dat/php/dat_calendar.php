<?php
return 
[
    2023=> 
    [
    /* public holidays can be now auto-computed
        [  
            'type' => 'public_holiday',
            'days' => [
                '1-1' => '元日',
                '1-2' => '振替休日',
                '1-9' => '成人の日',
                '2-11' => '建国記念の日',
                '2-23' => '天皇誕生日',
                '3-21' => '春分の日',
                '4-29' => '昭和の日',
                '5-3' => '憲法記念日',
                '5-4' => 'みどりの日',
                '5-5' => 'こどもの日',
                '7-17' => '海の日',
                '8-11' => '山の日',
                '9-18' => '敬老の日',
                '9-23' => '秋分の日',
                '10-9' => 'スポーツの日',
                '11-3' => '文化の日',
                '11-23' => '勤労感謝の日',
            ]
        ],
    */
        [
            'type' => 'local_holiday',
            'month' => [4,5,6,7,8,9,10,11,12],  
            'week' => [2,4],
            'wday' => [2,3],
        ],    
        
        [   
            'type' => 'local_holiday',
            'day' => [
                '10-12' => '臨時休業',
                '12-15' => '臨時休業',
            ]
        ],

        [
            'type' => 'local_workday',
            'day' => [
                '10-9' => '月曜特別営業日',
                '11-23' => '木曜特別営業日',
            ],
        ]
    ],
];