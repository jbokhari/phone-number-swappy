<?php
/**
 * Array option. Does not neccessarily store an array but includes a number of choices. These can be arranged with various ui
 * Current ui options: multiple, select, checkboxes, radio
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL2
 * Last updated 7/20/2014
 */
final class SwappyOption_array extends SwappyOption {
	public function init_tasks($options){
		//backwards compaitibility, or to level confusiion over settings
		if ( ! isset( $options['choices'] ) )
			if ( isset( $options['values'] ) )
				$this->choices = $options['values'];
			else 
				$this->logger->_error("Lava option with array type needs choices specified. None were set.");
		else
			$this->choices = $options['choices'];
		$this->ui = "select"; // default ui is select
		if ( $options['ui'] )
			$this->ui = $options['ui'];
	}
	public function multiple_html(){
		if ( $this->ui == "multiple" ){
			// xhtml valid
			return "multiple='multiple'";
		} else {
			return "";
		}
	}
	public function selected_html($choice){

		if ( is_array( $this->get_value() ) && in_array( $choice, $this->get_value() ) ) {
			return "selected='selected'";
		} else {	
			return "";
		}
	}
	public function checked_html($choice){
		if ( is_array( $this->get_value() ) && in_array( $choice, $this->get_value() ) ){
			return "checked='checked'";
		} else {	
			return "";
		}
	}
	public function get_choice_slug($label){
		$return = $label;
		$return = strtolower( $return );
		$return = str_replace(" ", "_", $return);
		$return = esc_attr( $return );
		return $return;
	}
	public function get_raw($default = null){
		return get_option($this->id);
	}
	// public function get_value($default = null){
	// 	if ($default === null)
	// 		$default = $this->default;
	// 	if( ! $this->value){
	// 		$value = get_option($this->id, $default);
	// 		if ($value)
	// 			$this->value = unserialize( $value );
	// 		else
	// 			$this->value = array();
			
	// 	}

	// 	return $this->value;
	// }
	public function output_filter($value){
		if ($value){
			return maybe_unserialize($value);
		} else {
			return array();
		}
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$multiple = "";
		// print_r($this->get_value());
		// var_dump($this->choices);
		switch($this->ui) {
			case "multiple" :
				$multiple = $this->multiple_html();
			case "select" :
				$html .= "<select id='{$id}' class='{$classes}' {$multiple} {$required} type='url' name='{$name}[]'>";
				foreach ($this->choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$selected = $this->selected_html($val);
					$html .= "<option $selected value='{$val}'>{$label}</option>";
				}
				$html .= "</select>";
				break;
			case "checkboxes" :
				$html .= "<div class='{$classes} checkboxes'>";
				foreach ($this->choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$checked = $this->checked_html($val);
					$choiceID = $this->id . "-" . $this->get_choice_slug($label);
					$html .= '<div class="checkbox-set">';
					$html .= "<input id='{$choiceID}' {$checked} type='checkbox' name='{$name}[]' value='{$val}' />";
					$html .= "<label for='{$choiceID}'>$label</label>";
					$html .= '</div>';
				}
				$html .= "</div>";
				break;
			case "radio" :
				$html .= "<div class='{$classes} radios'>";
				foreach ($this->choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$checked = $this->checked_html($val);
					$choiceID = $this->id . "-" . $this->get_choice_slug($label);
					$html .= "<label for='{$choiceID}'>$label</label>";
					$html .= "<input id='{$choiceID}' {$checked} type='radio' name='{$name}[]' value='{$val}' />";
				}
				$html .= "</div>";
				break;
			default :
				$this->_error("No ui specified, or ui did not match one of the built in options. Please specify a valid UI type when creating an array type option.");
				return "";
				break;
		}
		return $html;
	}
	public function is_valid_choice_value($val){
		foreach ($this->choices as $choice){
			if ( $choice["value"] == $val )
				return true;
		}
		return false;
	}
	public function validate($newValue = ""){
		$valid = true;
		$confirmedValues = array();
		if ( is_array($newValue) ){
			if ( $this->ui == "multiple" || $this->ui == "checkboxes" ){
				// mulitiple uptions allowed
				foreach ($newValue as $val){
					if ( $this->is_valid_choice_value( $val ) ){
						$confirmedValues[] = $val;
					} else {
						$valid = false;
					}
				}
			//otherwise, multiple values are not allowed
			} else {
				// just use the first option.
				$confirmedValues[] = $newValue[0]; 
				$this->logger->_log("The array type LavaOption $this->name received multiple values to save the ui should not allow it. Refferring to first option instead, but this could represent a problem or the form was submitted falsely.");
			}
		} else {
			$valid = false;
			$this->logger->_log("The array type LavaOption $this->name received something other than an array when validating. This is an error with the plugin and should not happen.");
			$newValue = strval($newValue);
			if ( $this->is_valid_choice_value($newValue) )
				$confirmedValues = $newValue;
		}
		// if not valid, dont' worry now just return validated value
		$values = serialize($confirmedValues);
		return $values;
	}
}