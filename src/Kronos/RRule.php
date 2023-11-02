<?php

// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore

namespace Kronos;

use DateTime;
use Kronos\RRule\Enums\Frequencies;
use Kronos\RRule\Enums\Parameters;
use Kronos\RRule\Exceptions\InvalidParameterValue;
use Kronos\RRule\Exceptions\InvalidRRule;
use Kronos\RRule\Exceptions\InvalidRRuleState;

/**
 * RRULE parser for RFC 5545
 */
class RRule
{
    /**
     * UTC until date format that is used in the generated RRule string.
     */
    public const RRULE_UNTIL_DATE_FORMAT = 'Ymd\THis\Z';

    /**
     * @var string
     */
    protected $_frequency;
    /**
     * @var DateTime
     */
    protected $_until;
    /**
     * @var DateTime
     */
    protected $_until_in_current_timezone;
    /**
     * @var int >=0
     */
    protected $_count;
    /**
     * @var int >=1
     */
    protected $_interval;
    /**
     * @var array
     */
    protected $_byday = [];
    /**
     * @var array
     */
    protected $_bymonthday = [];
    /**
     * @var array
     */
    protected $_byyearday = [];
    /**
     * @var array
     */
    protected $_byweekno = [];
    /**
     * @var array
     */
    protected $_bymonth = [];
    /**
     * @var array
     */
    protected $_bysetpos = [];
    /**
     * @var string
     */
    protected $_wkst;
    /**
     * @var array
     */
    protected $_bysecond = [];
    /**
     * @var array
     */
    protected $_byminute = [];
    /**
     * @var array
     */
    protected $_byhour = [];

    /**
     * @param string $value Defined in RRule\\Kronos\RRule\Enums\Frequencies
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setFrequency($value)
    {
        if (Frequencies::inEnum($value)) {
            $this->_frequency = $value;
            return $this;
        }
        throw new InvalidParameterValue(
            Parameters::FREQ,
            $value,
            'The given frequency is not valid. Look the definition of frequencies in \Kronos\RRule\Enums\Frequencies'
        );
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->_frequency;
    }

    /**
     * Will convert the timezone of $value to UTC with setTimezone('UTC').
     * Consider using the function setUntilAsString.
     * @param DateTime $value
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setUntil(DateTime $value)
    {
        if (!self::validateUntilDate($value)) {
            throw new InvalidParameterValue(
                Parameters::UNTIL,
                $value->format(self::RRULE_UNTIL_DATE_FORMAT),
                'The until date provided seems to be an invalid or malformed date.'
            );
        }

        $this->_until = clone $value;
        $this->_until_in_current_timezone = clone $value;

        $this->_until->setTimezone(new \DateTimeZone('UTC'));
        $this->_until_in_current_timezone->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $this;
    }

    /**
     * Parse the given $value to generate a new \DateTime object. Any warning or error during parsing will throw
     * \Kronos\RRule\Exceptions\InvalidParameterValue.
     * The warning and error are fetched using the \DateTime::getLastErrors static function
     * (http://php.net/manual/fr/datetime.getlasterrors.php).
     * @param string $value
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setUntilAsString($value)
    {
        $date = new DateTime($value);
        if ($error = DateTime::getLastErrors()) {
            $error_string = '';
            if ($error['warning_count'] > 0) {
                $error_string .= 'WARNINGS: ' . implode(', ', $error['warnings']) . ';';
            }
            if ($error['error_count'] > 0) {
                $error_string .= 'ERRORS: ' . implode(', ', $error['errors']) . ';';
            }
            if ($error_string) {
                throw new InvalidParameterValue(
                    Parameters::UNTIL,
                    $value,
                    'Could not parse Until date. ' . $error_string
                );
            }
        }

        $this->setUntil($date);
        return $this;
    }

    /**
     * Returns a DateTime object. The timezone is UTC so convert it if needed.
     * @return DateTime|null Null is returned if the until date is not present in the rrule or if it was not previously
     * set.
     */
    public function getUntil()
    {
        return $this->_until;
    }

    /**
     * Return the until date into the current timezone.
     * The object returned use the date_default_timezone_get() function to get the default timezone at the time of
     * assigning.
     * @return DateTime
     */
    public function getUntilInDefaultTimezone()
    {
        return $this->_until_in_current_timezone;
    }

