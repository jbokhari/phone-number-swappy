<?php
/**
 * SwappyOption basic elements of individual lava options. Contains two abstract methods
 * @abstract validate() converts new information to db safe value
 * @abstract get_option_field_html() generates html field for admin page
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */
abstract class SwappyOption extends PhoneNumberSwappyLogErrorClass {
	public $name;
	public $label;
	public $id;
	public $value;
	public $type = "str";
	public $ui = "default";
	public $default = "";
	public $in_menu = "true";
	public $required = false;
	public $classes = array();
	public $label_classes = array();
	public $in_js = false;
	public $tab = 0;
	public static $single_instance_scripts = array();
	protected $invalid = true;
	function __construct($prefix, array $options, $no = 0){
		// print_r($options);
		$this->_log("Instantiated");
		$this->prefix = $prefix;
		if ( isset( $options['name'] ) ){
			$this->name = $options['name'];
		} else {
			$this->_error("<code>Name</code> field not set for option {$this->name}. The label field and name are required.");
		}
		if ( isset( $options['label'] ) ){
			$this->label = $options['label'];
		} else {
			$this->_error("<code>Label</code> field not set for option {$this->name}. The label field and name are required.");
		} 
		$this->classes[] = "field-" . $no;
		$this->fieldnumber = $no;
		//for repeater fields
		if ( isset($options['id'] ) && !empty( $options['id'] ) )
			$this->id = $options['id'];
		else
			$this->id = $options['id'] = $this->prefix . $options['name'];
		$this->default_optionals($options);
		$this->init_tasks($options);
		$script = $this->get_single_instance_footer_scripts();
		// var_dump($script);
		if ($script)
			PhoneNumberSwappyCore::set_si_footer_scripts($script);
	}
	/**
	 * Not required. Simple helper function to run after base tasks are complete (creating the option)
	 * @param type $options 
	 * @return type
	 */
	protected function init_tasks($options){}
	/**
	 * Adds single input to the label_classes array, or merges array items.
	 * @param array or string $class 
	 * @return void
	 */
	public function add_label_class($class){
		$this->add_class($class, "label_classes");
	}
	public function add_outer_class($class){}
	public function input_classes($ref = "classes"){
		$classes = implode( " ", $this->$ref );
		$classes = sanitize_html_class( $classes );
		return $classes;
	}
	public function add_class($class, $ref = "classes"){
		if (is_array($class))
			$this->$this->$ref = array_merge($this->$this->$ref, $class);
		else array_push($this->$ref, $class);
	}
	/**
	 * Generates and returns option label html
	 * @return string
	 */
	public function get_option_label_html(){
		$html = "";
		$classes = $this->get_label_html_classes();
		$required = $this->required ? "*" : "";
		$html .= "<label class='$classes' for='{$this->id}'>{$this->label}{$required}</label>";
		return $html;
	}

	/**
	 * Alias of add_label_class Gets html ready list of label classes, separated by spaces
	 * @return string
	 */
	public function get_label_html_classes(){
		return $this->input_classes("label_classes");
	}

	public function get_form_js(){
		return "";
	}
	final public function get_option_header_html(){
		return "<div id='{$this->id}-container' class='option-block field-{$this->fieldnumber}'>";
	}
	final public function get_option_footer_html(){
		$return = $this->get_form_js();
		$return = "<div style='clear:both;'></div>";
		$return = "</div>";
		return $return;
	}
	/**
	 * Used by LavaPlugin class to queue JavaScript to be appended to the options page when this option is loaded. These scripts are defined in this function when a script is needed to be run only one time for no matter how many options of this type are created.
	 * @return (string)
	 */
	public function get_single_instance_footer_scripts(){}
	final private function delete_value(){
		return delete_option( $this->id );
	}
	public function default_optionals($options){
		$this->_log("Run default_optionals()");
		if ( isset( $options['type'] ) )
			$this->type = $options['type'];
		if ( isset( $options['default'] ) )
			$this->default = $options['default'];
		if ( isset( $options['in_menu'] ) )
			$this->in_menu = $options['in_menu'];
		if ( isset( $options['class'] ) ){
			if( is_array( $options['class'] ) )
				array_merge($this->classes, $options['class']);
			else
				array_push($this->classes, $options['class'] );
		}
		if ( isset( $options['in_js'] ) )
			$this->in_js = $options['in_js'];
		if ( isset( $options['tab'] ) )
			$this->tab = $options['tab'];
		if ( isset( $options['required'] ) )
			$this->required = $options['required'];
	}
	final public function get_value($default = null){
		if ($default === null){
			$this->_log("No default value provided for $this->name when running get_value(), internal default ($this->default) was used instead.");
			$default = $this->default;
		}
		if( ! $this->value){
			$this->_log("No current value for $this->name, getting option $this->id from database.");
			$value = get_option($this->id, $default);
			$this->value = $this->output_filter($value);
		}
		return $this->value;
	}
	/**
	 * Override output_filter() rather than rewrite get_value()
	 * @param type $input 
	 * @return type
	 */
	public function output_filter($input){
		return $input;
	}
	public function is_required(){
		if ($this->required){
			$this->_error("set_value() could not be performed on {$this->name} because it is required and the value was empty after validation.");
			$this->invalidate();
			return true;
		} else {
			return false;
		}
	}
	protected function invalidate($msg = ""){
		$this->invalid = true;
		if ($msg != ""){
			$this->error_tooltip = $msg;
		}
		$this->add_class('invalid');
	}
	public function set_value($newValue = ""){
		$newValue = $this->validate($newValue);
		$this->_log("Function set_value() was run for $this->name using $newValue (after validation.");
		if ( $newValue == "" && $this->is_required() )
			return false;
		$return = update_option($this->id, $newValue);
		if ($return)
			$this->_log("Value was successfully stored.");
		else
			$this->_log("Value was not stored!!!");
		return $return;
	}
	protected function required_html(){
		return $this->required ? 
			"required='required'" :
			"";
	}
	abstract public function validate($newValue = "");
	abstract public function get_option_field_html();
}

/* EOF */