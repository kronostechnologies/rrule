<?php

namespace Kronos\ExDate\Exceptions;

/**
 * Thrown when the rrule is malformed
 */
class InvalidExDate extends \Exception{

	protected $_invalid_exdate;
    /**
     * @var string|null
     */
	protected $_explanation;

	/**
	 * Returns the invalid raw Exdate
	 * @return string Raw Exdate
	 */
	public function getExdate(){
		return $this->_invalid_exdate;
	}
	/**
	 * @param string $value
	 */
	protected function setExdate($value){
		$this->_invalid_exdate = $value;
	}

	/**
	 * Returns the explanation of why the given exdate is invalid
	 * @return string|null
	 */
	public function getExplanation(){
		return $this->_explanation;
	}
	/**
	 * @param string|null $value
	 */
	protected function setExplanation($value){
		$this->_explanation = $value;
	}

	/**
	 * Thrown when an invalid ExDate is detected.
	 * @param string $invalid_exdate The raw RRule string.
	 * @param string|null $explanation The explanation of why the given rrule is invalid
	 * @param int $code
	 * @param \Exception|null $previous
	 */
	public function __construct($invalid_exdate, $explanation = null, $code = 0, $previous = null){
		parent::__construct('Explanation:"'.$explanation.'", RawRRule:"'.$invalid_exdate.'"', $code, $previous);

		$this->setExdate($invalid_exdate);
		$this->setExplanation($explanation);
	}


}
