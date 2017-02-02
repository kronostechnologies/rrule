<?php

namespace Kronos\Tests;

class ExDateDateTimeSetterTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Kronos\ExDate
	 */
	protected $_exdate = null;

	public function setUp(){
		parent::setUp();

		$this->_exdate = new \Kronos\ExDate();
	}

	public function testGetExceptionDatesReturnsDateInUtcTimezone(){
		\date_default_timezone_set('America/Montreal');

		$timezone = new \DateTimeZone('Pacific/Nauru');
		$this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
		$this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));

		$this->assertNotEmpty($this->_exdate->getExceptionDates());
		$this->assertCount(2, $this->_exdate->getExceptionDates());
		foreach($this->_exdate->getExceptionDates() as $date) {
			$this->assertEquals($date->getTimezone()->getName(), 'UTC');
		}
	}

	public function testGetExceptionDatesInCurrentTimezone() {
		\date_default_timezone_set('America/Montreal');

		$timezone = new \DateTimeZone('Pacific/Nauru');
		$this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
		$this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));

		$this->assertNotEmpty($this->_exdate->getExceptionDatesInCurrentTimezone());
		$this->assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
		foreach($this->_exdate->getExceptionDatesInCurrentTimezone() as $date) {
			$this->assertEquals($date->getTimezone()->getName(), 'America/Montreal');
		}
	}

	public function testSetExceptionDatesProperlyOverwriteDateArray() {
		$timezone = new \DateTimeZone('Pacific/Nauru');
		$this->_exdate->setExceptionDates([
			new \DateTime('2000-01-01', $timezone),
			new \DateTime('2000-02-01', $timezone),
		]);
		$this->assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
		$this->assertCount(2, $this->_exdate->getExceptionDates());

		$this->_exdate->setExceptionDates([
			new \DateTime('2000-01-01', $timezone),
			new \DateTime('2000-02-01', $timezone),
			new \DateTime('2000-03-01', $timezone),
		]);
		$this->assertCount(3, $this->_exdate->getExceptionDatesInCurrentTimezone());
		$this->assertCount(3, $this->_exdate->getExceptionDates());
	}

	public function testAddExceptionDateProperlyAppendToDateArray() {
		$timezone = new \DateTimeZone('Pacific/Nauru');
		$this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
		$this->assertCount(1, $this->_exdate->getExceptionDatesInCurrentTimezone());
		$this->assertCount(1, $this->_exdate->getExceptionDates());

		$this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));
		$this->assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
		$this->assertCount(2, $this->_exdate->getExceptionDates());
	}
}