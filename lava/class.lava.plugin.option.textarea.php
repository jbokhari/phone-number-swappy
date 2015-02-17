<?php 
final class SwappyOption_textarea extends SwappyOption {
	private $mode;
	private $syntax;
	private $valid_themes = array("3024-day","3024-night","ambiance-mobile","ambiance","base16-dark","base16-light","blackboard","cobalt","eclipse","elegant","erlang-dark","lesser-dark","mbo","mdn-like","midnight","monokai","neat","neo","night","paraiso-dark","paraiso-light","pastel-on-dark","rubyblue","solarized","the-matrix","tomorrow-night-bright","tomorrow-night-eighties","twilight","vibrant-ink","xq-dark","xq-light","zenburn");
	private $valid_syntax = array(
		"css" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/css/css.min.js",
		"coffeescript" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/coffeescript/coffeescript.min.js",
		"htmlmixed" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/htmlmixed/htmlmixed.min.js",
		"javascript" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/javascript/javascript.min.js",
		"markdown" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/markdown/markdown.min.js",
		"php" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/php/php.min.js",
		"xml" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.11.0/mode/xml/xml.min.js"
	);
	protected function init_tasks( $options ){
		if ( $this->is_code_field() ){

			if ( isset($options['theme'] ) ){
				$this->set_theme( $options['theme'] );
			} else {
				$this->set_theme( "default" );
			}

			if ( isset($options['syntax'] ) ){
				$this->set_mode( $options['syntax'] );
			}
		}
		$class = "textarea-ui-" . $this->ui; 
		$this->add_class( $class );

		if ( !isset( $this->syntax ) || $this->syntax == "html" ){
			$this->set_mode( "htmlmixed" ); //default to htmlmixed, map html to htmlmixed
		} else if ( ! array_key_exists( $this->syntax, $this->valid_syntax) ){
			trigger_error("The sytax/mode " . $this->syntax . " is not supported on a textarea code ui element. Field defaults to html, but you should specify a valid syntax type.", E_USER_NOTICE );
			$this->set_mode( "htmlmixed" ); //default to htmlmixed, map html to htmlmixed
		}
		$class = "textarea-syntax-" . $this->syntax; 

		if ( $this->is_code_field() ){
			if ($this->theme !== "default" )
				$theme = array("handle" => "codemirror-theme-" . $this->theme . ".css", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.10.0/theme/" . $this->theme . ".min.css" );
			$modejs = array("handle" => "codemirror-mode-" . $this->get_mode(), "source" => $this->valid_syntax[$this->get_mode()] );
			$codemirrorcssargs = array("handle" => "codemirror-css", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.8.0/codemirror.css" );
			$jsargs = array("handle" => "codemirror-js", "source" => "https://cdnjs.cloudflare.com/ajax/libs/codemirror/4.8.0/codemirror.js" );
			$this->scriptmgmt->add_script( $jsargs );
			$this->scriptmgmt->add_script( $modejs );
			$this->scriptmgmt->add_style( $codemirrorcssargs );
			$this->scriptmgmt->add_style( $theme );
			$this->scriptmgmt->register_local_script('lava-option-textarea-code-js', 'lava.option.textarea.code.js', array( 'codemirror.js' ) );	
		}
	}
	private function set_theme($theme){
		$this->theme = $theme;
	}
	private function get_theme(){
		return $this->theme;
	}
	private function set_mode($syntax){
		$this->syntax = $syntax;
	}
	private function get_mode(){
		return $this->syntax;
	}
	private function make_data_html($name, $data){
		$html = '';
		$html .= 'data-';
		$html .= sanitize_title_with_dashes( $name );
		$html .= '="';
		$html .= esc_attr( $data );
		$html .= '"';
		return $html;
	}
	/**
	 * Checks that valid theme was provided, if not returns solarized as default.
	 **/
	public function is_valid_theme(){
		return in_array( $theme, $this->valid_themes );
	}
	/**
	 * Check to see if this is a code field, using one of the various code ui's. Add new ui's here.
	 **/
	public function is_code_field(){
		return ( $this->ui == "code" );
	}
	public function get_theme_html(){
		$html = "";
		if ( $this->is_code_field() && isset( $this->theme ) ){
			$html .= " data-codemirror-theme='{$this->theme}' ";
		}
		return $html;
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		// var_dump($value);
		// $value = esc_textarea($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$theme = $this->make_data_html("codemirror-theme", $this->get_theme() );
		$mode = $this->make_data_html("codemirror-mode", $this->get_mode() );
		return "<textarea id='{$id}' class='{$classes}' {$required} $theme $mode type='text' name='{$name}'/>{$value}</textarea>";
	}
	public function validate($newValue = ""){
		if ($this->ui == "code" && $this->syntax == "css"){
			//this seems to be the only way
			// @link http://wordpress.stackexchange.com/questions/53970/sanitize-user-entered-css
			include( LAVAPLUGINPATH . '/library/inc/csstidy/class.csstidy.php');
			$csstidy = new csstidy();
			$csstidy->set_cfg( 'remove_bslash', FALSE );
			$csstidy->set_cfg( 'compress_colors', FALSE );
			$csstidy->set_cfg( 'compress_font-weight', FALSE );
			$csstidy->set_cfg( 'discard_invalid_properties', FALSE );
			$csstidy->set_cfg( 'merge_selectors', FALSE );
			$csstidy->set_cfg( 'remove_last_;', FALSE );
			$csstidy->set_cfg( 'css_level', 'CSS3.0' );
			$csstidy->set_cfg( 'preserve_css', TRUE ); //preserve comments, hacks
			// $newValue = preg_replace("/\t/", '%%TAB%%', $newValue );
			// $newValue = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $newValue ) ) );
			// $newValue = str_replace('%%TAB%%', "\t", $newValue );
			$csstidy->parse( $newValue );
			$newValue = $csstidy->print->plain();
			// $newValue = stripcslashes($newValue);
			return $newValue;

		} else {
			//retain tabs
			$newValue = preg_replace("/\t/", '%%%TAB%%%', $newValue );
			$newValue = htmlentities( $newValue );
			//retain line breaks
			//this brilliant genius http://stackoverflow.com/questions/20444042/wordpress-how-to-sanitize-multi-line-text-from-a-textarea-without-losing-line0
			$newValue = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $newValue ) ) );
			//retain tabs pt 2
			$newValue = str_replace('%%%TAB%%%', "\t", $newValue );
			return $newValue;
		}
	}
	public function output_filter($output){
		if ($this->ui == "code" && $this->syntax == "css"){
			return stripcslashes($output);
		} else {	
			return stripcslashes( html_entity_decode( $output ) );
		}
	}
}
