<?php

namespace Kronos\RRule\Exceptions;

/**
 * Thrown when the rrule is malformed
 */
class InvalidRRule extends \Exception{

	/**
	 * @var string
	 */
	protected $_invalid_rrule;
	/**
	 * @var string|null
	 */
	protected $_explanation;

	/**
	 * Returns the invalid raw RRule
	 * @return string Raw RRule
	 */
	public function getRRule(){
		return $this->_invalid_rrule;
	}
	/**
	 * @param string $value
	 */
	protected function setRRule($value){
		$this->_invalid_rrule = $value;
	}

	/**
	 * Returns the explanation of why the given rrule is invalid
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
	 * Thrown when an invalid RRule is detected.
	 * @param string $invalid_rrule The raw RRule string.
	 * @param string $explanation The explanation of why the given rrule is invalid
	 * @param int $code
	 * @param \Exception $previous
	 */
	public function __construct($invalid_rrule, $explanation = null, $code = 0, $previous = null){
		parent::__construct('Explanation:"'.$explanation.'", RawRRule:"'.$invalid_rrule.'"', $code, $previous);

		$this->setRRule($invalid_rrule);
		$this->setExplanation($explanation);
	}


}
