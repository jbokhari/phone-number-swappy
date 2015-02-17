<?php
/**
 * LavaOption basic elements of individual lava options. Contains two abstract methods
 * @package Lava
 * @author Jameel Bokhari
 * @license GPL22
 */
abstract class SwappyOption {
	public $logging;
	public $name;
	public $label;
	public $id;
	public $value = null;
	public $type = "";
	public $ui = "default";
	public $default = "";
	public $in_menu = "true";
	public $required = false;
	public $classes = array();
	public $container_classes = array();
	public $label_classes = array();
	public $optionals = array();
	public $in_js = false;
	public $tab = 0;
	public $post_id = null;
	protected $invalid = true;
	function __construct($prefix, array $options, $no = 0, $scriptmgmt, $jsvars){
		$this->jsvars = $jsvars;
		$this->scriptmgmt = $scriptmgmt;
		$this->prefix = $prefix;
		$this->logger = $this->generate_logging_object();

		if ( isset( $options['name'] ) ){
			$this->name = $options['name'];
		} else {
			$this->logger->_error("<code>Name</code> field not set for option {$this->name}. The label field and name are required.");
		}

		if ( isset( $options['condition'] ) ){
			$this->condition = $options['condition'];
		}
		if ( isset( $options['in_js'] ) ){
			$this->in_js = $options['in_js'];
		}
		if ( isset( $options['label'] ) ){
			$this->label = $options['label'];
		} else {
			$this->logger->_error("<code>Label</code> field not set for option {$this->name}. The label field and name are required.");
		} 

		$this->meta_box = isset( $options['meta_box'] ) ? $options['meta_box'] : false;

		$this->type = $options['type'];
		$this->classes[] = "field-" . $no;
		$this->fieldnumber = $no;
		//for repeater fields
		if ( isset( $options['id'] ) && !empty( $options['id'] ) )
			$this->id = strtolower( $options['id'] );
		else
			$this->id = $options['id'] = strtolower( $this->prefix . $options['name'] );
		$this->ui = isset( $options['ui'] ) ? $options['ui'] : "default";
		$this->default_actions($options);
	}
	protected function default_actions($options){
		$this->register_optionals();
		$this->default_optionals($options);
		$this->register_needed_scripts();
		$this->init_tasks($options);
		$this->add_container_class("{$this->type}-field");
		if ( isset( $this->in_js ) && $this->in_js ){
			$this->jsvars->add_value( $this->id, $this->get_value() );
		}
		if ( isset( $this->condition ) && !empty($this->condition) ){
			$this->register_conditional_variables($this->id, $this->condition);
		}
	}
	public function register_conditional_variables($id, $condition){
		$this->jsvars->add_conditional($id, $condition);
	}
	public function generate_logging_object(){
		return new SwappyLogging($this->name);
	}
	/**
	 * Simple helper function to run after base tasks are complete (creating the option).
	 * @param type $options 
	 * @return type
	 */
	abstract protected function init_tasks($options);
	/**
	 * Adds single input to the label_classes array, or merges array items.
	 * @param array or string $class 
	 * @return void
	 */
	public function add_container_class($class){
		$this->add_class($class, "container_classes");
	}
	public function add_label_class($class){
		$this->add_class($class, "label_classes");
	}
	public function add_outer_class($class){
		$this->add_class($class, "outer_classes");
	}
	public function input_classes(){
		return $this->get_classes_list("classes");
	}
	public function get_classes_list($ref = "classes"){
		$classes = implode( " ", $this->$ref );
		// $classes = sanitize_html_class( $classes );
		return $classes;
	}
	public function add_class($class, $ref = "classes"){
		if (is_array($class)){
			$this->$ref = array_merge($this->$ref, $class);
			return;
		}
		if ( !isset($this->$ref) ){
			$this->$ref = array();
		}
		array_push($this->$ref, $class);
		return;
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
	public function get_outer_class(){
		return $this->get_classes_list("outer_classes");
	}
	public function get_label_html_classes(){
		return $this->get_classes_list("label_classes");
	}
	public function get_container_html_classes(){
		return $this->get_classes_list("container_classes");
	}

	public function get_form_js(){
		return "";
	}
	final public function get_option_header_html(){
		$classes = $this->get_container_html_classes();
		return "<div id='{$this->id}-container' class='option-block field-{$this->fieldnumber} $classes'>";
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
	private function delete_value(){
		return delete_option( $this->id );
	}
	/**
	 * 
	 * Register optionals
	 *
	 **/
	public function register_optionals(){
		return array();
	}
	public function default_optionals($options){
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
		$this->description = isset( $options['description'] ) ? $options['description'] : "";
		foreach($this->optionals as $o){
			if ( isset( $options[$o] ) ){
				$this->$o = $options[ $o ];
			}
		}
	}
	public function get_value($default = null, $refresh = false){
		if ($default === null)
			$default = $this->default;
		if( $this->value === null || $refresh ){
			if ( $this->meta_box && $this->post_id > 0 ){
				// echo "Start get value of $this->id for post id $this->post_id";
				$value = get_post_meta( $this->post_id , $this->id, true );
				// var_dump($this->post_id);
				// var_dump($value);
			} else {
				$value = get_option($this->id, $default);
			}
			
			$this->value = $this->output_filter($value);
		}
		return $this->value;
	}
	/**
	 * Override output_filter() rather than rewrite get_value()
	 * @param type $input 
	 * @return type
	 */
	abstract public function output_filter($input);
	public function is_required(){
		if ($this->required){
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
		$this->logger->_log("set_value() was run.");
		$newValue = $this->validate($newValue);
		if ( $newValue == "" && $this->is_required() ){
			$this->invalidate();
			return false;
		}
		// $newValue = $this->output_filter($newValue); //not sure why this was here???
		$this->value = $this->output_filter( $newValue );
		if ( $this->meta_box && $this->post_id > 0 ){
			return update_post_meta( $this->post_id, $this->id, $newValue );
		}
		$update = update_option($this->id, $newValue);
		// var_dump( $update );
		return $update;
	}
	public function get_option_description_html($usewrapper = true){
		$html = "";
		if ( $this->description == "" ){
			return "";
		}
		if ($usewrapper){
			$html .= '<p class="description">';
		}
		
		$html .= $this->description;

		if ($usewrapper){
			$html .= '</p>';
		}
		return $html;
	}
	protected function required_html(){
		return $this->required ? 
			"required='required'" :
			"";
	}
	public function register_needed_scripts(){
		return false;
	}
	/**
	 * set post_id for meta field items
	 **/
	public function set_post_id( $post_id ){
		$this->value = null;
		$this->post_id = "$post_id";
	}
	abstract public function validate($newValue = "");
	abstract public function get_option_field_html();
}

/* EOF */