<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */
class ExDateParserTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \Kronos\ExDate\Exceptions\InvalidExdate
	 */
	public function testExdateStringIsNotPresent() {
		\Kronos\Exdate::fromRawExDate('');
	}

	/**
	 * @expectedException \Kronos\ExDate\Exceptions\InvalidExDate
	 */
	public function testExdateStringIsPresentButNotAtStart() {
		\Kronos\Exdate::fromRawExDate('it is here: EXDATE:');
	}

	/**
	 * @expectedException \Kronos\ExDate\Exceptions\InvalidExDate
	 */
	public function testNoDateInExdate() {
		\Kronos\Exdate::fromRawExDate('EXDATE:');
	}

	/**
	 * @expectedException \Kronos\ExDate\Exceptions\InvalidExDate
	 */
	public function testBadFormattedDateInExDate() {
		\Kronos\Exdate::fromRawExDate('EXDATE:0293-0123-2');
	}

	public function testParseCorrectlyFormattedExDate() {
		$exdate = \Kronos\Exdate::fromRawExDate('EXDATE:20000101T000000Z,20000201T000000Z');
		$this->assertCount(2, $exdate->getExceptionDates());
		$this->assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = \Kronos\Exdate::fromRawExDate('EXDATE:20000101T000000Z,20000201T000000');
		$this->assertCount(2, $exdate->getExceptionDates());
		$this->assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = \Kronos\Exdate::fromRawExDate('EXDATE:20000101,20000201T000000');
		$this->assertCount(2, $exdate->getExceptionDates());
		$this->assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = \Kronos\Exdate::fromRawExDate('EXDATE:20000101,20000201T000000Z,');
		$this->assertCount(2, $exdate->getExceptionDates());
		$this->assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());
	}

	public function testExceptionIsProperlyFilled(){
		try {
			\Kronos\Exdate::fromRawExDate('missing starting exdate');
		}
		catch(\Kronos\ExDate\Exceptions\InvalidExDate $e) {
			$this->assertEquals('missing starting exdate', $e->getExdate());
		}
	}
}