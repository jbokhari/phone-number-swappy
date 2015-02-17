<?php
class SwappyOption_int extends SwappyOption {

	public $rules = array();
	
	public function init_tasks($options){
		if ( isset($options['rules']) ){
			if ( isset( $options['rules']['min'] ) ){
				$this->rules['min'] = floatval( $options['rules']['min'] );
			}
			if ( isset( $options['rules']['max'] ) ){
				$this->rules['max'] = floatval( $options['rules']['max'] );
			}
			if ( isset( $options['rules']['step'] ) ){
				$this->rules['step'] = floatval( $options['rules']['step'] );
			}
		}
	}
	public function generate_maximum_value_html(){
		if ( isset( $this->rules['min'] ) ){
			return "min='{$this->rules['min']}'";
		} else {
			return "";
		}
	}
	public function generate_minimum_value_html(){
		if ( isset( $this->rules['max'] ) ){
			return "max='{$this->rules['max']}'";
		} else {
			return "";
		}
	}
	public function generate_step_value_html(){
		if ( isset( $this->rules['step'] ) ){
			return "step='{$this->rules['step']}'";
		} else {
			return "";
		}
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$min = $this->generate_minimum_value_html();
		$max = $this->generate_maximum_value_html();
		$step = $this->generate_step_value_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='number' $max $min $step name='{$name}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		$value = floatval( $newValue );
		if (!empty($this->rules)){
			if ( isset($this->rules['min'] ) ){
				if ( $newValue < $this->rules['min'] ){
					$this->invalidate();
					return $this->get_value();
				}
			}
			if ( isset($this->rules['max'] ) ){
				if ( $newValue > $this->rules['max'] ){
					$this->invalidate();
					return $this->get_value();
				}
			}
			if ( isset($this->rules['step'] ) ){
				// is there a need to enforce step?
				// maybe it shouldn't be called a rule at all?
			}
		}
		return $value;
	}
	public function output_filter( $output ){
		return $output;
	}
}