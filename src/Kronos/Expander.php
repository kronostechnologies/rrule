<?php

namespace Kronos\RRule;

/**
 * Calculate RRULE valid dates
 * Based on https://github.com/tplaner/When develop branch
 */
class Expander extends \DateTime {
	
	public static $frequencies = [
		'secondly',
		'minutely',
		'hourly',
		'daily',
		'weekly',
		'monthly',
		'yearly'
  ];

	public static  $weekDays = array('su', 'mo', 'tu', 'we', 'th', 'fr', 'sa');

	public $startDate;
	public $freq;
	public $until;
	public $count;
	public $interval;

	public $byseconds;
	public $byminutes;
	public $byhours;
	public $bydays;
	public $bymonthdays;
	public $byyeardays;
	public $byweeknos;
	public $bymonths;
	public $bysetpos;
	public $wkst;

	public $occurences = array();

	public function __construct($time = "now", $timezone = NULL) {
		$this->startDate = parent::__construct($time, $timezone);
	}

	public function startDate($startDate) {
		if (self::validDateTimeObject($startDate)) {
			$this->startDate = clone $startDate;

			return $this;
		}
		else {
			throw new Exceptions\InvalidArgument("startDate: Accepts valid DateTime objects");
		}
	}

	public function freq($frequency) {
		if (self::validFreq($frequency)) {
			$this->freq = strtolower($frequency);

			return $this;
		}

		throw new Exceptions\InvalidArgument("freq: Accepts " . rtrim(implode(self::$frequencies, ", "), ","));
	}

	public function until($endDate) {
		if (self::validDateTimeObject($endDate)) {
			$this->until = clone $endDate;
			return $this;
		}

		throw new Exceptions\InvalidArgument("until: Accepts valid DateTime objects");
	}

	public function count($count) {
		if (is_numeric($count)) {
			$this->count = (int)$count;

			return $this;
		}

		throw new Exceptions\InvalidArgument("count: Accepts numeric values");
	}

	public function interval($interval) {
		if (is_numeric($interval)) {
			$this->interval = (int)$interval;

			return $this;
		}

		throw new Exceptions\InvalidArgument("interval: Accepts numeric values");
	}

