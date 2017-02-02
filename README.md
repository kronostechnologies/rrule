# Kronos\RRule

Object oriented class to parse rrule and exdate.

## Installation

```
composer require kronos\rrule
```

## Test

```
composer install
./vendor/phpunit/phpunit/phpunit
```

## RRule Usage

### Generate Raw RRule

```
$rrule = new \Kronos\RRule();
$rrule->setByDay(array(\Kronos\RRule\Enums\Days::FRIDAY));
$rrule->setByHour(array('1'));
$rrule->setByMinute(array('1'));
$rrule->setByMonth(array('1'));
$rrule->setByMonthDay(array('1'));
$rrule->setBySecond(array('1'));
$rrule->setBySetPos(array('1'));
$rrule->setByWeekNo(array('1'));
$rrule->setByYearDay(array('1'));
$rrule->setCount(1);
$rrule->setFrequency(\Kronos\RRule\Enums\Frequencies::DAILY);
$rrule->setInterval(1);
$rrule->setUntil(new \DateTime('1980-08-08'));
$rrule->setWkst(\Kronos\RRule\Enums\Days::MONDAY);

echo $rrule->generateRawRRule();
```

### Parse Raw RRule

```
$raw_rrule = 'RRULE:BYMONTHDAY=20,21';
$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
```

## ExDate Usage

### Generate Raw ExDate

```
$timezone = new \DateTimeZone('UTC');
$exdate->setExceptionDates([
	new \DateTime('2000-01-01 00:00:00', $timezone),
	new \DateTime('2000-02-01 01:00:00', $timezone),
]);

echo $exdate->generateRawExDate();
```

### Parse Raw ExDate

```
$exdate = \Kronos\Exdate::fromRawExDate('EXDATE:20000101T000000Z,20000201T000000Z');
```
