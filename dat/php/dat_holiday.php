<?php
/**
 * 1. at most one holiday for each day
 * 2. special holiday i sandwiched by day i-1 and day i+1
 */
return [
    1=>[
        [
            'name' => '元日',
            'day' => 1,
        ],
        [
            'name' => '成人の日',
            'day' => [2, 1], # 2nd Monday
        ],
    ],
    2 => [
        [
            'name' => '建国記念の日',
            'day' => 11,
            'during' => [1966, 2999] # 2999 for reasonable future
        ],
        [
            'name' => '天皇誕生日', # 令和天皇
            'day' => 23,
            'during' => [2018, 2999], 
        ],
    ],
    3 => [
        [
            'name' => '春分の日',
            'day' => 'springEquinox',
        ],
    ],
    4 => [
        [
            'name' => '昭和の日',
            'day' => 29,
            'during' =>[1989, 2999],
        ],
        [
            'name' => '天皇誕生日', # 昭和天皇
            'day' => 29,
            'during' => [1910, 1988], # a range of years bound-inclusive 
        ],
    ],
    5 => [
        [
            'name' => '天皇の即位の日',
            'day' => 1,
            'in' => [2019],
        ],  
        [
            'name' => '憲法記念日',
            'day' => 3,
        ],
        [
            'name' => 'みどりの日',
            'day' => 4,
        ],
        [
            'name' => 'こどもの日',
            'day' => 5,
        ],
   
    ],
    7 => [
        [
            'name' => '海の日',
            'day' => [3, 1], # 3rd Monday
            'except' => [2020, 2021], # a set of years
        ],
        [
            'name' => '海の日',
            'day' => 22,
            'in' => [2021],
        ],
        [
            'name' => '海の日',
            'day' => 23,
            'in' => [2020],
        ],
        [
            'name' => 'スポーツの日',
            'day' => 24,
            'in' => [2020],
        ],  
        [
            'name' => 'スポーツの日',
            'day' => 23,
            'in' => [2021],
        ],

    ],
    8 => [
        [
            'name' => '山の日',
            'day' => 11,
            'except' => [2020, 2021]
        ],
        [
            'name' => '山の日',
            'day' => 8,
            'in' => [2021],
        ],
        [
            'name' => '山の日',
            'day' => 10,
            'in' => [2020],
        ],        
    ],
    9 => [
        [
            'name' => '敬老の日',
            'day' => [3, 1], # 3rd Monday
        ],
        [
            'name' => '秋分の日',
            'day' => 'autumnEquinox',
        ], 
    ],
    10 => [
        [
            'name' => 'スポーツの日',
            'day' => 9,
            'except' => [2020, 2021]
        ],
    ],
    11 => [
        [
            'name' => '文化の日',
            'day' => 3,
        ],
        [
            'name' => '勤労感謝の日',
            'day' => 23,
        ],
    ],
    12 => [
        [
            'name' => '天皇誕生日', # 平成天皇
            'day' => 23,
            'during' => [1988, 2017],
        ],
    ],
];