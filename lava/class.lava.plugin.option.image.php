<?php
final class SwappyOptionimage extends SwappyOption {
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= "<div class='image-container'>
			<img id='{$id}_preview' class='image-preview {$id}-preview' src='{$value}' alt=''></div>";
		$html .= "<input id='{$id}' class='{$classes}' {$required} type='hidden' name='{$id}' value='{$value}' />";
		$html .= "<input id='{$id}_button' type='button' class='media-upload media-{$id}' value='Upload'>";
		$html .= "<input id='{$id}_clear' type='button' class='media-upload-clear media-{$id}-clear' value='Clear'>";
		return $html;
	}
	public function validate($newValue = ""){
		return sanitize_file_name( $newValue );
	}
}