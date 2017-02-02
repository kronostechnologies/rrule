<?php

namespace Kronos\Tests;

/*
 * This is the parsing test for the RRule parser.
 */
class RawRRuleParserTest extends \PHPUnit_Framework_TestCase {
	public function setUp(){
		parent::setUp();
	}
	
	public function testConstructorAssignCorrectDefaultValue(){
		$rrule = new \Kronos\RRule();
		$this->assertEquals(null, $rrule->getInterval());
	}
	
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidRRule
	 */
	public function testMissingRRuleFirstTagThrowsCorrectException(){
		\Kronos\RRule::fromRawRRule('RRuleis:ofwrongsetup!');
	}
	/**
	 * @depends testMissingRRuleFirstTagThrowsCorrectException
	 */
	public function testInvalidRRuleExceptionContainsCorrectData(){
		$bad_raw_rrule = 'RRule:';
		try{
			\Kronos\RRule::fromRawRRule($bad_raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidRRule $e){
			$this->assertEquals($bad_raw_rrule, $e->getRRule());
		}
	}
	
	public function testFreqGetsProperlyFilled(){
		$parameter_value = \Kronos\RRule\Enums\Frequencies::DAILY;
		$raw_rrule = 'RRULE:FREQ='.$parameter_value;
		
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals($parameter_value, $rrule->getFrequency());
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrowWhenFreqRuleIsMalformed(){
		$parameter_value = 'WRONG_VALUE';
		$raw_rrule = 'RRULE:FREQ='.$parameter_value;
		
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrowWhenFreqRuleIsMalformed
	 */
	public function testProperExceptionDataWhenFreqRuleIsMalformed(){
		$parameter_value = '-1';
		$raw_rrule = 'RRULE:FREQ='.$parameter_value;
		
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::FREQ, $e->getParameterName());
			$this->assertEquals($parameter_value, $e->getParameterValue());
		}
	}
	
	public function testUntilGetsProperlyFilled(){
		$parameter_value = '19870101T000000Z';
		
		$raw_rrule = 'RRULE:UNTIL='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals($parameter_value, $rrule->getUntil()->format('Ymd\THis\Z'));
	}
	public function testUntilIsInUTCTimezone(){
		$parameter_value = '19870101T000000Z';
		
		$raw_rrule = 'RRULE:UNTIL='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals('UTC', $rrule->getUntil()->getTimezone()->getName());
	}
	public function testUntilInCurrentTimezoneIsInCurrentTimezone(){
		$parameter_value = '19870101T000000Z';
		
		$raw_rrule = 'RRULE:UNTIL='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals(date_default_timezone_get(), $rrule->getUntilInDefaultTimezone()->getTimezone()->getName());
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenUntilIsMalformed(){
		$parameter_value = '2012-01-01 14:14:14';
		
		$raw_rrule = 'RRULE:UNTIL='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenUntilIsMalformed
	 */
	public function testProperExceptionDataWhenUntilIsMalformed(){
		$parameter_value = '-1';
		$raw_rrule = 'RRULE:UNTIL='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::UNTIL, $e->getParameterName());
		}
	}
	
	public function testIntervalGetsProperlyFilled(){
		$parameter_value = '1';
		
		$raw_rrule = 'RRULE:INTERVAL='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals($parameter_value, $rrule->getInterval());
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenIntervalIsMalformed(){
		$parameter_value = '0';
		
		$raw_rrule = 'RRULE:INTERVAL='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenIntervalIsMalformed
	 */
	public function testProperExceptionDataWhenIntervalIsMalformed(){
		$parameter_value = '-1';
		$raw_rrule = 'RRULE:INTERVAL='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::INTERVAL, $e->getParameterName());
		}
	}
	
	public function testWkstGetsProperlyFilled(){
		$parameter_value = \Kronos\RRule\Enums\Days::FRIDAY;
		
		$raw_rrule = 'RRULE:WKST='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals($parameter_value, $rrule->getWkst());
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenWkstIsMalformed(){
		$parameter_value = 'MONDAY';
		
		$raw_rrule = 'RRULE:WKST='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenWkstIsMalformed
	 */
	public function testProperExceptionDataWhenWkstIsMalformed(){
		$parameter_value = '-1';
		$raw_rrule = 'RRULE:WKST='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::WKST, $e->getParameterName());
		}
	}
	
	
	public function testCountGetsProperlyFilled(){
		$parameter_value = '2';
		
		$raw_rrule = 'RRULE:COUNT='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$this->assertEquals($parameter_value, $rrule->getCount());
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenCountIsMalformed(){
		$parameter_value = '-12,29';
		
		$raw_rrule = 'RRULE:COUNT='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenCountIsMalformed
	 */
	public function testProperExceptionDataWhenCountIsMalformed(){
		$parameter_value = '-12,10';
		$raw_rrule = 'RRULE:COUNT='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::COUNT, $e->getParameterName());
		}
	}
	
	
	public function testBydayGetsProperlyFilled(){
		$possible_parameter_values = \Kronos\RRule\Enums\Days::toArray();
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYDAY='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByDay();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	
	public function testBydayGetsProperlyFilledWhenUsingIntegerInFrontOfValues(){
		$possible_parameter_values = array('-1'.\Kronos\RRule\Enums\Days::FRIDAY, '+1'.\Kronos\RRule\Enums\Days::MONDAY, \Kronos\RRule\Enums\Days::THURSDAY);
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYDAY='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByDay();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenBydayIsMalformed(){
		$parameter_value = 'MO,TU,WE,TAH';
		
		$raw_rrule = 'RRULE:BYDAY='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenBydayIsMalformed
	 */
	public function testProperExceptionDataWhenBydayIsMalformed(){
		$parameter_value = 'AS,MO';
		$raw_rrule = 'RRULE:BYDAY='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYDAY, $e->getParameterName());
		}
	}
	
	public function testByhourGetsProperlyFilled(){
		$possible_parameter_values = array();
		for($i = 0; $i < 24; $i++){
			$possible_parameter_values[] = $i;
		}
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYHOUR='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByHour();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenByhourIsMalformed(){
		$parameter_value = '24,23';
		
		$raw_rrule = 'RRULE:BYHOUR='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenByhourIsMalformed
	 */
	public function testProperExceptionDataWhenByhourIsMalformed(){
		$parameter_value = '-1,23';
		$raw_rrule = 'RRULE:BYHOUR='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYHOUR, $e->getParameterName());
		}
	}
	
