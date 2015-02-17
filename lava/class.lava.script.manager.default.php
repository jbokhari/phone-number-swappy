<?php
final class SwappyScriptManagerDefault {
	public $css_source = array();
	public $script_source = array();
	public function finish(){
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}
	public function the_scripts_for_post_type($post_type = null){
		$args = array( "post_type" => $post_type );
		$this->enqueue_scripts($args);
		$this->enqueue_styles($args);
	}
	public function the_scripts_by_tab( $tab = null ){
		// TODO : Finish tab implementation
		$args = array( "tab" => $tab );
		$this->enqueue_scripts( $args );
		$this->enqueue_styles( $args );
	}
	final public function enqueue_scripts(array $args){
		// TODO: make $args operate so that scripts only run if needed on tab
		// TODO: Also allows other agruments to work via $args
		if ( isset($this->script_source) && $this->script_source != "" ){

			if (is_array( $this->script_source ) ){

				foreach ($this->script_source as $s => $args ) {
					if ( !isset( $args['handle'] ) ){
						throw new Exception("No handle set in script for {$this->name}. The script will not be loaded.");
						continue;
					} else {
						$handle = $args['handle'];
					}
					$source = isset( $args['source'] ) ? $args['source'] : false;
					$dependencies = empty( $args['dependencies'] ) ? array() : $args['dependencies'];
					if ( ! is_array( $dependencies ) ){
						$dependencies = array( $dependencies );
					}
					$version = empty( $args['version'] ) ? false : $args['version'];
					$in_footer = empty( $args['in_footer'] ) ? false : $args['in_footer'];

					wp_enqueue_script( $handle, $source, $dependencies, $version, $in_footer);
					
				}

			} else {
				throw new Exception("Property script_source was not an array for {$this->name}.");
			}
		}
	}
	final public function enqueue_styles(array $args){
		if ( isset($this->css_source) && $this->css_source != "" ){
			if (is_array( $this->css_source ) ){
				foreach ($this->css_source as $s => $args ) {
					if ( !isset( $args['handle'] ) ){
						throw new Exception("No handle set in script for {$this->name}. The script will not be loaded.");
						continue;
					}
					if ( !isset( $args['source'] ) ){
						throw new Exception("No source set in script for {$this->name}. The script will not be loaded.");
						continue;
					}
					$args['dependencies'] = empty( $args['dependencies'] ) ? array() : $args['dependencies'];
					if ( ! is_array( $args['dependencies'] ) ){
						$args['dependencies'] = array( $args['dependencies'] );
					}
					$args['version'] = empty( $args['version'] ) ? false : $args['version'];
					$args['media'] = empty( $args['media'] ) ? "screen" : $args['media'];
					$args['in_footer'] = empty( $args['in_footer'] ) ? false : $args['in_footer'];
					wp_enqueue_style( $args['handle'], $args['source'], $args['dependencies'], $args['version'], $args['in_footer']);
				}
			} else {
				throw new Exception("Property script_source not an array for {$this->name}.");
			}
		}
	}
	public function register_local_script($handle, $src, array $dependencies ){
		$this->add_script( array('handle' => $handle, 'source' => PNS_URL . "lava/options/js/" . $src, $dependencies ) );
	}
	public function add_script(array $args){
		$this->script_source[] = $args;
	}
	public function add_style(array $args){
		$this->css_source[] = $args;
	}
}