<?php
final class SwappyOption_email extends SwappyOption {
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='email' name='{$name}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		return sanitize_email( $newValue );
	}
}