	public function testByminuteGetsProperlyFilled(){
		$possible_parameter_values = array();
		for($i = 0; $i < 60; $i++){
			$possible_parameter_values[] = $i;
		}
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYMINUTE='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByMinute();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenByminuteIsMalformed(){
		$parameter_value = '61,45';
		
		$raw_rrule = 'RRULE:BYMINUTE='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
	}
	/**
	 * @depends testExceptionGetsThrownWhenByminuteIsMalformed
	 */
	public function testProperExceptionDataWhenByminuteIsMalformed(){
		$parameter_value = '-1,23';
		$raw_rrule = 'RRULE:BYMINUTE='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYMINUTE, $e->getParameterName());
		}
	}
	
	public function testBymonthGetsProperlyFilled(){
		$possible_parameter_values = \Kronos\RRule\Enums\Months::toArray();
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYMONTH='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByMonth();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenBymonthIsMalformed(){
		$parameter_value = '0,1,13';
		
		$raw_rrule = 'RRULE:BYMONTH='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenBymonthIsMalformed
	 */
	public function testProperExceptionDataWhenBymonthIsMalformed(){
		$parameter_value = '-1,0';
		
		$raw_rrule = 'RRULE:BYMONTH='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYMONTH, $e->getParameterName());
		}
	}
	
	public function testBymonthdayGetsProperlyFilled(){
		$possible_parameter_values = array();
		for($i = -31; $i < 32; $i++){
			if($i == 0) continue;
			$possible_parameter_values[] = $i;
		}
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYMONTHDAY='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByMonthDay();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenBymonthdayIsMalformed(){
		$parameter_value = '0,1,13';
		
		$raw_rrule = 'RRULE:BYMONTHDAY='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenBymonthdayIsMalformed
	 */
	public function testProperExceptionDataWhenBymonthdayIsMalformed(){
		$parameter_value = '-32,5';
		
		$raw_rrule = 'RRULE:BYMONTHDAY='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYMONTHDAY, $e->getParameterName());
		}
	}
	
	public function testBysecondGetsProperlyFilled(){
		$possible_parameter_values = array();
		for($i = 0; $i < 61; $i++){
			if($i == 0) continue;
			$possible_parameter_values[] = $i;
		}
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYSECOND='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getBySecond();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenBysecondIsMalformed(){
		$parameter_value = '-1,3';
		
		$raw_rrule = 'RRULE:BYSECOND='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenBysecondIsMalformed
	 */
	public function testProperExceptionDataWhenBysecondIsMalformed(){
		$parameter_value = '61,-8';
		
		$raw_rrule = 'RRULE:BYSECOND='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYSECOND, $e->getParameterName());
		}
	}
	
	public function testBysetposGetsProperlyFilled(){
		$possible_parameter_values = array(-366,-1,1,366);
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYSETPOS='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getBySetPos();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenBysetposIsMalformed(){
		$parameter_value = '-367,355';
		
		$raw_rrule = 'RRULE:BYSETPOS='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenBysecondIsMalformed
	 */
	public function testProperExceptionDataWhenBysetposIsMalformed(){
		$parameter_value = '0,300';
		
		$raw_rrule = 'RRULE:BYSETPOS='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYSETPOS, $e->getParameterName());
		}
	}
	
	public function testByweeknoGetsProperlyFilled(){
		$possible_parameter_values = array();
		for($i = -53; $i++; $i<54){
			if($i == 0) continue;
			$possible_parameter_values[] = $i;
		}
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYWEEKNO='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByWeekNo();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenByweeknoIsMalformed(){
		$parameter_value = '0,51';
		
		$raw_rrule = 'RRULE:BYWEEKNO='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenBysecondIsMalformed
	 */
	public function testProperExceptionDataWhenByweeknoIsMalformed(){
		$parameter_value = '52,-54';
		
		$raw_rrule = 'RRULE:BYWEEKNO='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYWEEKNO, $e->getParameterName());
		}
	}
	
	public function testByyeardayGetsProperlyFilled(){
		$possible_parameter_values = array(-366,-1,1,366);
		$parameter_value = implode(',', $possible_parameter_values);
		
		$raw_rrule = 'RRULE:BYYEARDAY='.$parameter_value;
		$rrule = \Kronos\RRule::fromRawRRule($raw_rrule);
		
		$returned_value = $rrule->getByYearDay();
		$this->assertInternalType('array', $returned_value);
		$this->assertEquals(count($possible_parameter_values), count($returned_value));
		
		foreach($returned_value as $value){
			$this->assertTrue(in_array($value, $possible_parameter_values));
		}
	}
	/**
	 * @expectedException \Kronos\RRule\Exceptions\InvalidParameterValue
	 */
	public function testExceptionGetsThrownWhenByyeardayIsMalformed(){
		$parameter_value = '0,366';
		
		$raw_rrule = 'RRULE:BYYEARDAY='.$parameter_value;
		\Kronos\RRule::fromRawRRule($raw_rrule);
		
		return $parameter_value;
	}
	/**
	 * @depends testExceptionGetsThrownWhenByyeardayIsMalformed
	 */
	public function testProperExceptionDataWhenByyeardayIsMalformed(){
		$parameter_value = '300,-367';
		
		$raw_rrule = 'RRULE:BYYEARDAY='.$parameter_value;
		try{
			\Kronos\RRule::fromRawRRule($raw_rrule);
			$this->fail('Should have thrown \Kronos\RRule\Exceptions\InvalidParameterValue.');
		}
		catch(\Kronos\RRule\Exceptions\InvalidParameterValue $e){
			$this->assertEquals($parameter_value, $e->getParameterValue());
			$this->assertEquals(\Kronos\RRule\Enums\Parameters::BYYEARDAY, $e->getParameterName());
		}
	}
	
}