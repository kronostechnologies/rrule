<?php

namespace Kronos\RRule\Exceptions;

/**
 * Thrown when the general the rrule is synthax is valid but incoherent.
 */
class InvalidRRuleState extends \Exception{
	/**
	 * @var \Kronos\RRule
	 */
	protected $_rrule_instance;
	/**
	 * @var string
	 */
	protected $_parameter_name;
	
	protected function setRRuleInstance(\Kronos\RRule $value){
		$this->_rrule_instance = $value;
	}
	/**
	 * @return \Kronos\RRule The loaded RRule object with incoherent state.
	 */
	public function getRRuleInstance(){
		return $this->_rrule_instance;
	}
	
	protected function setParameterName($value){
		$this->_parameter_name = $value;
	}
	/**
	 * Returns the parameter name that should be fixed.
	 * @return string
	 */
	public function getParameterName(){
		return $this->_parameter_name;
	}
	
	public function __construct($parameter_name, 
	                            $explanation = '', 
	                            $code = null,
	                            $previous = null
	){
		parent::__construct('Explanation:"'.$explanation.'", Parameter:"'.$parameter_name.'"', $code, $previous);
		$this->setParameterName($parameter_name);
	}
}