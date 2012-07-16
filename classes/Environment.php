<?php

class Environment {
	protected $_data = array(
		'name'  => 'production',
	);

	private function __construct() {} //prevents initialization from outside the class

	protected static $singleton;
	static function Singleton() {
		return (!static::$singleton) ? static::$singleton = new static() : static::$singleton;
	}	
	
	public function setName($name = 'development') {
		$this->_data['name'] = $name;
		return $this;
	}
	
	public function __get($index) {
		$index = strtolower($index);
		return isset($this->_data[$index]) ? $this->_data[$index] : null;
	}
	
	public function __set($index, $value) {
		$this->_data[strtolower($index)] = $value;
	}
	
	public function __isset($index) {
		return isset($this->_data[strtolower($index)]);
	}
	
	public function __call($name, $args) {
		switch (substr($name, 0, 3)) {
			
		case 'get':
			$index = strtolower(substr($name, 3));
			return isset($this->_data[$index]) ? $this->_data[$index] : null;
			
		case 'set':
			$index = strtolower(substr($name, 4));
			$this->_data[$index] = $args[0];
			return $this;

		default:
			throw new \BadMethodCallException("Unknown function Environment->$name.");
			
		}
	}
	
}
