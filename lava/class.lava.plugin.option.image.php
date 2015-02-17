<?php
final class SwappyOption_image extends SwappyOption {
	public $requires_script = true;
	static $instance = 1;
	protected function init_tasks($options){
		$this->add_container_class("image-ui-" . $this->ui);
	}
	public function get_image_meta($index, $default = ""){
		$val = $this->get_value();
		if ( isset($val[ $index ] ) ){
			return $val[ $index ];
		} else {
			return $default;
		}
	}
	public function get_option_field_html(){
		self::$instance++;
		$value = $this->get_value();
		$url = $this->get_image_meta( "url" );
		$url = esc_url_raw( $url );
		$imgid =  $this->get_image_meta( "id" );
		$height =  $this->get_image_meta( "height" );
		$width = $this->get_image_meta( "width" );
		$classes = $this->input_classes();
		$outerclasses = $this->get_container_html_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$instance = self::$instance;
		$html = "";
		$fieldnumber = $this->fieldnumber;
		$html .= "<div id='image_{$id}_{$instance}_{$fieldnumber}_container' data-image-id='{$id}_{$instance}_{$fieldnumber}' class='image-container'>";
			$html .= "<img src='{$url}' alt='' class='{$id}_{$instance}_{$fieldnumber}_preview image-preview {$id}_{$instance}-preview'>";
		$html .= "</div>";
		$html .= "<br>";
		$html .= "<input class='{$id}_{$instance}_{$fieldnumber} image-source {$classes}' {$required} type='hidden' name='{$name}[url]' value='{$url}' />";
		$html .= "<input class='{$id}_{$instance}_{$fieldnumber} image-id {$classes}' {$required} type='hidden' name='{$name}[id]' value='{$imgid}' />";
		$html .= "<input class='{$id}_{$instance}_{$fieldnumber} image-width {$classes}' {$required} type='hidden' name='{$name}[width]' value='{$width}' />";
		$html .= "<input class='{$id}_{$instance}_{$fieldnumber} image-height {$classes}' {$required} type='hidden' name='{$name}[height]' value='{$height}' />";
		$html .= "<input type='button' data-image-id='{$id}_{$instance}_{$fieldnumber}' class='{$id}_{$instance}_{$fieldnumber}_button media-upload media-{$id}_{$instance}' value='Upload'>";
		$html .= "<input type='button' data-image-id='{$id}_{$instance}_{$fieldnumber}' class='{$id}_{$instance}_{$fieldnumber}_clear media-upload-clear media-{$id}_{$instance}-clear' value='Clear'>";
		return $html;
	}
	public function validate($newValue = ""){
		if ( is_array($newValue) ){
			if ( ! isset($newValue['url'] ) || empty( $newValue['url'] ) )
				return false;
			foreach ($newValue as $i => $value) {
				$safeValue[$i] = sanitize_text_field( $value );
			}
			$newValue = serialize($safeValue);
			return $newValue;
		} else {
			return "Error";
		}
	}

	public function register_needed_scripts(){
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				$this->scriptmgmt->register_local_script("lava.option.image.default.js", "lava.option.image.default.js", array() );
				break;
		}
	}
	public function output_filter($output){
		$arrayoutput = maybe_unserialize($output);
		return $arrayoutput;
		// return $output;
	}
}