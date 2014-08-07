<?php 
final class SwappyOptiontextarea extends SwappyOption {
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_textarea($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		return "<textarea id='{$id}' class='{$classes}' {$required} type='text' name='{$id}'/>{$value}</textarea>' ";
	}
	public function validate($newValue = ""){
		/* later we can add better validation here like string length, zip code validation and stuff like that */
		return sanitize_text_field( $newValue );
	}
}
