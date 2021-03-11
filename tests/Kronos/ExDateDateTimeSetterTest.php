<?php

namespace Kronos\Tests;

use Kronos\ExDate;
use PHPUnit\Framework\TestCase;

use function date_default_timezone_set;

class ExDateDateTimeSetterTest extends TestCase
{
    /**
     * @var ExDate
     */
    protected $_exdate;

    public function setUp(): void
    {
        parent::setUp();

        $this->_exdate = new ExDate();
    }

    public function testGetExceptionDatesReturnsDateInUtcTimezone()
    {
        date_default_timezone_set('America/Montreal');

        $timezone = new \DateTimeZone('Pacific/Nauru');
        $this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
        $this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));

        self::assertNotEmpty($this->_exdate->getExceptionDates());
        self::assertCount(2, $this->_exdate->getExceptionDates());
        foreach ($this->_exdate->getExceptionDates() as $date) {
            self::assertEquals($date->getTimezone()->getName(), 'UTC');
        }
    }

    public function testGetExceptionDatesInCurrentTimezone()
    {
        date_default_timezone_set('America/Montreal');

        $timezone = new \DateTimeZone('Pacific/Nauru');
        $this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
        $this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));

        self::assertNotEmpty($this->_exdate->getExceptionDatesInCurrentTimezone());
        self::assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
        foreach ($this->_exdate->getExceptionDatesInCurrentTimezone() as $date) {
            self::assertEquals($date->getTimezone()->getName(), 'America/Montreal');
        }
    }

    public function testSetExceptionDatesProperlyOverwriteDateArray()
    {
        $timezone = new \DateTimeZone('Pacific/Nauru');
        $this->_exdate->setExceptionDates([
            new \DateTime('2000-01-01', $timezone),
            new \DateTime('2000-02-01', $timezone),
        ]);
        self::assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
        self::assertCount(2, $this->_exdate->getExceptionDates());

        $this->_exdate->setExceptionDates([
            new \DateTime('2000-01-01', $timezone),
            new \DateTime('2000-02-01', $timezone),
            new \DateTime('2000-03-01', $timezone),
        ]);
        self::assertCount(3, $this->_exdate->getExceptionDatesInCurrentTimezone());
        self::assertCount(3, $this->_exdate->getExceptionDates());
    }

    public function testAddExceptionDateProperlyAppendToDateArray()
    {
        $timezone = new \DateTimeZone('Pacific/Nauru');
        $this->_exdate->addExceptionDate(new \DateTime('2000-01-01', $timezone));
        self::assertCount(1, $this->_exdate->getExceptionDatesInCurrentTimezone());
        self::assertCount(1, $this->_exdate->getExceptionDates());

        $this->_exdate->addExceptionDate(new \DateTime('2000-02-01', $timezone));
        self::assertCount(2, $this->_exdate->getExceptionDatesInCurrentTimezone());
        self::assertCount(2, $this->_exdate->getExceptionDates());
    }
}
