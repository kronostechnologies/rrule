<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */
class ExDateGenerateTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Kronos\ExDate
	 */
	protected $_exdate = null;

	public function setUp() {
		parent::setUp();

		$this->_exdate = new \Kronos\ExDate();
	}

	public function testGenerateWithOneDate() {
		$timezone = new \DateTimeZone('UTC');
		$this->_exdate->setExceptionDates([
			new \DateTime('2000-01-01 00:00:00', $timezone)
		]);
		$raw = $this->_exdate->generateRawExDate();
		$this->assertEquals('EXDATE:20000101T000000Z', $raw);
	}

	public function testGenerateWithTwoDate() {
		$timezone = new \DateTimeZone('UTC');
		$this->_exdate->setExceptionDates([
			new \DateTime('2000-01-01 00:00:00', $timezone),
			new \DateTime('2000-02-01 01:00:00', $timezone),
		]);
		$raw = $this->_exdate->generateRawExDate();
		$this->assertEquals('EXDATE:20000101T000000Z,20000201T010000Z', $raw);
	}

	/**
	 * @expectedException \Kronos\ExDate\Exceptions\InvalidExDate
	 */
	public function testValidateExdateThrowsWhenNoDate() {
		$this->_exdate->setExceptionDates([]);
		$this->_exdate->validate();
	}
}