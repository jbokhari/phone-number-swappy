<?php
/* Bones Custom Post Type Example
This page walks you through creating 
a custom post type and taxonomies. You
can edit this one or copy the following code 
to create another one. 

I put this in a separate file so as to 
keep it organized. I find it easier to edit
and change things if they are concentrated
in their own file.

Developed by: Eddie Machado
URL: http://themble.com/bones/
*/
class SwappyCustomPostType{
	private $name = "";
	private $singular = "";
	private $plural = "";
	private $slug = "";
	private $hierachical = "";
	private $catname = "";
	private $taghierarchical = "";
	function __construct($name, $singular, $plural, $slug, $catname, $catslug, $taghierarchical){

		//get options first
		$this->name = $name;
		$this->singular = $singular;
		$this->plural = $plural;
		$this->slug = $slug;
		$this->catname = $catname;

		$this->catslug = $catslug;
		$this->taghierarchical = ( $taghierarchical[0] == "categories" ) ? true : false;
		add_action('init', array( $this, 'register_taxonomy' ) );
		add_action('init', array( $this, 'create_post_type' ) );

	}
	static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}
	public function create_post_type() { 
		$name = $this->name;
		$singular = $this->singular;
		$plural = $this->plural;
		$slug = $this->slug;
		register_post_type( $this->slug,
			array( 'labels' => array(
				'name' => $plural,
				'singular_name' => $singular,
				'all_items' => 'All ' . $plural,
				'add_new' => 'Add New', /* The add new menu item */
				'add_new_item' => 'Add New Custom Type',
				'edit' => 'Edit',
				'edit_item' => 'Edit ' . $plural,
				'new_item' => 'New ' . $singular,
				'view_item' => 'View ' . $singular,
				'search_items' => 'Search ' . $singular, 
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
				),
				'description' => "",
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'show_ui' => true,
				'query_var' => true,
				'menu_position' => 8,
				'menu_icon' => null,
				'rewrite'	=> array( 'slug' => $slug, 'with_front' => false ),
				'has_archive' => false,
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions')
			)
		);
		
	}
	public function register_taxonomy(){
		$slug = $this->slug;
		$catname = $this->catname;
		$catslug = $this->catslug;
		$taghierarchical = $this->taghierarchical;

		register_taxonomy( $catslug, 
			array($slug),
			array('hierarchical' => $taghierarchical,
				'labels' => array(
					'name' => $catname,
					'singular_name' => 'Custom Category',
					'search_items' =>  'Search Custom Categories',
					'all_items' => 'All Custom Categories',
					'parent_item' => 'Parent Custom Category',
					'parent_item_colon' => 'Parent Custom Category:',
					'edit_item' => 'Edit Custom Category',
					'update_item' => 'Update Custom Category',
					'add_new_item' => 'Add New Custom Category', 
					'new_item_name' => 'New Custom Category Name'
				),
				'show_admin_column' => true, 
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'custom-slug' ),
			)
		);
	}
}
?>