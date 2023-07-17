![Logo](kscal_sm.png)
# KsCal: A pure PHP business calendar library

# Introduction

*KsCal* is a pure PHP business calendar library. It provides a general framework for definition and calculation of national holidays, business calendar (business days / non-business days) and facility availability. The calculation is rule-based.  

There are four types of dates:  (1) **holiday**, either`national` or `local`   (2) **business day**, or **workday**
(3) **business weekday**, (4) **non-business weekday**, (5) **availability**,  either `available` or `reserved`.   

Note that a *local holiday* will ALWAYS be a non-business day, no matter how it was defined elsewhere. A *local workday* will ALWAYS be a business day, no matter how it was defined elsewhere.  A *national holiday* will be a non-business day unless it is explicitly defined as a local workday. 

# Rule Specification
## Rule Specification for Holiday

 For a given  **month**, the specification of rules for holidays is given by 

- `name` :  holiday name, business day's name, or a reservation name.  
- `day` : definition of a day, which can be a fixed day e.g., `12` or a fixed weekday, `[3, 1]`, that is the 3rd Monday. 
- `for`, `in`, `except` : define when the rule is valid. E.g., `for [2016, 2022] except [2017, 2021]` , is the same as`in [2016, 2018, 2019, 2020, 2022]`.

## Rule Specification for Business Calendar
For a given a **year**,  the specification of rules for business calendar is given by  

-  `type`, type of the calendar days, such as  *local_holiday*, *local_workday*
-  `days`, a list of individual days, each is given by a pair of a date in format of`mm-dd` and a name. E.g., `'10-12' : 'temporary workday'`  
- `week`, `wday`, routine business days or non-business days defined by  specifying weekdays in  some weeks  E.g., `(week=[2,4], wday=[3])`  means the *2nd* and *4th* Wednesdays.
- `month`, a list of months for which the rule is valid. 

-  **reservation**, reserved timespans or timeslots for a specific facility


# Classes
## KsCalendar Class

Paragraph paragraph paragraph paragraph

## KsHoliday Class

KsHoliday class Paragraph paragraph paragraph paragraph 

## Availability Class

Paragraph paragraph paragraph paragraph 

## KsDateTime Class

KsDateTime class is an extension of  *DateTime* class by introducing new format characters for Japanese calendar. The following characters are recognized in the format parameter string.

| format character | Description | Example returned values |
| ---------------- | ----------- | ----------------------- |
| *DateTime* | class | ---- |
| 	a	| 	Lowercase Ante meridiem and Post meridiem	| 	am or pm	|
| 	A	| 	Uppercase Ante meridiem and Post meridiem	| 	AM or PM	|
| 	B	| 	Swatch Internet time	| 	000 through 999	|
| 	c	| 	ISO 8601 date	| 	2004-02-12T15:19:21+00:00	|
| 	d	| 	Day of the month, 2 digits with leading zeros	| 	01 to 31	|
| 	D	| 	A textual representation of a day, three letters	| 	Mon through Sun	|
| 	e	| 	Timezone identifier	| 	Examples: UTC, GMT, Atlantic/Azores	|
| 	F	| 	A full textual representation of a month, such as January or March	| 	January through December	|
| 	g	| 	12-hour format of an hour without leading zeros	| 	1 through 12	|
| 	G	| 	24-hour format of an hour without leading zeros	| 	0 through 23	|
| 	h	| 	12-hour format of an hour with leading zeros	| 	01 through 12	|
| 	H	| 	24-hour format of an hour with leading zeros	| 	00 through 23	|
| 	i	| 	Minutes with leading zeros	| 	00 to 59	|
| 	I	| 	Whether or not the date is in daylight saving time	| 	1 if Daylight Saving Time, 0 otherwise.	|
| 	j	| 	Day of the month without leading zeros	| 	1 to 31	|
| 	L	| 	Whether it's a leap year	| 	1 if it is a leap year, 0 otherwise.	|
| 	l 	| 	A full textual representation of the day of the week	| 	Sunday through Saturday	|
| 	m	| 	Numeric representation of a month, with leading zeros	| 	01 through 12	|
| 	M	| 	A short textual representation of a month, three letters	| 	Jan through Dec	|
| 	n	| 	Numeric representation of a month, without leading zeros	| 	1 through 12	|
| 	N	| 	ISO 8601 numeric representation of the day of the week	| 	1 (for Monday) through 7 (for Sunday)	|
| 	o	| 	ISO 8601 week-numbering year. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.	| 	Examples: 1999 or 2003	|
| 	O	| 	Difference to Greenwich time (GMT) without colon between hours and minutes	| 	Example: +0200	|
| 	p	| 	The same as P, but returns Z instead of +00:00 (available as of PHP 8.0.0)	| 	Examples: Z or +02:00	|
| 	P	| 	Difference to Greenwich time (GMT) with colon between hours and minutes	| 	Example: +02:00	|
| 	r	| 	» RFC 2822/» RFC 5322 formatted date	| 	Example: Thu, 21 Dec 2000 16:01:07 +0200	|
| 	s	| 	Seconds with leading zeros	| 	00 through 59	|
| 	S	| 	English ordinal suffix for the day of the month, 2 characters	| 	st, nd, rd or th. Works well with j	|
| 	t	| 	Number of days in the given month	| 	28 through 31	|
| 	T	| 	Timezone abbreviation, if known; otherwise the GMT offset.	| 	Examples: EST, MDT, +05	|
| 	u	| 	Microseconds. Note that date() will always generate 000000 since it takes an int parameter, whereas DateTime::format() does support microseconds if DateTime was created with microseconds.	| 	Example: 654321	|
| 	U	| 	Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)	| 	See also time()	|
| 	v	| 	Milliseconds. Same note applies as for u.	| 	Example: 654	|
| 	w	| 	Numeric representation of the day of the week	| 	0 (for Sunday) through 6 (for Saturday)	|
| 	W	| 	ISO 8601 week number of year, weeks starting on Monday	| 	Example: 42 (the 42nd week in the year)	|
| 	x	| 	An expanded full numeric representation if requried, or a standard full numeral representation if possible (like Y). At least four digits. Years BCE are prefixed with a -. Years beyond (and including) 10000 are prefixed by a +.	| 	Examples: -0055, 0787, 1999, +10191	|
| 	X	| 	An expanded full numeric representation of a year, at least 4 digits, with - for years BCE, and + for years CE.	| 	Examples: -0055, +0787, +1999, +10191	|
| 	y	| 	A two digit representation of a year	| 	Examples: 99 or 03	|
| 	Y	| 	A full numeric representation of a year, at least 4 digits, with - for years BCE.	| 	Examples: -0055, 0787, 1999, 2003, 10191	|
| 	z	| 	The day of the year (starting from 0)	| 	0 through 365	|
| 	Z	| 	Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.	| 	-43200 through 50400	|
| *KsDateTime*     | class	| ----	|
| 	J 	| 	元号 (漢字)。	| 	例：昭和	|
| 	R 	| 	元号略称 (ローマ字)。	| 	例：S	|
| 	K 	| 	和暦年 (1年を元年と表記)。	| 	例：元、2、3	|
| 	k 	| 	和暦年 (数字のみ)。	| 	例：1、2、3	|
| 	Q 	| 	西暦年度 (数字。4月～新年度)。	| 	例：2019	|
| 	q 	| 	和暦年度 (数字。4月～新年度)。	| 	例：2	|
| 	b 	| 	日本語曜日 (漢字一文字)。	| 	例：火、木、土	|
| 	E 	| 	 午前午後	| 	|

