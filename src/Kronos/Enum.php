<?php

namespace Kronos;

/**
 * Represent a base class enumeration. Any new Enum should extends from this class.
 * Enum value must be declared as constants like  "const name = 'value';".
 *
 * This class was improved based on Brian Cline answer in http://stackoverflow.com/questions/254514/php-and-enumerations
 */
abstract class  Enum {

	private static $cache = NULL;

	private static function initialiseCache() {
		if(self::$cache === NULL) {
			self::$cache = array();
		}
	}

	private static function getConstants() {
		self::initialiseCache();

		$classname = get_called_class();
		if(!array_key_exists($classname, self::$cache)) {
			$r = new \ReflectionClass($classname);
			self::$cache[$classname] = $r->getConstants();
		}

		return self::$cache[$classname];
	}

	final static public function isValidName($name) {
		return array_key_exists($name, self::getConstants());
	}

	final static public function isValidValue($value) {
		return in_array($value, self::getConstants());
	}

	/**
	 * Check whether or not the value is defined in the array of constant for a given enum classname.
	 * @param mixed $value An enum value to verify
	 * @return boolean True if the value is defined otherwise false.
	 */
	final static public function inEnum($value){
		if(is_array($value)){
			return count(array_intersect($value, self::getConstants())) ==  count($value);
		}
		else{
			return array_search($value, self::getConstants()) !== false;
		}
	}

	/**
	 * Convert this Enum to a key(const name)=>value(const value) array.
	 * @return array A key=>value array.
	 */
	final static public function toArray(){
		$r = new \ReflectionClass(get_called_class());
		return $r->getConstants();
	}
}