	public function bysecond($seconds, $delimiter = ",") {
		if ($this->byseconds = self::prepareItemsList($seconds, $delimiter, 'second')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("bysecond: Accepts numeric values between 0 and 60");
	}

	public function byminute($minutes, $delimiter = ",") {
		if ($this->byminutes = self::prepareItemsList($minutes, $delimiter, 'minute')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("byminute: Accepts numeric values between 0 and 59");
	}

	public function byhour($hours, $delimiter = ",") {
		if ($this->byhours = self::prepareItemsList($hours, $delimiter, 'hour')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("byhour: Accepts numeric values between 0 and 23");
	}

	public function byday($bywdaylist, $delimiter = ",") {
		if (is_string($bywdaylist) && strpos($bywdaylist, $delimiter) !== false) {
			// remove any accidental delimiters
			$bywdaylist = trim($bywdaylist, $delimiter);

			$bywdaylist = explode($delimiter, $bywdaylist);
		}
		else if(is_string($bywdaylist)) {
			// remove any accidental delimiters
			$bywdaylist = trim($bywdaylist, $delimiter);

			$bywdaylist = array($bywdaylist);
		}

		if (is_array($bywdaylist) && self::validDaysList($bywdaylist)) {
			$this->bydays = self::createDaysList($bywdaylist);

			return $this;
		}

		throw new Exceptions\InvalidArgument("bydays: Accepts (optional) positive and negative values between 1 and 53 followed by a valid week day");
	}

	public function bymonthday($bymodaylist, $delimiter = ",") {
		if($this->bymonthdays = self::prepareItemsList($bymodaylist, $delimiter, 'monthDayNum')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("bymonthday: Accepts positive and negative values between 1 and 31");
	}

	public function byyearday($byyrdaylist, $delimiter = ",") {
		if($this->byyeardays = self::prepareItemsList($byyrdaylist, $delimiter, 'yearDayNum')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("byyearday: Accepts positive and negative values between 1 and 366");
	}

	public function byweekno($bywknolist, $delimiter = ",") {
		if($this->byweeknos = self::prepareItemsList($bywknolist, $delimiter, 'weekNum')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("byweekno: Accepts positive and negative values between 1 and 53");
	}

	public function bymonth($bymolist, $delimiter = ",") {
		if($this->bymonths = self::prepareItemsList($bymolist, $delimiter, 'monthNum')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("bymonth: Accepts values between 1 and 12");
	}

	public function bysetpos($bysplist, $delimiter = ",") {
		if ($this->bysetpos = self::prepareItemsList($bysplist, $delimiter, 'setPosDay')) {
			return $this;
		}

		throw new Exceptions\InvalidArgument("bysetpos: Accepts positive and negative values between 1 and 366");
	}

	public function wkst($weekDay) {
		if (self::validWeekDay($weekDay)) {
			$this->wkst = strtolower($weekDay);

			return $this;
		}
		else {
			throw new Exceptions\InvalidArgument("wkst: Accepts " . rtrim(implode(self::$weekDays, ", "), ","));
		}
	}

	public function rrule($rrule) {
		// strip off a trailing semi-colon
		$rrule = trim($rrule, ";");

		$parts = explode(";", $rrule);

		foreach($parts as $part) {
			list($rule, $param) = explode("=", $part);

			$rule = strtoupper($rule);
			$param = strtoupper($param);

			switch($rule) {
				case "UNTIL":
					$this->until(new \DateTime($param));
					break;
				case "FREQ":
				case "COUNT":
				case "INTERVAL":
				case "WKST":
					$this->{$rule}($param);
					break;
				case "BYDAY":
				case "BYMONTHDAY":
				case "BYYEARDAY":
				case "BYWEEKNO":
				case "BYMONTH":
				case "BYSETPOS":
				case "BYHOUR":
				case "BYMINUTE":
				case "BYSECOND":
					$params = explode(",", $param);
					$this->{$rule}($params);
					break;
			}
		}

		return $this;
	}

	public function occursOn($date) {
		if (!self::validDateTimeObject($date)) {
			throw new Exceptions\InvalidArgument("occursOn: Accepts valid DateTime objects");
		}

		// breakdown the date
		$year = $date->format('Y');
		$month = $date->format('n');
		$day = $date->format('j');
		$dayFromEndOfMonth = -((int)$date->format('t') + 1 - (int)$day);

		$leapYear = (int)$date->format('L');

		$yearDay = $date->format('z') + 1;
		$yearDayNeg = -366 + (int)$yearDay;
		if ($leapYear) {
			$yearDayNeg = -367 + (int)$yearDay;
		}

		// this is the nth occurence of the date
		$occur = ceil($day / 7);
		$occurNeg = -1 * ceil(abs($dayFromEndOfMonth) / 7);

		// starting on a monday
		$week = $date->format('W');
		$weekDay = strtolower($date->format('D'));

		$dayOfWeek = $date->format('l');
		$dayOfWeekAbr = strtolower(substr($dayOfWeek, 0, 2));

		// the date has to be greater then the start date
		if ($date < $this->startDate) {
			return false;
		}

		// if the there is an end date, make sure date is under
		if (isset($this->until)) {
			if ($date > $this->until) {
				return false;
			}
		}

		if (isset($this->bymonths)) {
			if (!in_array($month, $this->bymonths)) {
				return false;
			}
		}

		if (isset($this->bydays)) {
			if (!in_array(0 . $dayOfWeekAbr, $this->bydays) &&
				!in_array($occur . $dayOfWeekAbr, $this->bydays) &&
				!in_array($occurNeg . $dayOfWeekAbr, $this->bydays)) {
				return false;
			}
		}

		if (isset($this->byweeknos)) {
			if (!in_array($week, $this->byweeknos)) {
				return false;
			}
		}

		if (isset($this->bymonthdays)) {
			if (!in_array($day, $this->bymonthdays) &&
				!in_array($dayFromEndOfMonth, $this->bymonthdays)) {
				return false;
			}
		}

		if (isset($this->byyeardays)) {
			if (!in_array($yearDay, $this->byyeardays) &&
				!in_array($yearDayNeg, $this->byyeardays)) {
				return false;
			}
		}

		return true;
	}

	public function occursAt($date) {
		$hour = (int)$date->format('G');
		$minute = (int)$date->format('i');
		$second = (int)$date->format('s');

		if (isset($this->byhours)) {
			if (!in_array($hour, $this->byhours)) {
				return false;
			}
		}

		if (isset($this->byminutes)) {
			if (!in_array($minute, $this->byminutes)) {
				return false;
			}
		}

		if (isset($this->byseconds)) {
			if (!in_array($second, $this->byseconds)) {
				return false;
			}
		}
	}

	public function generateOccurences() {
		self::prepareDateElements();

		$count = 0;

		$dateLooper = clone $this->startDate;

		// add the start date to the list of occurences
		if ($this->occursOn($dateLooper)) {
			$this->addOccurence($this->generateTimeOccurences($dateLooper));
		}
		else {
			$this->count--; // This is because Microsoft and Google do not respect restriction A.1.1 of the RFC 5545
		}

		while ((!$this->until || ($this->until && $dateLooper < $this->until)) && (!$this->count || ($this->count && count($this->occurences) < $this->count))) {
			if ($this->freq === "yearly") {
				if (isset($this->bymonths)) {
					foreach ($this->bymonths as $month) {
						if (isset($this->bydays)) {
							$dateLooper->setDate($dateLooper->format("Y"), $month, 1);

							// get the number of days
							$totalDays = $dateLooper->format("t");
							$today = 0;

							$occurences = array();
							while ($today < $totalDays) {
								if ($this->occursOn($dateLooper)) {
									$occurences = array_merge($occurences, $this->generateTimeOccurences($dateLooper));
								}

								$dateLooper->add(new \DateInterval('P1D'));
								$today++;
							}

							// if bysetpos is set we need to limit the
							// number of occurences to only those which
							// meet the setpos
							if (isset($this->bysetpos)) {
								if ($count > 0) {
									$occurenceCount = count($occurences);

									foreach ($this->bysetpos as $setpos) {
										if ($setpos > 0) {
											$this->occurences[] = $occurences[$setpos - 1];
										}
										else {
											$this->occurences[] = $occurences[$occurenceCount + $setpos];
										}
									}
								}
							}
							else {
								$this->addOccurence($occurences);
							}
						}
						else {
							$dateLooper->setDate($dateLooper->format("Y"), $month, $dateLooper->format("j"));

							if ($this->occursOn($dateLooper)) {
								$this->addOccurence($this->generateTimeOccurences($dateLooper));

							}
						}
					}
				}
				else {
					$dateLooper->setDate($dateLooper->format("Y"), 1, 1);

					$leapYear = (int)$dateLooper->format("L");
					if ($leapYear) {
						$days = 366;
					}
					else {
						$days = 365;
					}

					$day = 0;
					while ($day < $days) {
						if ($this->occursOn($dateLooper)) {
							$this->addOccurence($this->generateTimeOccurences($dateLooper));

						}
						$dateLooper->add(new \DateInterval('P1D'));
						$day++;
					}
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->add(new \DateInterval('P' . ($this->interval * ++$count) . 'Y'));
			}
			else if ($this->freq === "monthly") {
				$days = (int)$dateLooper->format("t");

				$day = (int)$dateLooper->format("j");

				$occurences = array();
				while ($day <= $days) {
					if ($this->occursOn($dateLooper)) {
						$occurences = array_merge($occurences, $this->generateTimeOccurences($dateLooper));
					}

					$dateLooper->add(new \DateInterval('P1D'));
					$day++;
				}

				// if bysetpos is set we need to limit the
				// number of occurences to only those which
				// meet the setpos
				if (isset($this->bysetpos)) {
					if ($count > 0) {
						$occurenceCount = count($occurences);

						foreach ($this->bysetpos as $setpos) {
							if ($setpos > 0) {
								$this->occurences[] = $occurences[$setpos - 1];
							}
							else {
								$this->occurences[] = $occurences[$occurenceCount + $setpos];
							}
						}
					}
				}
				else {
					$this->addOccurence($occurences);
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->setDate($dateLooper->format("Y"), $dateLooper->format("n"), 1);
				$dateLooper->add(new \DateInterval('P' . ($this->interval * ++$count) . 'M'));
			}
			else if ($this->freq === "weekly") {
				$dateLooper->setDate($dateLooper->format("Y"), $dateLooper->format("n"), $dateLooper->format("j"));

				switch ($this->wkst) {
					case "su":
						$wkst = "Sunday";
						break;
					case "mo":
						$wkst = "Monday";
						break;
					case "tu":
						$wkst = "Tuesday";
						break;
					case "we":
						$wkst = "Wednesday";
						break;
					case "th":
						$wkst = "Thursday";
						break;
					case "fr":
						$wkst = "Friday";
						break;
					case "sa":
						$wkst = "Saturday";
						break;
				}

				$daysLeft = 7;

				// not very happy with this
				if ($count === 0) {
					$startWeekDay = clone $this->startDate;
					$startWeekDay->modify("last " . $wkst);
					$startWeekDay->modify("+7 days");

					$daysLeft = intval($startWeekDay->format('j')) - intval($dateLooper->format("j"));

					$startWeekDay->modify("-7 days");
				}

				while ($daysLeft > 0) {
					if ($this->occursOn($dateLooper)) {
						$this->addOccurence($this->generateTimeOccurences($dateLooper));
					}
					$dateLooper->add(new \DateInterval('P1D'));
					$daysLeft--;
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->setDate($startWeekDay->format("Y"), $startWeekDay->format("n"), $startWeekDay->format('j'));
				$dateLooper->add(new \DateInterval('P' . ($this->interval * (++$count * 7)) . 'D'));
			}
			else if ($this->freq === "daily") {
				if ($this->occursOn($dateLooper)) {
					$this->addOccurence($this->generateTimeOccurences($dateLooper));
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->setDate($dateLooper->format("Y"), $dateLooper->format("n"), $dateLooper->format('j'));
				$dateLooper->add(new \DateInterval('P' . ($this->interval * ++$count) . 'D'));
			}
			else if ($this->freq === "hourly") {
				$occurence = array();
				if ($this->occursOn($dateLooper)) {
					$occurence[] = $dateLooper;
					$this->addOccurence($occurence);
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->add(new \DateInterval('PT' . ($this->interval * ++$count) . 'H'));
			}
			else if ($this->freq === "minutely") {
				$occurence = array();
				if ($this->occursOn($dateLooper)) {
					$occurence[] = $dateLooper;
					$this->addOccurence($occurence);
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->add(new \DateInterval('PT' . ($this->interval * ++$count) . 'M'));
			}
			else if ($this->freq === "secondly") {
				$occurence = array();
				if ($this->occursOn($dateLooper)) {
					$occurence[] = $dateLooper;
					$this->addOccurence($occurence);
				}

				$dateLooper = clone $this->startDate;
				$dateLooper->add(new \DateInterval('PT' . ($this->interval * ++$count) . 'S'));

			}
		}
	}

	protected function addOccurence($occurences) {
		foreach ($occurences as $occurence) {
			// make sure that this occurence isn't already in the list
			if (!in_array($occurence, $this->occurences)) {
				$this->occurences[] = $occurence;
			}
		}
	}

	// not happy with this.
	protected function generateTimeOccurences($dateLooper) {
		$occurences = array();

		foreach ($this->byhours as $hour) {
			foreach ($this->byminutes as $minute) {
				foreach ($this->byseconds as $second) {
					if (!$this->count || ($this->count && count($this->occurences) < $this->count)) {
						$occurence = clone $dateLooper;
						$occurence->setTime($hour, $minute, $second);
						$occurences[] = $occurence;
					}
					else {
						break 3;
					}
				}
			}
		}

		return $occurences;
	}

	protected function prepareDateElements() {
		// if the interval isn't set, set it.
		if (!isset($this->interval)) {
			$this->interval = 1;
		}

		// must have a frequency
		if (!isset($this->freq) && self::validByFreqValid($this->freq, $this->byweeknos, $this->byyeardays, $this->bymonthdays)) {
			throw new Exceptions\FrequencyRequired();
		}

		if (!isset($this->count) && !isset($this->until)) {
			$this->count = 200;
		}

		// "Similarly, if the BYMINUTE, BYHOUR, BYDAY,
		// BYMONTHDAY, or BYMONTH rule part were missing, the appropriate
		// minute, hour, day, or month would have been retrieved from the
		// "DTSTART" property."

		// if there is no startDate, make it now
		if (!$this->startDate) {
			$this->startDate = new \DateTime();
		}

		// the calendar repeats itself every 400 years, so if a date
		// doesn't exist for 400 years, I don't think it will ever
		// occur
		if (!isset($this->until)) {
			$this->until = new \DateTime();
			$this->until->add(new \DateInterval('P400Y'));
		}

		if (!isset($this->byminutes)) {
			$this->byminutes = array((int)$this->startDate->format('i'));
		}

		if (!isset($this->byhours)) {
			$this->byhours = array((int)$this->startDate->format('G'));
		}

		if (!isset($this->byseconds)) {
			$this->byseconds = array((int)$this->startDate->format('s'));
		}

		if (!isset($this->wkst)) {
			$this->wkst = "mo";
		}

		/*if (!isset($this->bydays)) {
			$dayOfWeek = $this->startDate->format('l');
			$dayOfWeekAbr = strtolower(substr($dayOfWeek, 0, 2));
			$this->bydays = array($dayOfWeekAbr);
		}*/

		if ($this->freq === "monthly") {
			if (!isset($this->bymonthdays) && !isset($this->bydays)) {
				$this->bymonthdays = array((int)$this->startDate->format('j'));
			}
		}

		if ($this->freq === "weekly") {
			if (!isset($this->bymonthdays) && !isset($this->bydays)) {
				$dayOfWeek = $this->startDate->format('l');
				$dayOfWeekAbr = strtolower(substr($dayOfWeek, 0, 2));
				$this->bydays = array("0" . $dayOfWeekAbr);
			}
		}
	}

	protected static function createItemsList($list, $delimiter) {
		$items = explode($delimiter, $list);

		return array_map('intval', $items);
	}

	protected static function prepareItemsList($items, $delimiter = ",", $validator=null) {
		$_items = false;

		if (is_numeric($items)) {
			$_items = array(intval($items));
		}

		if (is_string($items) && $_items === false) {
			// remove any accidental delimiters
			$items = trim($items, $delimiter);

			$_items = self::createItemsList($items, $delimiter);
		}

		if (is_array($items)) {
			$_items = $items;
		}

		if (is_array($_items) && self::validItemsList($_items, $validator)) {
			return $_items;
		}
		else {
			return false;
		}
	}

	protected static function createDaysList($days) {
		$_days = array();

		foreach($days as $day) {
			$day = ltrim($day, "+");
			$day = trim($day);

			$ordwk = 0;
			$weekday = false;

			if (strlen($day) === 2) {
				$weekday = $day;
			}
			else {
				list($ordwk, $weekday) = sscanf($day, "%d%s");
			}

			$_days[] = $ordwk . strtolower($weekday);
		}

		return $_days;
	}

	/**
	 * Test if array of days is valid
	 *
	 * @param  array	$days
	 * @return bool
	 */
	public static function validDaysList($days) {
		foreach($days as $day) {
			// if it isn't negative, it's positive
			$day = ltrim($day, "+");
			$day = trim($day);

			$ordwk = 1;
			$weekday = false;

			if (strlen($day) === 2) {
				$weekday = $day;
			}
			else {
				list($ordwk, $weekday) = sscanf($day, "%d%s");
			}

			if (!self::validWeekDay($weekday) || !self::validOrdWk(abs($ordwk))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Test for valid itemsList
	 *
	 * @param  array	$items
	 * @param  string   $validator  Validator to use agains the list (second, minute, hour)
	 * @return bool
	 */
	public static function validItemsList($items, $validator) {
		foreach ($items as $item) {
			$valid = 'valid'.ucfirst($validator);
			if (!self::$valid($item)) {
				return false;
			}
		}

		return true;
	}

	public static function validByFreqValid($freq, $byweeknos, $byyeardays, $bymonthdays) {
		if (isset($byweeknos) && $freq !== "yearly") {
			throw new Exceptions\InvalidCombination();
		}

		if (isset($byyeardays) && !in_array($freq, array("daily", "weekly", "monthly"))) {
			throw new Exceptions\InvalidCombination();
		}

		if (isset($bymonthdays) && $freq === "weekly") {
			throw new Exceptions\InvalidCombination();
		}

		return true;
	}

	public static function validYearDayNum($day) {
		return self::validOrdYrDay(abs($day));
	}

	public static function validOrdYrDay($ordyrday) {
		return ($ordyrday >= 1 && $ordyrday <= 366);
	}

	public static function validMonthDayNum($day) {
		return self::validOrdMoDay(abs($day));
	}

	public static function validMonthNum($month) {
		return ($month >= 1 && $month <= 12);
	}

	public static function validSetPosDay($day) {
		return self::validYearDayNum($day);
	}

	/**
	 * Tests for valid ordMoDay
	 *
	 * @param  integer $ordmoday
	 * @return bool
	 */
	public static function validOrdMoDay($ordmoday) {
		return ($ordmoday >= 1 && $ordmoday <= 31);
	}

	/**
	 * Test for a valid weekNum
	 *
	 * @param  integer $week
	 * @return bool
	 */
	public static function validWeekNum($week) {
		return self::validOrdWk(abs($week));
	}

	/**
	 * Test for valid ordWk
	 *
	 * TODO: ensure this doesn't suffer from Y2K bug since there can be 54 weeks in a year
	 *
	 * @param  integer $ordwk
	 * @return bool
	 */
	public static function validOrdWk($ordwk) {
		return ($ordwk >= 1 && $ordwk <= 53);
	}

	/**
	 * Test for valid hour
	 *
	 * @param  integer $hour
	 * @return bool
	 */
	public static function validHour($hour) {
		return ($hour >= 0 && $hour <= 23);
	}

	/**
	 * Test for valid minute
	 *
	 * @param  integer $minute
	 * @return bool
	 */
	public static function validMinute($minute) {
		return ($minute >= 0 && $minute <= 59);
	}

	/**
	 * Test for valid second
	 *
	 * @param  integer $second
	 * @return bool
	 */
	public static function validSecond($second) {
		return ($second >= 0 && $second <= 60);
	}

	/**
	 * Test for valid weekDay
	 *
	 * @param  string $weekDay
	 * @return bool
	 */
	public static function validWeekDay($weekDay) {
		return in_array(strtolower($weekDay), self::$weekDays);
	}

	/**
	 * Test for valid frequency
	 *
	 * @param  string $frequency
	 * @return bool
	 */
	public static function validFreq($frequency) {
		return in_array(strtolower($frequency), self::$frequencies);
	}

	/**
	 * Test for valid DateTime object
	 *
	 * @param  DateTime $dateTime
	 * @return bool
	 */
	public static function validDateTimeObject($dateTime) {
		return (is_object($dateTime) && $dateTime instanceof \DateTime);
	}
}