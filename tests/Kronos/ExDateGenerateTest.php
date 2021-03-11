<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */

use Kronos\ExDate;
use Kronos\ExDate\Exceptions\InvalidExDate;
use PHPUnit\Framework\TestCase;

class ExDateGenerateTest extends TestCase
{
    /**
     * @var ExDate
     */
    protected $_exdate = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->_exdate = new ExDate();
    }

    public function testGenerateWithOneDate()
    {
        $timezone = new \DateTimeZone('UTC');
        $this->_exdate->setExceptionDates([
            new \DateTime('2000-01-01 00:00:00', $timezone)
        ]);
        $raw = $this->_exdate->generateRawExDate();
        self::assertEquals('EXDATE:20000101T000000Z', $raw);
    }

    public function testGenerateWithTwoDate()
    {
        $timezone = new \DateTimeZone('UTC');
        $this->_exdate->setExceptionDates([
            new \DateTime('2000-01-01 00:00:00', $timezone),
            new \DateTime('2000-02-01 01:00:00', $timezone),
        ]);
        $raw = $this->_exdate->generateRawExDate();
        self::assertEquals('EXDATE:20000101T000000Z,20000201T010000Z', $raw);
    }

    public function testValidateExdateThrowsWhenNoDate()
    {
        $this->expectException(InvalidExDate::class);
        $this->_exdate->setExceptionDates([]);
        $this->_exdate->validate();
    }
}
