<?php
final class SwappyOptionrepeater extends SwappyOption {
	public $rows = 1;
	function init_tasks($options){
		if ( isset($options['fields']) && ! empty( $options['fields'] ) &&  is_array( $options['fields'] ) ){
			foreach ($options['fields'] as $f){
				//unsupported options currently
				$unsupported = array("repeater", "sortable", "bool", "array", "image", "repeater", "color");
				if (in_array($f['type'], $unsupported)){
					$this->_error("LavaPlugin error: the repeater field does not currently support elements of type {$f['type']}. Please remove this option in your settings file or change it to a supported element. Unsupported elements include: " . print_r($unsupported,true));
					return;
				}
				$f['id'] = $this->id . "_" . $f['name'] . "[]";
				$this->fields[] = LavaFactory::create("", $f );
			}
			$count = count($this->fields);
			if ($count < 7){
				$this->add_outer_class("col-1of{$count}");
				$this->column_width = $count;
			}
			else{	
				$this->_error("Too many sub fields assigned to option {$this->name}. Seven is the currently supported maximum.");
			}
		}
		echo "<pre>";
		// print_r($this);
		foreach($this->fields as $field){
			$f = $this->id . "_" . $field->name;
			// print_r( $f );
			// print_r( $_POST[$f] );
		}
		echo "</pre>";
	}
	public function get_option_field_html(){
		$html = "";
		$html .= "<div id='{$this->id}-fields' class='cf repeater-field-fields'>";
		$html .= "<div class='repeater-head'>";
		foreach ($this->fields as $f){
			$html .= $f->get_option_label_html();
		}
		$html .= "</div>";
		$html .= "<div class='repeater-row cf'>";
		for ($i = 0; $i < $this->rows; $i++) { 
			foreach ($this->fields as $f){
				$html .= $f->get_option_field_html();
			}
		}
		$html .= "</div>";//end row
		$html .= "</div>";//end container
		$html .= "<div class='cf button-container'>";
		$html .= "<button data-id='$this->id' class='repeater-add'>Add Fields</button>";
		$html .= "</div>";
		return $html;
	}
	public function get_single_instance_footer_scripts(){
		if ( ! empty( self::$single_instance_scripts[$this->ui] ) )
			return;
		self::$single_instance_scripts[$this->ui] = true;
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				return "lava.option.repeater.default.js";
				break;
		}
		return false; //default return false
	}
	public function validate($newValue = ""){
		foreach($this->fields as $field){
			
		}
	}
}