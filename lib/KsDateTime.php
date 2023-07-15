<?php

namespace kcal;

/*
 *--------------------------------------------------------------------------
 * Japanese extensions to standard `DateTime` class (和暦などformatを追加)
 *    * 追加した記号
 *  J : 元号
 *  b : 元号略称(ローマ字)
 *  K : 和暦年(1年を元年と表記)
 *  k : 和暦年
 *  x : 日本語曜日(0:日-6:土)
 *  E : 午前午後
 *  X : 同じ週の初日（日曜日の日）。1から31
 *  Q : 西暦年度(n: n年4月1日～n+1年3月31日)。数字。例：2020,
 *  q : 和暦年度(n: 年号n年4月1日～年号n+1年3月31日)。数字。例：2
 * 
 * 例) $dt = new KsDateTime('1965/10/18 16:10');
 *　   echo $dt->format('JK年n月j日(x) Eg:m'); // 昭和40年10月18日(月) 午後4:10
 */

class KsDateTime extends \DateTime{

  const JP_DATE = 'JK年n月j日'; // 例:平成元年5月7日（明治5年以前は当時と異なる日付が出るので注意）
  const JP_TIME = 'Eg時i分s秒'; // 例:午後3時05分07秒

  const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s'; // toString()で利用する表示フォーマット

  /**
   * 元号用設定
   * timestamp  1970-01-01からの秒数
   *  $date = new DateTime('1989-01-08'); // 平成開始
   *  echo $date->getTimeStamp();  // 600188400
   */
  private static$jpYearNameList = [
    ['name' => '令和', 'name_short' => 'R', 'timestamp' =>  1556636400], // 2019-05-01
    ['name' => '平成', 'name_short' => 'H', 'timestamp' =>  600188400 ], // 1989-01-08
    ['name' => '昭和', 'name_short' => 'S', 'timestamp' => -1357635600], // 1926-12-25
    ['name' => '大正', 'name_short' => 'T', 'timestamp' => -1812186000], // 1912-07-30
    ['name' => '明治', 'name_short' => 'M', 'timestamp' => -3216790800], // 1868-01-25
  ];

  /** 日本語曜日設定 */
  private static $weekJp = [
    0 => '日',
    1 => '月',
    2 => '火',
    3 => '水',
    4 => '木',
    5 => '金',
    6 => '土',
  ];
  /** 午前午後 */
  private static $ampm = [
    'am' => '午前',
    'pm' => '午後',
  ];

  /**
   * 文字列に変換された際に返却するもの
   *
   * @return string
   */
  public function __toString()
  {
    return $this->format(self::DEFAULT_TO_STRING_FORMAT);
  }
  
  /**
   * 和暦などを追加したformatメソッド
   * @param string $format DateTime::formatに準ずるformat文字列
   * @return string
   */
  public function format($format): string
  {

    // 和暦関連のオプションがある場合は和暦取得
   $jp_year = array();
    if (preg_match('/(?<!\\\)[J|b|K|k]/', $format)) {
      foreach (self::$jpYearNameList as $gengo) {
        if ($gengo['timestamp'] <= $this->getTimestamp()) {
           $jp_year = $gengo;
            break;
        }
      }
      // 元号が取得できない場合はException
      if (empty($jpYear)) {
        throw new \Exception('Invalid timestamp : '.$this->getTimestamp());
      }
    }

    // J : 元号
    if ($this->hasChar('J', $format)) {
      $format = $this->replaceChar('J',$jp_year['name'], $format);
    }

    // b : 元号略称(ローマ字)
    if ($this->hasChar('b', $format)) {
      $format = preg_replace('/b/',$jp_year['name_short'], $format);
    }

    // K : 和暦用年(元年表示)
    if ($this->hasChar('K', $format)) {
      $year = date('Y', $this->getTimestamp()) - date('Y',$jp_year['timestamp']) + 1;
      $year = $year == 1 ? '元' : $year;
      $format = $this->replaceChar('K', $year, $format);
    }

    // k : 和暦用年(数字)
    if ($this->hasChar('k', $format)) {
      $year = date('Y', $this->getTimestamp()) - date('Y',$jp_year['timestamp']) + 1;
      $format = $this->replaceChar('k', $year, $format);
    }

    // x : 日本語曜日
    if ($this->hasChar('x', $format)) {
      $w = date('w', $this->getTimestamp());
      $format = $this->replaceChar('x', self::$weekJp[$w], $format);
    }

    // 午前午後
    if ($this->hasChar('E', $format)) {
      $a = date('a', $this->getTimestamp());
      $value = isset(self::$ampm[$a]) ? self::$ampm[$a] : '';
      $format = $this->replaceChar('E', $value, $format);
    }

    // X : 週の初日（日曜日の日）。1から31
    if ($this->hasChar('X', $format)) {
      $w = date('w', $this->getTimestamp());
      $value = date('j', $this->getTimestamp() - $w * 24 * 60 * 60);
      $format = $this->replaceChar('X', $value, $format);
    }
    
    // Q : 西暦年度(n: n年4月1日～n+1年3月31日)。数字。例：2020,
    if ($this->hasChar('Q', $format)) {
      $month = date('n', $this->getTimestamp());
      $year  = date('Y', $this->getTimestamp());
      $value = (3 < $month and $month < 13  ) ? $year : $year - 1; 
      $format = $this->replaceChar('Q', $value, $format);
    }
    
    // q : 和暦年度(n: 年号n年4月1日～年号n+1年3月31日)。数字。例：2
    if ($this->hasChar('q', $format)) {
      $month = date('n', $this->getTimestamp());
      $year  = date('Y', $this->getTimestamp()) - date('Y',$jp_year['timestamp']) + 1;
      $value = (3 < $month and $month < 13  ) ? $year : $year - 1; 
      $format = $this->replaceChar('q', $value, $format);
    }
    return parent::format($format);
  }

  /**
   * 指定した文字があるかどうか調べる（エスケープされているものは除外）
   * @param string $char 検索する文字
   * @param string $string 検索される文字列
   * @return boolean
   */
  private function hasChar($char, $string)
  {
    return preg_match('/(?<!\\\)'.$char.'/', $string);
  }

  /**
   * 指定した文字を置換する(エスケープされていないもののみ)
   * @param string $char 置換される文字
   * @param string $replace 置換する文字列
   * @param string $string 元の文字列
   * @return string 置換した文字列
   */
  private function replaceChar($char, $replace, $string)
  {
    // エスケープされていないもののみ置換
    $string = preg_replace('/(?<!\\\)'.$char.'/', '${1}'.$replace, $string);
    // エスケープ文字を削除
    $string = preg_replace('/\\\\'.$char.'/', $char, $string);
    return $string;
  }
}