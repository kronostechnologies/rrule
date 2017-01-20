<?php

namespace Kronos\Tests\RRule;

/*
 * This is the parsing test for the RRule parser.
 */
class RawRRuleGeneratorTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @var \Kronos\RRule
	 */
	protected $_rrule;
	
	public function setUp(){
		parent::setUp();
		
		$this->_rrule = new \Kronos\RRule();
	}
	
	public function testFreqIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::FREQ;
		$value = \Kronos\RRule\Enums\Frequencies::MINUTELY;
		
		$this->_rrule->setFrequency($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, $value) !== false);
	}
	
	public function testCountIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::COUNT;
		$value = '1';
		
		$this->_rrule->setCount($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, $value) !== false);
	}
	public function testIntervalIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::INTERVAL;
		$value = '1';
		
		$this->_rrule->setInterval($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, $value) !== false);
	}
	public function testWkstIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::WKST;
		$value = \Kronos\RRule\Enums\Days::FRIDAY;
		
		$this->_rrule->setWkst($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();

		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, $value) !== false);
	}
	public function testUntilIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::UNTIL;
		$value = new \DateTime('1988-08-08 13:00:00');
		$value->setTimezone(new \DateTimeZone('UTC'));
		
		$this->_rrule->setUntil($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, $value->format('Ymd\THis\Z')) !== false);
	}
	public function testBydayIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYDAY;
		$value = array('-1'.\Kronos\RRule\Enums\Days::FRIDAY, '+1'.\Kronos\RRule\Enums\Days::MONDAY, \Kronos\RRule\Enums\Days::THURSDAY);
		
		$this->_rrule->setByDay($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testByhourIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYHOUR;
		$value = array('1', '2');
		
		$this->_rrule->setByHour($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testByminuteIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYMINUTE;
		$value = array('1', '2');
		
		$this->_rrule->setByMinute($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testBymonthIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYMONTH;
		$value = array('1', '2');
		
		$this->_rrule->setByMonth($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testBymonthdayIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYMONTHDAY;
		$value = array('1', '2');
		
		$this->_rrule->setByMonthDay($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testBysecondIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYSECOND;
		$value = array('1', '2');
		
		$this->_rrule->setBySecond($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testBysetposIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYSETPOS;
		$value = array('1', '2');
		
		$this->_rrule->setBySetPos($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testByweeknoIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYWEEKNO;
		$value = array('1', '2');
		
		$this->_rrule->setByWeekNo($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	public function testByyeardayIsHere(){
		$parameter = \Kronos\RRule\Enums\Parameters::BYYEARDAY;
		$value = array('1', '2');
		
		$this->_rrule->setByYearDay($value);
		$generated_raw_rrule = $this->_rrule->generateRawRRule();
		
		$this->assertTrue(strpos($generated_raw_rrule, $parameter) !== false);
		$this->assertTrue(strpos($generated_raw_rrule, implode(',', $value)) !== false);
	}
	
	public function testAllParameterAreSet(){
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
		$this->_rrule->setFrequency(\Kronos\RRule\Enums\Frequencies::DAILY);
		$this->_rrule->setInterval(1);
		$this->_rrule->setUntil(new \DateTime('1988-08-08'));
		$this->_rrule->setWkst(\Kronos\RRule\Enums\Days::MONDAY);
		
		$this->assertNotEmpty($this->_rrule->generateRawRRule());
	}

	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testSetUntilThrowWhenInvalidDate(){
		$this->_rrule->setUntil(new \DateTime('0000-00-00'));
	}

	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testSetUntilAsStringThrowWhenInvalidDate(){
		$this->_rrule->setUntilAsString('0000-00-00');
	}

	public function testIsEndless(){
		$this->_rrule->setCount(1);
		$this->assertFalse($this->_rrule->isEndless());
		$this->_rrule->setCount(0);
		$this->assertTrue($this->_rrule->isEndless());
		$this->_rrule->setUntilAsString('2015-01-01');
		$this->assertFalse($this->_rrule->isEndless());
	}
}