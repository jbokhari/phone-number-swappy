<?php
class SwappyConfiguration {

	private $registry = array();
	private static $instance = null;

	private function __construct() {}
	private function __clone() {}

	public static function getInstance() {
		if($this->instance === null) {
			$this->instance = new Configuration();
		}

		return $this->instance;
	}
	public function add_script(){

	}
	public function set($key, $value) {
		if (isset($this->registry[$key])){
			throw new Exception("There is already an entry for key " . $key);
		}
	}
	public function get($key){
		if (isset($this->registry[$key])){
			throw new Exception("There is no entry for key " . $key);
		}

		return $this->registry[$key];

	}

}