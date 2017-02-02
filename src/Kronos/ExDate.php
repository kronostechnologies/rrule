<?php

namespace Kronos;

class ExDate{
	/**
	 * @var \DateTime[] object
	 */
	protected $_exception_dates = array();
	/**
	 * @var \DateTime[] object
	 */
	protected $_exception_dates_in_current_timezone = array();
	
	/**
	 * The DateTime object returned are in UTC.
	 * @return \DateTime[] object
	 */
	public function getExceptionDates(){
		return $this->_exception_dates;
	}
	
	/**
	 * The DateTime object returned are in default timezone (see date_default_timezone_get() function).
	 * @return \DateTime[] object
	 */
	public function getExceptionDatesInCurrentTimezone(){
		return $this->_exception_dates_in_current_timezone;
	}
	
	/**
	 * @param \DateTime $value
	 * @return \Kronos\ExDate
	 */
	public function addExceptionDate(\DateTime $value){
		$dt = clone $value;
		$dt_in_current_timezone = clone $value;
		
		$dt->setTimezone(new \DateTimeZone('UTC'));
		$this->_exception_dates[] = $dt;
		
		$dt_in_current_timezone->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		$this->_exception_dates_in_current_timezone[] = $dt_in_current_timezone;
		return $this;
	}
	/**
	 * @param array $values Array of \DateTime object
	 * @return \Kronos\ExDate
	 */
	public function setExceptionDates(array $values){
		$this->_exception_dates = [];
		$this->_exception_dates_in_current_timezone = [];
		foreach($values as $value) {
			$this->addExceptionDate($value);
		}
		return $this;
	}
	
	public function __construct(){
		
	}

	/**
	 * @throws \Kronos\ExDate\Exceptions\InvalidExDate
	 */
	public function validate(){
		if(count($this->getExceptionDates()) == 0){
			throw new \Kronos\ExDate\Exceptions\InvalidExDate('*NO EXDATE*', 'setExceptionDates property need to be filled properly');
		}
	}
	
	/**
	 * @return string 
	 */
	public function generateRawExDate(){
		$raw_exdate = 'EXDATE:';
		
		foreach($this->getExceptionDates() as $date){
			$raw_exdate .= $date->format('Ymd\THis\Z') . ',';
		}
		$raw_exdate = trim($raw_exdate, ',');
		
		return $raw_exdate;
	}
	
	/**
	 * @param string $raw_exdate
	 * @throws \Kronos\ExDate\Exceptions\InvalidExDate
	 * @return \Kronos\ExDate
	 */
	public static function fromRawExDate($raw_exdate){
		$modified_raw_exdate = strtoupper($raw_exdate);
		
		if(strpos($modified_raw_exdate, 'EXDATE:') !== 0){
			throw new \Kronos\ExDate\Exceptions\InvalidExDate($raw_exdate, 'Missing the starting \'EXDATE:\'');
		}
		
		$modified_raw_exdate = str_replace('EXDATE:', '', $modified_raw_exdate);
		$modified_raw_exdate = trim($modified_raw_exdate, ',');

		$parts = explode(",", $modified_raw_exdate);
		
		if(empty($parts) || empty($parts[0])){
			throw new \Kronos\ExDate\Exceptions\InvalidExDate($raw_exdate, 'No dates were defined. EXDATE: was properly found but it contained no values');
		}
		
		$exdate = new ExDate();
		foreach($parts as $date){
			try {
				$datetime = new \DateTime(trim($date, 'Z'), new \DateTimeZone('UTC'));
			}
			catch(\Exception $e) {
				throw new \Kronos\ExDate\Exceptions\InvalidExDate($raw_exdate, 'The Date "'.$date.'" in the EXDATE does not seem to be a valid datetime', $e->getCode(), $e);
			}

			$exdate->addExceptionDate($datetime);
		}
		
		return $exdate;
	}
}
