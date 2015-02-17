<?php
final class SwappyOption_bool extends SwappyOption {
	protected function init_tasks($options){
		return;
	}
	public function get_option_field_html(){
		$value = $this->get_value(null, true);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$checked = $this->checked_html();
		$name = $this->name;
		$id = $this->id;
		$this->checked_html();
		return "<input id='{$id}' class='{$classes}' {$checked} {$required} type='checkbox' name='{$name}' value='1' />";
	}
	public function is_required(){
		return false;
	}
	public function checked_html(){
		if ($this->get_value() == "true"){
			return "checked='checked'";
		} else {	
			return "";
		}
	}
	public function validate($value = null){
		if ($value && $value != "false")
			return "true";
		else
			return "false";
	}
	public function output_filter($input){
		return $input;
	}
}