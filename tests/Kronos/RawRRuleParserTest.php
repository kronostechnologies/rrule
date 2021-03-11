<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */

use Kronos\RRule;
use Kronos\RRule\Enums\Days;
use Kronos\RRule\Enums\Parameters;
use Kronos\RRule\Exceptions\InvalidParameterValue;
use Kronos\RRule\Exceptions\InvalidRRule;
use PHPUnit\Framework\TestCase;

class RawRRuleParserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorAssignCorrectDefaultValue()
    {
        $rrule = new RRule();
        self::assertEquals(null, $rrule->getInterval());
    }

    public function testMissingRRuleFirstTagThrowsCorrectException()
    {
        $this->expectException(InvalidRRule::class);
        RRule::fromRawRRule('RRuleis:ofwrongsetup!');
    }

    /**
     * @depends testMissingRRuleFirstTagThrowsCorrectException
     */
    public function testInvalidRRuleExceptionContainsCorrectData()
    {
        $bad_raw_rrule = 'RRule:';
        try {
            RRule::fromRawRRule($bad_raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidRRule $e) {
            self::assertEquals($bad_raw_rrule, $e->getRRule());
        }
    }

    public function testFreqGetsProperlyFilled()
    {
        $parameter_value = \Kronos\RRule\Enums\Frequencies::DAILY;
        $raw_rrule = 'RRULE:FREQ=' . $parameter_value;

        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals($parameter_value, $rrule->getFrequency());
    }

    public function testExceptionGetsThrowWhenFreqRuleIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);
        $parameter_value = 'WRONG_VALUE';
        $raw_rrule = 'RRULE:FREQ=' . $parameter_value;

        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrowWhenFreqRuleIsMalformed
     */
    public function testProperExceptionDataWhenFreqRuleIsMalformed()
    {
        $parameter_value = '-1';
        $raw_rrule = 'RRULE:FREQ=' . $parameter_value;

        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals(Parameters::FREQ, $e->getParameterName());
            self::assertEquals($parameter_value, $e->getParameterValue());
        }
    }

    public function testUntilGetsProperlyFilled()
    {
        $parameter_value = '19870101T000000Z';

        $raw_rrule = 'RRULE:UNTIL=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals($parameter_value, $rrule->getUntil()->format('Ymd\THis\Z'));
    }

    public function testUntilIsInUTCTimezone()
    {
        $parameter_value = '19870101T000000Z';

        $raw_rrule = 'RRULE:UNTIL=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals('UTC', $rrule->getUntil()->getTimezone()->getName());
    }

    public function testUntilInCurrentTimezoneIsInCurrentTimezone()
    {
        $parameter_value = '19870101T000000Z';

        $raw_rrule = 'RRULE:UNTIL=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals(date_default_timezone_get(), $rrule->getUntilInDefaultTimezone()->getTimezone()->getName());
    }

    public function testExceptionGetsThrownWhenUntilIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);
        $parameter_value = '2012-01-01 14:14:14';

        $raw_rrule = 'RRULE:UNTIL=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenUntilIsMalformed
     */
    public function testProperExceptionDataWhenUntilIsMalformed()
    {
        $parameter_value = '-1';
        $raw_rrule = 'RRULE:UNTIL=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::UNTIL, $e->getParameterName());
        }
    }

    public function testIntervalGetsProperlyFilled()
    {
        $parameter_value = '1';

        $raw_rrule = 'RRULE:INTERVAL=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals($parameter_value, $rrule->getInterval());
    }

    public function testExceptionGetsThrownWhenIntervalIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);
        $parameter_value = '0';

        $raw_rrule = 'RRULE:INTERVAL=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenIntervalIsMalformed
     */
    public function testProperExceptionDataWhenIntervalIsMalformed()
    {
        $parameter_value = '-1';
        $raw_rrule = 'RRULE:INTERVAL=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::INTERVAL, $e->getParameterName());
        }
    }

    public function testWkstGetsProperlyFilled()
    {
        $parameter_value = Days::FRIDAY;

        $raw_rrule = 'RRULE:WKST=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals($parameter_value, $rrule->getWkst());
    }

    public function testExceptionGetsThrownWhenWkstIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);
        $parameter_value = 'MONDAY';

        $raw_rrule = 'RRULE:WKST=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenWkstIsMalformed
     */
    public function testProperExceptionDataWhenWkstIsMalformed()
    {
        $parameter_value = '-1';
        $raw_rrule = 'RRULE:WKST=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::WKST, $e->getParameterName());
        }
    }


    public function testCountGetsProperlyFilled()
    {
        $parameter_value = '2';

        $raw_rrule = 'RRULE:COUNT=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        self::assertEquals($parameter_value, $rrule->getCount());
    }

    public function testExceptionGetsThrownWhenCountIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);
        $parameter_value = '-12,29';

        $raw_rrule = 'RRULE:COUNT=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenCountIsMalformed
     */
    public function testProperExceptionDataWhenCountIsMalformed()
    {
        $parameter_value = '-12';
        $raw_rrule = 'RRULE:COUNT=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::COUNT, $e->getParameterName());
        }
    }


    public function testBydayGetsProperlyFilled()
    {
        $possible_parameter_values = Days::toArray();
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYDAY=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByDay();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testBydayGetsProperlyFilledWhenUsingIntegerInFrontOfValues()
    {
        $possible_parameter_values = array(
            '-1' . Days::FRIDAY,
            '+1' . Days::MONDAY,
            Days::THURSDAY
        );
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYDAY=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByDay();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenBydayIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = 'MO,TU,WE,TAH';

        $raw_rrule = 'RRULE:BYDAY=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenBydayIsMalformed
     */
    public function testProperExceptionDataWhenBydayIsMalformed()
    {
        $parameter_value = 'AS,MO';
        $raw_rrule = 'RRULE:BYDAY=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYDAY, $e->getParameterName());
        }
    }

    public function testByhourGetsProperlyFilled()
    {
        $possible_parameter_values = array();
        for ($i = 0; $i < 24; $i++) {
            $possible_parameter_values[] = $i;
        }
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYHOUR=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByHour();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenByhourIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '24,23';

        $raw_rrule = 'RRULE:BYHOUR=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenByhourIsMalformed
     */
    public function testProperExceptionDataWhenByhourIsMalformed()
    {
        $parameter_value = '-1,23';
        $raw_rrule = 'RRULE:BYHOUR=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYHOUR, $e->getParameterName());
        }
    }

    public function testByminuteGetsProperlyFilled()
    {
        $possible_parameter_values = array();
        for ($i = 0; $i < 60; $i++) {
            $possible_parameter_values[] = $i;
        }
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYMINUTE=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByMinute();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenByminuteIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '61,45';

        $raw_rrule = 'RRULE:BYMINUTE=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);
    }

    /**
     * @depends testExceptionGetsThrownWhenByminuteIsMalformed
     */
    public function testProperExceptionDataWhenByminuteIsMalformed()
    {
        $parameter_value = '-1,23';
        $raw_rrule = 'RRULE:BYMINUTE=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYMINUTE, $e->getParameterName());
        }
    }

    public function testBymonthGetsProperlyFilled()
    {
        $possible_parameter_values = \Kronos\RRule\Enums\Months::toArray();
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYMONTH=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByMonth();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenBymonthIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '0,1,13';

        $raw_rrule = 'RRULE:BYMONTH=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenBymonthIsMalformed
     */
    public function testProperExceptionDataWhenBymonthIsMalformed()
    {
        $parameter_value = '-1,0';

        $raw_rrule = 'RRULE:BYMONTH=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYMONTH, $e->getParameterName());
        }
    }

    public function testBymonthdayGetsProperlyFilled()
    {
        $possible_parameter_values = array();
        for ($i = -31; $i < 32; $i++) {
            if ($i == 0) {
                continue;
            }
            $possible_parameter_values[] = $i;
        }
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYMONTHDAY=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByMonthDay();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenBymonthdayIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '0,1,13';

        $raw_rrule = 'RRULE:BYMONTHDAY=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenBymonthdayIsMalformed
     */
    public function testProperExceptionDataWhenBymonthdayIsMalformed()
    {
        $parameter_value = '-32,5';

        $raw_rrule = 'RRULE:BYMONTHDAY=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYMONTHDAY, $e->getParameterName());
        }
    }

    public function testBysecondGetsProperlyFilled()
    {
        $possible_parameter_values = array();
        for ($i = 0; $i < 61; $i++) {
            if ($i == 0) {
                continue;
            }
            $possible_parameter_values[] = $i;
        }
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYSECOND=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getBySecond();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenBysecondIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '-1,3';

        $raw_rrule = 'RRULE:BYSECOND=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenBysecondIsMalformed
     */
    public function testProperExceptionDataWhenBysecondIsMalformed()
    {
        $parameter_value = '61,-8';

        $raw_rrule = 'RRULE:BYSECOND=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYSECOND, $e->getParameterName());
        }
    }

    public function testBysetposGetsProperlyFilled()
    {
        $possible_parameter_values = array(-366, -1, 1, 366);
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYSETPOS=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getBySetPos();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenBysetposIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '-367,355';

        $raw_rrule = 'RRULE:BYSETPOS=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenBysecondIsMalformed
     */
    public function testProperExceptionDataWhenBysetposIsMalformed()
    {
        $parameter_value = '0,300';

        $raw_rrule = 'RRULE:BYSETPOS=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYSETPOS, $e->getParameterName());
        }
    }

    public function testByweeknoGetsProperlyFilled()
    {
        $possible_parameter_values = array();
        for ($i = -53; $i++; $i < 54) {
            if ($i == 0) {
                continue;
            }
            $possible_parameter_values[] = $i;
        }
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYWEEKNO=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByWeekNo();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenByweeknoIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '0,51';

        $raw_rrule = 'RRULE:BYWEEKNO=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenBysecondIsMalformed
     */
    public function testProperExceptionDataWhenByweeknoIsMalformed()
    {
        $parameter_value = '52,-54';

        $raw_rrule = 'RRULE:BYWEEKNO=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYWEEKNO, $e->getParameterName());
        }
    }

    public function testByyeardayGetsProperlyFilled()
    {
        $possible_parameter_values = array(-366, -1, 1, 366);
        $parameter_value = implode(',', $possible_parameter_values);

        $raw_rrule = 'RRULE:BYYEARDAY=' . $parameter_value;
        $rrule = RRule::fromRawRRule($raw_rrule);

        $returned_value = $rrule->getByYearDay();
        self::assertIsArray($returned_value);
        self::assertEquals(count($possible_parameter_values), count($returned_value));

        foreach ($returned_value as $value) {
            self::assertTrue(in_array($value, $possible_parameter_values));
        }
    }

    public function testExceptionGetsThrownWhenByyeardayIsMalformed()
    {
        $this->expectException(InvalidParameterValue::class);

        $parameter_value = '0,366';

        $raw_rrule = 'RRULE:BYYEARDAY=' . $parameter_value;
        RRule::fromRawRRule($raw_rrule);

        return $parameter_value;
    }

    /**
     * @depends testExceptionGetsThrownWhenByyeardayIsMalformed
     */
    public function testProperExceptionDataWhenByyeardayIsMalformed()
    {
        $parameter_value = '300,-367';

        $raw_rrule = 'RRULE:BYYEARDAY=' . $parameter_value;
        try {
            RRule::fromRawRRule($raw_rrule);
            self::fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
        } catch (InvalidParameterValue $e) {
            self::assertEquals($parameter_value, $e->getParameterValue());
            self::assertEquals(Parameters::BYYEARDAY, $e->getParameterName());
        }
    }

}
