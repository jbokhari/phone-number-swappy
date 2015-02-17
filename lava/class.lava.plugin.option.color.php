<?php
final class SwappyOption_color extends SwappyOption {
	public $ui;
	static $added_style = false;
    protected function init_tasks( $options ){
    	if ($this->ui == "default") $this->ui = "wp";
		$class = "colorpicker-" . $this->ui . "-ui"; 
		$this->add_class( $class );
		switch( $this->ui ){
			case "rgba" :

				$jsargs = array("handle" => "rgbacolorpicker.min.js", "source" => LAVAPLUGINURL . '/library/js/libs/colorpickerrgba/rgbacolorpicker.js' );
				$cssargs = array("handle" => "rgbacolorpicker.css", "source" => LAVAPLUGINURL . '/library/css/libs/colorpickerrgba/rgbacolorpicker.css' );
				$this->scriptmgmt->add_script( $jsargs );
				// $this->scriptmgmt->add_style( $cssargs );
				$this->scriptmgmt->register_local_script('color-picker-rgba-ui', 'lava.option.color.rgbaui.js', array('jquery', 'rgbacolorpicker.min.js' ) );
				break;
			case "wp" :
				$this->scriptmgmt->register_local_script('lava.option.color.wp.js', 'lava.option.color.wp.js', array('wp-color-picker') );
				$this->scriptmgmt->add_script( array("handle" => 'wp-color-picker' ) );
				break;
			case "hex" :
				break;
			default :
				break;
		}
				/**

			$rgbascript = array("handle" => "codemirror.css", "source" => "libs/colorpickerrgba/rgbacolorpicker.min.js" );
			$solarized = array("handle" => "codemirror-theme-ambiance.css", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.8.0/theme/solarized.min.css" );
			$jsargs = array("handle" => "codemirror.js", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.8.0/codemirror.js" );
			$jscssmode = array("handle" => "codemirror.cssmode", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.8.0/mode/css/css.min.js" );
			$this->scriptmgmt->add_script( $jsargs ); //more manual way of registering other types of scripts outside of lava/options/js path
			$this->scriptmgmt->add_script( $jscssmode ); //more manual way of registering other types of scripts outside of lava/options/js path
			$this->scriptmgmt->add_style( $codemirrorcssargs );
			$this->scriptmgmt->add_style( $solarized );

			$this->scriptmgmt->register_local_script('lava.option.textarea.code.js', 'lava.option.textarea.code.js', array( 'codemirror.js' ) ); //quick way to register script by putting name in lava/options/js path
			**/
			// $this->css_source[ ] = $themecssargs;
	}
	// public function init_tasks($options){
	// 	add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles' ) );
	// 	$this->ui = isset($options['ui']) ? $options['ui'] : "hex";
	// 	if ($this->ui == "rgba")
	// 		$this->add_class("rgbacolorpicker");
	// 	else if ($this->ui == "hex")
	// 		$this->add_class("lava-color-chooser");
	// 	else if ($this->ui == "wp")
	// 		$this->add_class("wp-colorpicker-ui");
	// }
	/**
	 * Registers script. Tracks whether script was registered
	 * or not via the $single_instance_scripts static var
	 * Returns the script's file name
	 * @return string OR boolean false
	 */
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= $this->add_style_one_time();
		$html .= "<div class='color-outer'>";
		$html .= "<div class='color-inner'>";
		$html .= "<input id='{$id}' type='text' name='{$name}' value='{$value}' class='{$classes}'>";
		$html .= "</div>";
		$html .= "</div>";
		return $html;
	}
	public function add_style_one_time(){
		if ($this->ui == "wp" && self::$added_style === false){
			self::$added_style = true;
			ob_start(); ?>
			<style>
					input.wp-picker-clear,
				input.colorpicker-wp-ui {
					margin: 0 6px 6px 0;
				}
				input.colorpicker-wp-ui {
					width: 94px;
				}
				.color-field .wp-color-result {
					background-color: #f7f7f7;
					border: 1px solid #ccc;
					-webkit-border-radius: 3px;
					border-radius: 3px;
					cursor: pointer;
					height: 22px;
					margin: 0 6px 6px 0;
					position: relative;
					-webkit-user-select: none;
					-moz-user-select: none;
					-ms-user-select: none;
					user-select: none;
					vertical-align: bottom;
					display: inline-block;
					padding-left: 30px;
					-webkit-box-shadow: 0 1px 0 rgba(0,0,0,.08);
					box-shadow: 0 1px 0 rgba(0,0,0,.08);
				}
				.color-field .wp-color-result:after {
					background: #f7f7f7;
					-webkit-border-radius: 0 2px 2px 0;
					border-radius: 0 2px 2px 0;
					border-left: 1px solid #ccc;
					color: #555;
					content: attr(title);
					display: block;
					font-size: 11px;
					line-height: 22px;
					padding: 0 6px;
					position: relative;
					right: 0;
					text-align: center;
					top: 0;
					-webkit-box-shadow: inset 0 1px 0 #fff;
					box-shadow: inset 0 1px 0 #fff;
				}
				</style>
			<?php 
			$return = ob_get_contents();
		}
		return "";
	}
	
	public function validate($newValue = ""){
		if ( ! preg_match( '/^#[a-f0-9]{6}$/i', $newValue) ){
			return "";
		} else {
			return $newValue;
		}
	}
	public function output_filter($output){
		return $output;
	}
}