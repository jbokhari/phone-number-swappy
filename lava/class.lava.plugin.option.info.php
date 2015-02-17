<?php 
/**
 * Lava option info Object
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */
final class SwappyOption_info extends SwappyOption {
	public $element = "p";
	public $possibletags = array( "h1", "h2", "h3", "h4", "h5", "h6", "label", "p" );
	public function init_tasks($options){
		if ( isset( $options['element'] ) && in_array($options['element'], $this->possibletags ) ){
			$this->element = $options['element'];
		}
	}
	/**
	 * Generates and returns option label html
	 * @return string
	 */
	public function get_option_label_html(){
		$html = "";
		$classes = $this->get_label_html_classes();
		$required = $this->required ? "*" : "";
		$html .= "<{$this->element} class='$classes' for='{$this->id}'>{$this->label}{$required}</{$this->element}>";
		return $html;
	}
	public function get_option_field_html(){
		return;
	}
	public function validate($newValue = null){
		return;
	}
	public function set_value($newValue = ""){
		return;
	}
	public function output_filter($output){
		return;
	}
	
}