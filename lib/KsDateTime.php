<?php

namespace kcal;

class KsDateTime extends \DateTime{

    /* Format characters   
     * DateTime  : [AB.D.FGHI..LMNOP..STU.WXYZ a.cde.ghij.lmnop.rstuvwxyz]
     * KsDateTime: [EJKQR.bkq], not used [CV.f]
     */ 
    private const HELP = <<<'EOT'
Japanese extensions to standard `DateTime` class (和暦などformatを追加)
追加した記号:
J : 元号(漢字)。例：昭和
R : 元号略称(ローマ字)。例：S
K : 和暦年(1年を元年と表記)。例：元、2、3
k : 和暦年(数字のみ)。例：1、2、3
Q : 西暦年度(数字。4月～新年度)。例：2019
q : 和暦年度(数字。4月～新年度)。例：2
b : 日本語曜日(漢字一文字)。例：火、木、土
E : 午前午後

使用中のフォーマット文字:( )内はKsDateTimeに追加されるもの「.」未使用
  AB.D(E)FGHI(JK)LMNOP(QR)STU.WXYZa(b)cde.ghij(k)lmnop(q)rstuvwxyz

例) $dt = new KsDateTime("1965/10/18 16:10");
    echo $dt->format("JK年n月j日(x) Eg:i"); // 昭和40年10月18日(月) 午後4:10
EOT; // single-quoted dochere string

    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s'; // toString()で利用する表示フォーマット
    const DATETIME_FORMAT = '/(?<!\\\)[ABDFGHILMNOPSTUWXYZacdeghijlmnoprstuvwxyz]/';
    const KSDATETIME_FORMAT = '/(?<!\\\)[EJKQRkbq]/';

    private static $gengoNameList = [
        ['name' => '令和', 'romaji' => 'R', 'since' => '2019-05-01'],  
        ['name' => '平成', 'romaji' => 'H', 'since' => '1989-01-08'],  
        ['name' => '昭和', 'romaji' => 'S', 'since' => '1926-12-25'],  
        ['name' => '大正', 'romaji' => 'T', 'since' => '1912-07-30'],  
        ['name' => '明治', 'romaji' => 'M', 'since' => '1868-01-25'],  
    ];    

   // 日本語曜日設定 
    private static $weekJp = [
        0=>'日', 1=>'月', 2=>'火', 3=>'水', 4=>'木', 5=>'金', 6=>'土' 
    ];

    // 午前午後 
    private static $ampm = ['am'=>'午前',  'pm'=>'午後' ];

    /** 文字列に変換された際に返却するもの */
    public function __toString()
    {
        return $this->format(self::DEFAULT_TO_STRING_FORMAT);
    }
    
    public static function help()
    {
        return self::HELP;
    }

    /** 和暦などを追加したformatメソッド  */
    public function format($format): string
    {
        if (! preg_match(self::KSDATETIME_FORMAT, $format)){
            return parent::format($format);
        }

        $now = $this->getTimestamp();
        $gengo = $this->getGengo($now);
        if (empty($gengo)) {
            throw new \Exception('No available Gengo for timestamp '. $now);
        }

        $since = new \DateTime($gengo['since']);        
        $m = date('n', $now);
        $w = date('w', $now);
        $a = date('a', $now);

        $year0 = date('Y', $now); //西暦年
        $year1 = $year0 - $since->format('Y') + 1; //和暦年
        $year2 =  $m < 4 ? $year0 - 1 : $year0; //西暦年度
        $year3 =  $m < 4 ? $year1 - 1 : $year1; //和暦年度
        $am_pm = isset(self::$ampm[$a]) ? self::$ampm[$a] : '';
    
        $format_chars = [
            'J' => $gengo['name'],  // 元号(漢字)
            'R' => '\\' . $gengo['romaji'], // 元号略称(ローマ字),※元号ローマ字をエスケープ
            'K' => $year1==1 ? '元' : $year1, // 和暦用年(元年表示)
            'k' => $year1, // 和暦用年(数字のみ)
            'Q' => $year2, // 西暦年度(数字)。例：2020
            'q' => $year3, // 和暦年度(数字)。例：2
            'b' => self::$weekJp[$w], // 日本語曜日
            'E' => $am_pm, // 午前午後
            ];
        foreach ($format_chars as $symbol=>$value){
            if ($this->hasChar($symbol, $format)){
                $format = $this->replaceChar($symbol, $value, $format);
            }  
        }
        return parent::format($format);
    }

    /** 指定した文字があるかどうか調べる（エスケープされているものは除外） */
    private function hasChar($char, $string)
    {  // 否定後読み(?<!パターン) ..「\」以外で始まる対象文字にマッチ
        return preg_match('/(?<!\\\)' . $char . '/', $string); 
    }

    /** 指定した文字を置換する(エスケープされていないもののみ) */
    private function replaceChar($char, $replace, $string)
    {
        $string = preg_replace('/(?<!\\\)' . $char . '/', '${1}'. $replace, $string);
        $string = preg_replace('/\\\\' . $char . '/', $char, $string); // エスケープ文字を削除
        return $string;
    }

    /** Lookup the gengo definition */
    private function getGengo($now)
    {
        $gengo = array();
        foreach (self::$gengoNameList as $g) {
            $since = new \DateTime($g['since']);
            if ($since->getTimestamp() <= $now) {
                $gengo = $g;
                break;
            }
        }
        return $gengo;
    }
}