    /**
     * 0 is a valid value however, setting a count to 0 would mean that the recurrence is inexistant therefor, the count
     * parameter won't be present in the generated rrule and act as if there is no Count parameter.
     * @param int $value >=0
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setCount($value)
    {
        if ($value >= 0) {
            $this->_count = $value;
            return $this;
        }

        throw new InvalidParameterValue(
            Parameters::COUNT,
            $value,
            'Count needs to be greater or equals to zero (>=0).'
        );
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * @param int $value >=1
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setInterval($value)
    {
        if ($value >= 1) {
            $this->_interval = $value;
            return $this;
        }

        throw new InvalidParameterValue(
            Parameters::INTERVAL,
            $value,
            'Interval needs to be greater or equals to 1 (>=1)'
        );
    }

    /**
     * @return int
     */
    public function getInterval()
    {
        return $this->_interval;
    }

    /**
     * @param array $values An array of value as defined in the Recurrence\\Kronos\RRule\Enums\Days enumeration
     * optionally preceded by a positive or negative integer i.e. -1MO, +1FR, etc.
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByDay(array $values)
    {
        $valid_day_values = implode('|', \Kronos\RRule\Enums\Days::toArray());

        foreach ($values as $value) {
            if (preg_match('/^([-+])?([1-9])?(' . $valid_day_values . ')+$/', $value, $matches) == false) {
                throw new InvalidParameterValue(
                    Parameters::BYDAY,
                    implode(',', $values),
                    'Byday value is not valid. It Must be positive (+) or negative (-) integer followed by ' .
                    'a valid day. ' .
                    'Look for Recurrence\\Kronos\RRule\Enums\Days as this gives you the valid days.' .
                    'The value that cause problem is *' . $value . '*'
                );
            }

            if (!empty($matches[2]) && ($matches[2] < 1 || $matches[2] > 53)) {
                throw new InvalidParameterValue(
                    Parameters::BYDAY,
                    implode(',', $values),
                    'Byday rule is invalid. The integer part of the rule is invalid.' .
                    'It must be greater or equals to 1 and smaller or equals to 53 (-53 to 53 excluding 0).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }

        $this->_byday = $values;
        return $this;
    }

    /**
     * Returns an array of values as defined in the Recurrence\\Kronos\RRule\Enums\Days enumeration optionally
     * preceded by a positive or negative integer i.e. -1MO, +1FR, etc.
     * @return array An array of string
     */
    public function getByDay()
    {
        return $this->_byday;
    }

