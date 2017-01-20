<?php

namespace Kronos\RRule\Exceptions;

/**
 * Thrown when the a parameter's value is malformed.
 */
class InvalidParameterValue extends \Exception{
	
	/**
	 * @var string 
	 */
	protected $_parameter_name;
	/**
	 * @var string 
	 */
	protected $_parameter_value;
	/**
	 * @var string 
	 */
	protected $_explanation;
	
	/**
	 * @return string
	 */
	public function getParameterName(){
		return $this->_parameter_name;
	}
	protected function setParameterName($value){
		$this->_parameter_name = $value;
	}
	/**
	 * @return string
	 */
	public function getParameterValue(){
		return $this->_parameter_value;
	}
	protected function setParameterValue($value){
		$this->_parameter_value = $value;
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
	 * Thrown when an invalid frequency (FREQ parameter) is detected.
	 * @param string $parameter_name The parameter name.
	 * @param string $parameter_value The value of the parameter.
	 * @param string $explanation The explanation of why the given parameter is invalid
	 * @param string $code 
	 * @param \Exception $previous 
	 */
	public function __construct($parameter_name, $parameter_value, $explanation = null, $code = null, $previous = null){
		parent::__construct('Explanation:"'.$explanation.'", Parameter:"'.$parameter_name.'", Value:"'.$parameter_value.'"', $code, $previous);
		
		$this->setParameterName($parameter_name);
		$this->setParameterValue($parameter_value);
		$this->setExplanation($explanation);
	}
}