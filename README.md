![Logo](kscal_sm.png)
# KsCal: A pure PHP business calendar library

# Introduction

*KsCal* is a pure PHP business calendar library. It is general-purpose that supports public holidays,  business/non-business days, and availability management. It is rule-based where days specified by rules such as second Monday one or every month, 

- Rules for **days**, **weeks** and **weekdays**.  E.g., `week=[2,4], wday=[3]` for *2nd* and *4th* Wednesdays.
- constraints of appliable rules by `during` and `except`.
E.g., `during [2013, 2023]`,  `except [2017, 2019, 2021]` 

**holiday**,  `public` or `local`
**business day**, or **workday**
**non-business day** not equal to **holiday**, 
**availability**: Facility x Timeslots `available`, `reserved` 
**reservation**

`n_weeks`[int]: number of weeks in a month
`n_days`, `lastday`[int]: number of days in a month

`week`[int], array[int]: week number, week of the month
`wday`[int], array[int]: weekday, day of the week, `[0 : 6]`
`day` [int], array[int]: day number, day of the month, `[1 : n_days]`



# Highlights
