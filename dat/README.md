# 昭和二十三年法律第百七十八号「国民の祝日に関する法律」

## 第2条
「国民の祝日」を次のように定める。
- **元日**	1月1日	
- **成人の日**	1月の第2月曜日	
- **建国記念の日**	2月11日　※昭和４１年政令第３７６号
- **天皇誕生日**	2月23日
  - 令和天皇誕生日2月23日[2018.5.1～]
  - 平成天皇誕生日12月23日[1989.1.8～]
  - 昭和天皇誕生日4月29日 [1926.12.25～]
- **春分の日**	春分日
- **昭和の日**	4月29日
- **憲法記念日**	5月3日	
- **みどりの日**	5月4日	
- **こどもの日**	5月5日	
- **海の日**	7月の第3月曜日  ※特例：2020年7月23日，2021年7月22日
- **山の日**	8月11日　※特例：2020年8月10日、2021年8月8日 (＋8月9日 振替休日)
- **敬老の日**	9月の第3月曜日	
- **秋分の日**	秋分日
- **スポーツの日**	10月の第2月曜日	※特例：2020年7月24日、2021年7月23日
- **文化の日**	11月3日	
- **勤労感謝の日**	11月23日

## 第３条
「国民の祝日」は、休日とする。
2. **振替休日**　第3条第2項：「国民の祝日」が日曜日に当たるときは、その日後においてその日に最も近い「国民の祝日」でない日を休日とする。
3. **国民の休日**　第3条第3項：その前日と翌日の両方を「国民の祝日」に挟まれた平日は休日となる

**附則**
1. この法律は、公布の日(1948.)からこれを施行する。
2. 昭和二年勅令第二十五号は、これを廃止する。

# 春分の日・秋分の日

下記の簡易計算式により、１８５１年～２１５０年までの春分／秋分日は求めることができる。

春分の日`X = INT[ A + D * (西暦年-1980)　- INT[(西暦年-1980)/4] ]`

秋分の日`X = INT[ B + D * (西暦年-1980)　- INT[(西暦年-1980)/4] ]`

ここのパラメターは以下のようになっています。
- `D = 0.242194`
- `A`と`B`は年によって変わる。
  - `1851 - 1899`は   `A=19.8277`   `B=22.2588`
  - `1900 - 1979`は   `A=20.8357`   `B=23.2588`
  - `1980 - 2099`は   `A=20.8431`   `B=23.2488`
  - `2100 - 2150`は   `A=21.8510`   `B=24.2488`

Cf. https://mt-soft.sakura.ne.jp/kyozai/excel_high/200_jissen_kiso/60_syunbun.htm