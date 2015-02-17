<?php

/** 
 * The Class.
 */
class SwappyMetaBoxFactory {

	private $post_type;
	private $options = array();
	private $meta_boxes = array();
	private static $meta_box_count;
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	public function set_post_type( $post_type ){
		$this->post_type = $post_type;
	}
	/**
	 * Add a valid LavaOption. This should happen before add_meta_boxes gets fired
	 **/
	public function add_option(LavaOption $option){
		if ( ! in_array( $option->meta_box["group"], $this->meta_boxes ) ){
			$this->meta_boxes[] = $option->meta_box["group"];
		}
		$this->options[] = $option;
	}
	private function meta_box_plus(){
		self::$meta_box_count++;
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
        $post_types = array();     //Add other post types here
        $post_types[] = $this->post_type;
        if ( in_array( $post_type, $post_types ) ) {
        	foreach($this->meta_boxes as $group ) {
				add_meta_box(
					'lava_meta_box' . self::$meta_box_count,
					__( $group, 'myplugin_textdomain' ),
					array( $this, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high',
					array( "group" => $group )
				);
		        $this->meta_box_plus();
        	}
        }
	}
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		// exit;
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['lava_meta_box_save'] ) )
			return $post_id;

		$nonce = $_POST['lava_meta_box_save'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'lava_save_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.

		// Update the meta field.
		// update_post_meta( $post_id, '_my_meta_value_key', $mydata );
		foreach( $this->options as $o ) {
			
			$o->set_post_id( $post_id );

			$o->set_value( $_POST[$o->name] );
		}
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post, $param ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'lava_save_box', 'lava_meta_box_save' );

		// Use get_post_meta to retrieve an existing value from the database.
		// $value = get_post_meta( $post->ID, '_my_meta_value_key', true );
		foreach( $this->options as $o ) {
			if ( $param['args']['group'] !== $o->meta_box['group'] )
				continue;
			$o->set_post_id( $post->ID );
			echo $o->get_option_header_html();
			echo $o->get_option_label_html();
			echo $o->get_option_field_html();
			echo $o->get_option_description_html();
			echo $o->get_option_footer_html();
		}
		// Display the form, using the current value.
	}
}