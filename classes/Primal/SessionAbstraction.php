<?php 

namespace Primal;

abstract class SessionAbstraction implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable  {
	
	protected $session_key = 'SessionAbstraction';
	
	function import($array) {
		$_SESSION[$this->session_key] = is_array($_SESSION[$this->session_key]) ? array_merge($_SESSION[$this->session_key], $array) : $array;
	}
	
/**
	arrayaccess
*/

	public function &offsetGet($key){
		return $_SESSION[$this->session_key][$key];
	}

	public function offsetSet($key, $value){
		$_SESSION[$this->session_key][$key] = $value;
	}

	public function offsetExists($key) {
		isset($_SESSION[$this->session_key][$key]);
	}

	public function offsetUnset($key){
		unset($_SESSION[$this->session_key][$key]);
	}
	
/**
	Iterator
*/

	protected $iteration_position;
	function rewind() {
		$this->iteration_position = 0;
	}

	function current() {
		return $_SESSION[$this->session_key][$this->iteration_position];
	}

	function key() {
		return $this->iteration_position;
	}

	function next() {
		$this->iteration_position++;
	}

	function valid() {
		return isset($_SESSION[$this->session_key][$this->iteration_position]);
	}

/**
	Serializable
*/

	public function serialize() {
		return json_encode($_SESSION[$this->session_key]);
	}
	public function unserialize($data) {
		$_SESSION[$this->session_key] = json_decode($data, true);
	}
	public function getData() {
		return $_SESSION[$this->session_key];
	}
	
/**
	Countable and IteratorAggrigate
*/

	function count() {
		return count($_SESSION[$this->session_key]);
	}
	
	public function getIterator() {
		return new ArrayIterator($_SESSION[$this->session_key]);
	}
}