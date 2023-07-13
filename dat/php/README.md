##「国民の祝日」を次のように定める。
- 元日	1月1日	
- 成人の日	1月の第2月曜日[※曜日]	
- 建国記念の日	2月11日　[昭和４１年政令第３７６号]
- 天皇誕生日	2月23日[令和天皇2018.5.1~]、12月23日[平成天皇1988.2]、4月29日[昭和天皇]
- 春分の日	春分日
- 昭和の日	4月29日
- 憲法記念日	5月3日	
- みどりの日	5月4日	
- こどもの日	5月5日	
- 海の日	7月の第3月曜日[※曜日]  ※特例：2020年7月23日，2021年7月22日
- 山の日	8月11日　※特例：2020年8月10日、2021年8月8日 (＋8月9日 振替休日)
- 敬老の日	9月の第3月曜日[※曜日]	
- 秋分の日	秋分日
- スポーツの日	10月の第2月曜日[※曜日]	※特例：2020年7月24日、2021年7月23日
- 文化の日	11月3日	
- 勤労感謝の日	11月23日

- 振替休日
　「国民の祝日」が日曜日に当たるときは、
   その日後においてその日に最も近い「国民の祝日」でない日を休日とする。
- 休日
　前日と翌日の両方を「国民の祝日」に挟まれた平日は休日となる




Cf. https://mt-soft.sakura.ne.jp/kyozai/excel_high/200_jissen_kiso/60_syunbun.htm
下記の簡易計算式により、１８５１年～２１５０年までの春分／秋分日は求
              `A`         `B`          
1851 - 1899   19.8277   22.2588
1900 - 1979   20.8357   23.2588
1980 - 2099   20.8431   23.2488
2100 - 2150   21.8510   24.2488

`D = 0.242194`

春分の日`X = INT[ A + D * (西暦年-1980)　- INT[(西暦年-1980)/4] ]`
秋分の日`X = INT[ B + D * (西暦年-1980)　- INT[(西暦年-1980)/4] ]`