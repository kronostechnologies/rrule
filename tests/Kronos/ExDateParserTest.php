<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */

use Kronos\ExDate;
use Kronos\ExDate\Exceptions\InvalidExDate;
use PHPUnit\Framework\TestCase;

class ExDateParserTest extends TestCase {


	public function testExdateStringIsNotPresent() {
	    $this->expectException(InvalidExDate::class);

        ExDate::fromRawExDate('');
	}

	public function testExdateStringIsPresentButNotAtStart() {
        $this->expectException(InvalidExDate::class);

        ExDate::fromRawExDate('it is here: EXDATE:');
	}

	public function testNoDateInExdate() {
        $this->expectException(InvalidExDate::class);

        ExDate::fromRawExDate('EXDATE:');
	}

	public function testBadFormattedDateInExDate() {
        $this->expectException(InvalidExDate::class);

        ExDate::fromRawExDate('EXDATE:0293-0123-2');
	}

	public function testParseCorrectlyFormattedExDate() {
		$exdate = ExDate::fromRawExDate('EXDATE:20000101T000000Z,20000201T000000Z');
		self::assertCount(2, $exdate->getExceptionDates());
		self::assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = ExDate::fromRawExDate('EXDATE:20000101T000000Z,20000201T000000');
		self::assertCount(2, $exdate->getExceptionDates());
		self::assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = ExDate::fromRawExDate('EXDATE:20000101,20000201T000000');
		self::assertCount(2, $exdate->getExceptionDates());
		self::assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());

		$exdate = ExDate::fromRawExDate('EXDATE:20000101,20000201T000000Z,');
		self::assertCount(2, $exdate->getExceptionDates());
		self::assertCount(2, $exdate->getExceptionDatesInCurrentTimezone());
	}

	public function testExceptionIsProperlyFilled(){
		try {
            ExDate::fromRawExDate('missing starting exdate');
		}
		catch(InvalidExDate $e) {
			self::assertEquals('missing starting exdate', $e->getExdate());
		}
	}
}
