<?php 
/**
 * Lava option string Object, basic text field with no special validation
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */
final class SwappyOption_str extends SwappyOption {
	protected function init_tasks($options){
		return;
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = stripcslashes($value);
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='text' name='{$name}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		/* later we can add better validation here like string length, zip code validation and stuff like that */
		$newValue = apply_filters( "lava_validate_" . $this->name, $newValue );
		return sanitize_text_field( $newValue );
	}
	public function output_filter($value){
		$newvalue = stripcslashes($value);
		return $newvalue;
	}
}