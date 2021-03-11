<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */

use Kronos\RRule;
use Kronos\RRule\Enums\Frequencies;
use Kronos\RRule\Enums\Parameters;
use Kronos\RRule\Exceptions\InvalidParameterValue;
use PHPUnit\Framework\TestCase;

class RawRRuleGeneratorTest extends TestCase
{

    /**
     * @var RRule
     */
    protected $_rrule;

    public function setUp(): void
    {
        parent::setUp();

        $this->_rrule = new RRule();
    }

    public function testFreqIsHere()
    {
        $parameter = Parameters::FREQ;
        $value = Frequencies::MINUTELY;

        $this->_rrule->setFrequency($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, $value) !== false);
    }

    public function testCountIsHere()
    {
        $parameter = Parameters::COUNT;
        $value = 1;

        $this->_rrule->setCount($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, (string)$value) !== false);
    }

    public function testIntervalIsHere()
    {
        $parameter = Parameters::INTERVAL;
        $value = 1;

        $this->_rrule->setInterval($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, (string)$value) !== false);
    }

    public function testWkstIsHere()
    {
        $parameter = Parameters::WKST;
        $value = \Kronos\RRule\Enums\Days::FRIDAY;

        $this->_rrule->setWkst($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, $value) !== false);
    }

    public function testUntilIsHere()
    {
        $parameter = Parameters::UNTIL;
        $value = new \DateTime('1988-08-08 13:00:00');
        $value->setTimezone(new \DateTimeZone('UTC'));

        $this->_rrule->setUntil($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, $value->format('Ymd\THis\Z')) !== false);
    }

    public function testBydayIsHere()
    {
        $parameter = Parameters::BYDAY;
        $value = array(
            '-1' . \Kronos\RRule\Enums\Days::FRIDAY,
            '+1' . \Kronos\RRule\Enums\Days::MONDAY,
            \Kronos\RRule\Enums\Days::THURSDAY
        );

        $this->_rrule->setByDay($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testByhourIsHere()
    {
        $parameter = Parameters::BYHOUR;
        $value = array('1', '2');

        $this->_rrule->setByHour($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testByminuteIsHere()
    {
        $parameter = Parameters::BYMINUTE;
        $value = array('1', '2');

        $this->_rrule->setByMinute($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testBymonthIsHere()
    {
        $parameter = Parameters::BYMONTH;
        $value = array('1', '2');

        $this->_rrule->setByMonth($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testBymonthdayIsHere()
    {
        $parameter = Parameters::BYMONTHDAY;
        $value = array('1', '2');

        $this->_rrule->setByMonthDay($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testBysecondIsHere()
    {
        $parameter = Parameters::BYSECOND;
        $value = array('1', '2');

        $this->_rrule->setBySecond($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testBysetposIsHere()
    {
        $parameter = Parameters::BYSETPOS;
        $value = array('1', '2');

        $this->_rrule->setBySetPos($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testByweeknoIsHere()
    {
        $parameter = Parameters::BYWEEKNO;
        $value = array('1', '2');

        $this->_rrule->setByWeekNo($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testByyeardayIsHere()
    {
        $parameter = Parameters::BYYEARDAY;
        $value = array('1', '2');

        $this->_rrule->setByYearDay($value);
        $generated_raw_rrule = $this->_rrule->generateRawRRule();

        self::assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
        self::assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
    }

    public function testAllParameterAreSet()
    {
        $this->_rrule->setByDay(array(\Kronos\RRule\Enums\Days::FRIDAY));
        $this->_rrule->setByHour(array('1'));
        $this->_rrule->setByMinute(array('1'));
        $this->_rrule->setByMonth(array('1'));
        $this->_rrule->setByMonthDay(array('1'));
        $this->_rrule->setBySecond(array('1'));
        $this->_rrule->setBySetPos(array('1'));
        $this->_rrule->setByWeekNo(array('1'));
        $this->_rrule->setByYearDay(array('1'));
        $this->_rrule->setCount(1);
        $this->_rrule->setFrequency(Frequencies::DAILY);
        $this->_rrule->setInterval(1);
        $this->_rrule->setUntil(new \DateTime('1988-08-08'));
        $this->_rrule->setWkst(\Kronos\RRule\Enums\Days::MONDAY);

        self::assertNotEmpty($this->_rrule->generateRawRRule());
    }

    public function testSetUntilThrowWhenInvalidDate()
    {
        $this->expectException(InvalidParameterValue::class);

        $this->_rrule->setUntil(new \DateTime('0000-00-00'));
    }

    public function testSetUntilAsStringThrowWhenInvalidDate()
    {
        $this->expectException(InvalidParameterValue::class);

        $this->_rrule->setUntilAsString('0000-00-00');
    }

    public function testIsEndless()
    {
        $this->_rrule->setCount(1);
        self::assertFalse($this->_rrule->isEndless());
        $this->_rrule->setCount(0);
        self::assertTrue($this->_rrule->isEndless());
        $this->_rrule->setUntilAsString('2015-01-01');
        self::assertFalse($this->_rrule->isEndless());
    }
}
