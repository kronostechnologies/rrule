<?php

namespace Kronos\RRule\Exceptions;

/**
 * Thrown when the rrule is malformed
 */
class InvalidRRule extends \Exception{
	
	protected $_invalid_rrule;
	protected $_explanation;
	
	/**
	 * Returns the invalid raw RRule
	 * @return string Raw RRule
	 */
	public function getRRule(){
		return $this->_invalid_rrule;
	}
	protected function setRRule($value){
		$this->_invalid_rrule = $value;
	}
	
	/**
	 * Returns the explanation of why the given rrule is invalid
	 * @return string
	 */
	public function getExplanation(){
		return $this->_explanation;
	}
	protected function setExplanation($value){
		$this->_explanation = $value;
	}
	
	/**
	 * Thrown when an invalid RRule is detected.
	 * @param string $invalid_rrule The raw RRule string.
	 * @param string $explanation The explanation of why the given rrule is invalid
	 * @param string $code 
	 * @param \Exception $previous 
	 */
	public function __construct($invalid_rrule, $explanation = null, $code = null, $previous = null){
		parent::__construct('Explanation:"'.$explanation.'", RawRRule:"'.$invalid_rrule.'"', $code, $previous);
		
		$this->setRRule($invalid_rrule);
		$this->setExplanation($explanation);
	}
	
	
}
