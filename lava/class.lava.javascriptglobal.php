<?php 
class SwappyJavaScriptGlobal {
	private $values = array( "__conditions" => array() );
	public function get_values(){
		return $this->values;
	}
	public function add_value($name, $value){
		if ( $name == "__conditions" ){
			throw new Exception("__conditions is a reserved item name for javascript values.", 1);
		}
		$this->values[$name] = $value;
	}	
	public function add_conditional($id, $values){
		if ( empty( $values['action'] ) ||  empty( $values['when'] ) ||  empty( $values['compare'] ) ||  empty( $values['value'] ) ){
			throw new Exception("Condition was not set correctly, missing parameters.", 1);
		}
		$values['on'] = $id;
		array_push($this->values['__conditions'], $values);
	}
}