<?php
final class SwappyOptioncolor extends SwappyOption {
	public $ui;
	public $scripts = array(
        array( "jscolor", "libs/jscolor/jscolor.js", array() ),
        array( "rgbapicker", "libs/colorpickerrgba/rgbacolorpicker.min.js", array("jquery") )
	);
	public $styles = array(
		array("rgbapicker", "libs/colorpickerrgba/rgbacolorpicker.css", array())
    );
	public function init_tasks($options){
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles' ) );
		$this->ui = isset($options['ui']) ? $options['ui'] : "hex";
		if ($this->ui == "rgba")
			$this->add_class("rgbacolorpicker");
		else if ($this->ui == "hex")
			$this->add_class("lava-color-chooser");
		else if ($this->ui == "wp")
			$this->add_class("wp-colorpicker-ui");
	}
	/**
	 * Registers script. Tracks whether script was registered
	 * or not via the $single_instance_scripts static var
	 * Returns the script's file name
	 * @return string OR boolean false
	 */
	public function get_single_instance_footer_scripts(){
		if ( ! empty( self::$single_instance_scripts[$this->ui] ) )
			return;
		self::$single_instance_scripts[$this->ui] = true;
		switch ( $this->ui ){
			case "rgba" :
				//scripts will be loaded from plugin/library/js/options
				return "lava.option.color.rgbaui.js";
				break;
			case "wp" :
				return "lava.option.color.wp.js";
				break;
		}
		return false; //default return false
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= "<input name='{$id}' value='{$value}' class='{$classes}'>";
		return $html;
	}
	public function enqueue_scripts_and_styles(){
		foreach ($this->styles as $style){
			list($handle, $source, $dependencies) = $style;
			$path = PhoneNumberSwappyCore::get_css_dir();
			$fullpath = $path . $source;
			wp_register_style( $handle, $fullpath, $dependencies );
		}
		foreach ($this->scripts as $script){
			list($handle, $source, $dependencies) = $script;
			$path = PhoneNumberSwappyCore::get_js_dir();
			$fullpath = $path . $source;
			wp_register_script( $handle, $fullpath, $dependencies );
		}
		if ($this->ui == "hex" ){
			wp_enqueue_script( "jscolor" );
		}
		if ($this->ui == "rgba" ){
			wp_enqueue_script( "rgbapicker" );
			wp_enqueue_style( "rgbapicker" );
		}
	}
	public function validate($newValue = ""){
		return sanitize_file_name( $newValue );
	}
}