    /**
     * @param array $values $values are integer and must respect the rule [-31, 1] & [1 to 31].
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByMonthDay(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < -31 || $value > 31 || $value == 0) {
                throw new InvalidParameterValue(
                    Parameters::BYMONTHDAY,
                    implode(',', $values),
                    'Bymonthday value is not valid. It must be greater or equals to -31 and smaller ' .
                    ' or equals to 31 and not be 0 (-31 to 31 excluding 0).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }
        $this->_bymonthday = $values;
        return $this;
    }

    /**
     * Returns an array of integer that are contained in [-31, 1] & [1, 31].
     * @return array Array of integer contained in [-31, 1] & [1, 31]
     */
    public function getByMonthDay()
    {
        return $this->_bymonthday;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByYearDay(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < -366 || $value == 0 || $value > 366) {
                throw new InvalidParameterValue(
                    Parameters::BYYEARDAY,
                    implode(',', $values),
                    'Byyearday value is not valid. It must be greater or equals to -366 and smaller ' .
                    ' or equals to 366 and not be 0 (-366 to 366 excluding 0).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }
        $this->_byyearday = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getByYearDay()
    {
        return $this->_byyearday;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByWeekNo(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < -53 || $value == 0 || $value > 53) {
                throw new InvalidParameterValue(
                    Parameters::BYWEEKNO,
                    implode(',', $values),
                    'Byweekno value is not valid. It must be greater or equals to -53 and smaller ' .
                    'or equals to 53 and not be 0 (-53 to 53 excluding 0).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }
        $this->_byweekno = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getByWeekNo()
    {
        return $this->_byweekno;
    }

    /**
     * Set the month value. It must be [1 to 12]. Use \Kronos\RRule\Enums\Month for reference.
     * @param array $values Defined in \Kronos\RRule\Enums\Months
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByMonth(array $values)
    {
        if (\Kronos\RRule\Enums\Months::inEnum($values)) {
            $this->_bymonth = $values;
            return $this;
        }

        throw new InvalidParameterValue(
            Parameters::BYMONTH,
            implode(',', $values),
            'Bymonth value is not valid. It must be greater or equals to 1 and smaller ' .
            'or equals to 12 (1 to 12). Look for \Kronos\RRule\Enums\Months for valid month value.'
        );
    }

    /**
     * Return an array of value as defined in the \Kronos\RRule\Enums\Months enumeration
     * @return array of value defined in \Kronos\RRule\Enums\Months
     */
    public function getByMonth()
    {
        return $this->_bymonth;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setBySetPos(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < -366 || $value == 0 or $value > 366) {
                throw new InvalidParameterValue(
                    Parameters::BYSETPOS,
                    implode(',', $values),
                    'Bysetpos value is not valid. It must be greater or equals to -366 and smaller ' .
                    ' or equals to 366 and not be 0 (-366 to 366 excluding 0).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }

        $this->_bysetpos = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getBySetPos()
    {
        return $this->_bysetpos;
    }

    /**
     * @param string $value Defined in Recurrence\\Kronos\RRule\Enums\Days
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setWkst($value)
    {
        if (\Kronos\RRule\Enums\Days::inEnum($value)) {
            $this->_wkst = $value;
            return $this;
        }

        throw new InvalidParameterValue(
            Parameters::WKST,
            $value,
            'Wkst value is not valid. Look for \Kronos\RRule\Enums\Days for valid value.'
        );
    }

    /**
     * A value as defined in Recurrence\\Kronos\RRule\Enums\Days
     * @return string Defined in Recurrence\\Kronos\RRule\Enums\Days
     */
    public function getWkst()
    {
        return $this->_wkst;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setBySecond(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < 0 || $value > 60) {
                throw new InvalidParameterValue(
                    Parameters::BYSECOND,
                    implode(',', $values),
                    'Bysecond value is not valid. It must be greater or equals to 0 and smaller or ' .
                    'equals to 60 (0 to 60).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }

        $this->_bysecond = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getBySecond()
    {
        return $this->_bysecond;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByMinute(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < 0 || $value > 59) {
                throw new InvalidParameterValue(
                    Parameters::BYMINUTE,
                    implode(',', $values),
                    'Byminute value is not valid. It must be greater or equals to 0 and smaller ' .
                    'or equals to 59 (0 to 59).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }

        $this->_byminute = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getByMinute()
    {
        return $this->_byminute;
    }

    /**
     * @param array $values
     * @return RRule
     * @throws InvalidParameterValue
     */
    public function setByHour(array $values)
    {
        foreach ($values as $value) {
            $value = (int)$value;
            if ($value < 0 || $value > 23) {
                throw new InvalidParameterValue(
                    Parameters::BYHOUR,
                    implode(',', $values),
                    'Byhour value is not valid. It must be greater or equals to 0 and smaller ' .
                    'or equals to 23 (0 to 23).' .
                    'The value that cause problem is *' . $value . '*'
                );
            }
        }

        $this->_byhour = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getByHour()
    {
        return $this->_byhour;
    }

    /**
     * Check if the count or until date is present. If both are not present, the rrule is endless.
     * @return bool
     */
    public function isEndless()
    {
        return !$this->getCount() && !$this->getUntil();
    }

    public function __construct()
    {
    }

    /**
     * Generate the raw_rrule for this object. If you're not sure if this rrule object is valid, call the
     * validate() function.
     * @param bool $include_property_name If true, add "RRULE:" in front of the rrule
     * i.e.: RRULE:{the_rrule_string}. DEFAULT true
     * @return string
     */
    public function generateRawRRule($include_property_name = true)
    {
        if ($include_property_name) {
            $raw_rrule = 'RRULE:';
        } else {
            $raw_rrule = '';
        }

        $raw_rrule .= Parameters::FREQ . '=' . $this->getFrequency();

        if ($this->getByDay()) {
            $raw_rrule .= ';' . Parameters::BYDAY . '=' . implode(',', $this->getByDay());
        }
        if ($this->getByHour()) {
            $raw_rrule .= ';' . Parameters::BYHOUR . '=' . implode(',', $this->getByHour());
        }
        if ($this->getByMinute()) {
            $raw_rrule .= ';' . Parameters::BYMINUTE . '=' . implode(',', $this->getByMinute());
        }
        if ($this->getByMonth()) {
            $raw_rrule .= ';' . Parameters::BYMONTH . '=' . implode(',', $this->getByMonth());
        }
        if ($this->getByMonthDay()) {
            $raw_rrule .= ';' . Parameters::BYMONTHDAY . '=' . implode(',', $this->getByMonthDay());
        }
        if ($this->getBySecond()) {
            $raw_rrule .= ';' . Parameters::BYSECOND . '=' . implode(',', $this->getBySecond());
        }
        if ($this->getBySetPos()) {
            $raw_rrule .= ';' . Parameters::BYSETPOS . '=' . implode(',', $this->getBySetPos());
        }
        if ($this->getByWeekNo()) {
            $raw_rrule .= ';' . Parameters::BYWEEKNO . '=' . implode(',', $this->getByWeekNo());
        }
        if ($this->getByYearDay()) {
            $raw_rrule .= ';' . Parameters::BYYEARDAY . '=' . implode(',', $this->getByYearDay());
        }
        if ($this->getCount()) {
            $raw_rrule .= ';' . Parameters::COUNT . '=' . $this->getCount();
        }
        if ($this->getInterval()) {
            $raw_rrule .= ';' . Parameters::INTERVAL . '=' . $this->getInterval();
        }
        if ($until = $this->getUntil()) {
            $raw_rrule .= ';' . Parameters::UNTIL . '=' . $until->format(self::RRULE_UNTIL_DATE_FORMAT);
        }
        if ($this->getWkst()) {
            $raw_rrule .= ';' . Parameters::WKST . '=' . $this->getWkst();
        }

        return $raw_rrule;
    }

    /**
     * Check the consistency between the different value i.e. FREQ rule part was not filled properly.
     * @return bool True if the current recurrence rule is valid otherwise \Kronos\RRule\Exceptions\InvalidRRuleState
     * exception is thrown.
     * @throws InvalidRRuleState
     */
    public function validate()
    {
        if (!$this->getFrequency()) {
            throw new InvalidRRuleState(
                Parameters::FREQ,
                'FREQ rule part MUST be specified in the recurrence rule.'
            );
        }
        if ($this->getByDay()) {
            if (
                $this->getFrequency() != Frequencies::MONTHLY &&
                $this->getFrequency() != Frequencies::YEARLY
            ) {
                foreach ($this->getByDay() as $day) {
                    if ((int)$day) {
                        throw new InvalidRRuleState(
                            Parameters::BYDAY,
                            'The BYDAY rule part MUST NOT be specified with a numeric value when the ' .
                            'FREQ rule part is not set to MONTHLY or YEARLY'
                        );
                    }
                }
            }
            if (
                $this->getByWeekNo() &&
                $this->getFrequency() == Frequencies::YEARLY
            ) {
                foreach ($this->getByDay() as $day) {
                    if ((int)$day) {
                        throw new InvalidRRuleState(
                            Parameters::BYDAY,
                            'BYDAY rule part MUST NOT be specified with a numeric value with the ' .
                            'FREQ rule part set to YEARLY when the BYWEEKNO rule part is specified.'
                        );
                    }
                }
            }
        }
        if (
            $this->getByMonthDay() &&
            $this->getFrequency() == Frequencies::WEEKLY
        ) {
            throw new InvalidRRuleState(
                Parameters::BYMONTHDAY,
                'BYMONTHDAY rule part MUST NOT be specified when the FREQ rule part is set to WEEKLY'
            );
        }
        if (
            $this->getByYearDay() &&
            ($this->getFrequency() == Frequencies::DAILY ||
                $this->getFrequency() == Frequencies::WEEKLY ||
                $this->getFrequency() == Frequencies::MONTHLY)
        ) {
            throw new InvalidRRuleState(
                Parameters::BYYEARDAY,
                'BYYEARDAY rule part MUST NOT be specified when the FREQ rule part is set to DAILY, WEEKLY, or MONTHLY.'
            );
        }

        if (
            $this->getByWeekNo() &&
            $this->getFrequency() != Frequencies::YEARLY
        ) {
            throw new InvalidRRuleState(
                Parameters::BYWEEKNO,
                'BYWEEKNO part MUST NOT be used when the FREQ rule part is set to anything other than YEARLY.'
            );
        }
        if (
            $this->getBySetPos() &&
            !$this->getByDay() &&
            !$this->getByHour() &&
            !$this->getByMinute() &&
            !$this->getByMonth() &&
            !$this->getByMonthDay() &&
            !$this->getBySecond() &&
            !$this->getByWeekNo() &&
            !$this->getByYearDay()
        ) {
            throw new InvalidRRuleState(
                Parameters::BYSETPOS,
                'BYSETPOS rule part MUST only be used in conjunction with another BYxxx rule part.'
            );
        }

        return true;
    }

    /**
     * Fully load a Recurrence object with the specified $raw_rrule.
     * @param string $raw_rrule
     * @return RRule
     * @throws InvalidParameterValue if some parameter of $raw_rrule are malformed.
     * @throws InvalidRRule if $raw_rrule is malformed.
     */
    public static function fromRawRRule($raw_rrule)
    {
        $modified_raw_rrule = strtoupper($raw_rrule);

        if (strpos($modified_raw_rrule, 'RRULE:') === 0) {
            $modified_raw_rrule = str_replace('RRULE:', '', $modified_raw_rrule);
        }

        $modified_raw_rrule = trim($modified_raw_rrule, ";");

        $parts = explode(";", $modified_raw_rrule);

        if ($parts === ['']) {
            throw new InvalidRRule(
                $raw_rrule,
                'No parameter were found. Parameter should be defined like NAME=RULE separated by semi-colon";"'
            );
        }
        $recurrence = new RRule();
        foreach ($parts as $rrule_param) {
            if (!strpos($rrule_param, '=')) {
                throw new InvalidRRule(
                    $raw_rrule,
                    'Parameter should be defined like NAME=RULE'
                );
            }

            list($rule, $param) = explode("=", $rrule_param);

            switch ($rule) {
                case Parameters::FREQ:
                    $recurrence->setFrequency($param);
                    break;
                case Parameters::UNTIL:
                    if (self::validateUntilDateString($param)) {
                        try {
                            $date_time = new DateTime(trim($param, 'Z'), new \DateTimeZone('UTC'));
                            $recurrence->setUntil($date_time);
                        } catch (\Exception $e) {
                            throw new InvalidParameterValue(
                                Parameters::UNTIL,
                                $param,
                                'Until parameter found but it seems to be an invalid or malformed date.',
                                0,
                                $e
                            );
                        }
                    } else {
                        throw new InvalidParameterValue(
                            Parameters::UNTIL,
                            $param,
                            'Until parameter found but it seems to be an invalid or malformed date.'
                        );
                    }
                    break;
                case Parameters::COUNT:
                    $recurrence->setCount((int)$param);
                    break;
                case Parameters::INTERVAL:
                    $recurrence->setInterval((int)$param);
                    break;
                case Parameters::BYSECOND:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYSECOND,
                            $param,
                            'Bysecond parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setBySecond($params);
                    break;
                case Parameters::BYMINUTE:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYMINUTE,
                            $param,
                            'Byminute parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByMinute($params);
                    break;
                case Parameters::BYHOUR:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYHOUR,
                            $param,
                            'Byhour parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByHour($params);
                    break;

                case Parameters::BYDAY:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYDAY,
                            $param,
                            'Byday parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }

                    $recurrence->setByDay($params);
                    break;
                case Parameters::BYMONTHDAY:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYMONTHDAY,
                            $param,
                            'Bymonthday parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByMonthDay($params);
                    break;
                case Parameters::BYYEARDAY:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYYEARDAY,
                            $param,
                            'Byyearday parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByYearDay($params);
                    break;
                case Parameters::BYWEEKNO:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYWEEKNO,
                            $param,
                            'Byweekno parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByWeekNo($params);
                    break;
                case Parameters::BYMONTH:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYWEEKNO,
                            $param,
                            'Bymonth parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setByMonth($params);
                    break;
                case Parameters::BYSETPOS:
                    $params = explode(",", $param);
                    if ($params === ['']) {
                        throw new InvalidParameterValue(
                            Parameters::BYSETPOS,
                            $param,
                            'Bymonth parameter found but it\'s values were either empty or malformed. ' .
                            'It must be comma separated values.'
                        );
                    }
                    $recurrence->setBySetPos($params);
                    break;
                case Parameters::WKST:
                    $recurrence->setWkst($param);
                    break;
            }
        }

        return $recurrence;
    }

    /**
     * This function actually format the given $date using the rrule until date format constant
     * (see RRULE::RRULE_UNTIL_DATE_FORMAT).
     * After formatting, it calls validateUntilDateString.
     * @param DateTime $date
     * @return bool true if valid otherwise false.
     * @throws \Exception only if the `preg_match` function failed.
     */
    private static function validateUntilDate(DateTime $date)
    {
        return self::validateUntilDateString($date->format(self::RRULE_UNTIL_DATE_FORMAT));
    }

    /**
     * Make sure that the given $date is a rrule compliant string date.
     * @param string $date
     * @return bool
     * @throws \Exception only if the `preg_match` function failed.
     */
    private static function validateUntilDateString($date)
    {
        $match = preg_match('/^[0-9]{8}T[0-9]{6}Z$/', $date);
        if ($match === 0) {
            return false;
        }

        if ($match === false) {
            throw new \Exception(
                'preg_match failed while trying this call: `preg_match(\'/^[0-9]{8}T[0-9]{6}Z$/\', "' . $date . '");`'
            );
        }
        return true;
    }
}
