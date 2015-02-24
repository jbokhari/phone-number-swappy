<?php
/**
 * Plugin core defines several methods for Lava Plugins
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL2
 * Last updated 7/20/2014
 */
if (!class_exists('PhoneNumberSwappyCore')) :

	class PhoneNumberSwappyCore {
		public $optionspage = array();
		public $options = array();
		public $cache = array();
		public $useAdminCss = false;
		public $useAdminJs = false;
		public $useFrontendCss = false;
		public $useFrontendJs = false;
		public $newStatic;
		public $newDynamic;
		public $tabs;
		public $dir;
		public $jsdir;
		public $cssdir;
		public $admin_message = "";
		private $current_tab = 0;
		private static $fieldnumber = 0;

		protected $option_prefix;

		public function __construct($optionfactory, $loggingobject, $notifierobject, $scriptmgmt, $metabox){

			$this->factory = $optionfactory;
			$this->logger = $loggingobject;
			$this->notifier = $notifierobject;
			$this->scriptmgmt = $scriptmgmt;
			// $this->metabox = $metabox;
			$this->jsvars = new SwappyJavaScriptGlobal;
			$this->set_default_js_values();

			$this->set_options();

			$this->dir = plugin_dir_url( __FILE__ );
			$this->cssdir = $this->dir . '../library/css/';
			$this->jsdir = $this->dir . '../library/js/';
			//TODO add activation and deactivation hooks

			add_action( 'admin_menu', array($this, 'add_admin_page_to_menu') );	
			add_action( 'admin_head', array($this, "save_admin") );
			$this->init();
			add_action( 'current_screen', array( $this, "get_current_screen" ) );
			add_action( 'current_screen', array( $this, "get_current_tab" ) );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );

		}
		public function set_default_js_values(){
			$this->jsvars->add_value("__prefix", PhoneNumberSwappy::$prefix );
			$this->jsvars->add_value("__name", PhoneNumberSwappy::$name );
		}
		public function meta_boxes($cpt){
			$this->metabox->set_post_type($cpt); 
			foreach( $this->options as $o ){
				if ( $o->meta_box ){
					// var_dump( $o );
					$this->metabox->add_option($o);
				}
			}
		}
		public function get_current_screen(){
			$this->current_screen = get_current_screen();
		}
		public function get_current_tab(){
			if ( isset( $_GET['tab'] ) && $this->is_options_page() ){
				$this->current_tab = $_GET['tab'];
			}
		}
		public function enqueue_admin_scripts(){
			global $pagenow;
			if ( $this->is_options_page() ){
				$this->scriptmgmt->the_scripts_by_tab( $this->current_tab );
				$this->admin_enqueue_scripts_and_styles();
			}
			if (is_admin() && in_array( $pagenow, array('post-new.php', 'post.php', 'post' ) ) ){
				$this->scriptmgmt->the_scripts_for_post_type();
			}
		}
		public function init(){
			return;
		}

		public function prefix($option){
			$option = $this->option_prefix . $option;
			return $option;
		}

		public function set_options(){
			require_once( plugin_dir_path(__FILE__) . '/../settings.php' );
			$this->static = $static;
			foreach( $dynamic as $option ) {
				$name = $option['name'];
				$this->options[$name] = $this->factory->create($this->option_prefix, $option, $this->scriptmgmt, $this->jsvars );
			}
		}
		public function do_tabs($current){
		    echo '<div id="icon-themes" class="icon32"><br /></div>';
		    echo '<h2 class="nav-tab-wrapper">';
			$plugin_tabs = $this->static['tabs'];
			$menu_slug = $this->static['options_page']['menu_slug'];
		    $tabindex = 0;
		    foreach( $plugin_tabs as $tabslug => $values ){
		    	$label = $values['label'];
		        $class = ( $tabindex == $current ) ? 'nav-tab-active' : '';
		        echo "<a class='nav-tab $class' href='options-general.php?page={$menu_slug}&tab=$tabindex'>$label</a>";
		        $tabindex++;
		    }
		    echo '</h2>';
		}
		public function set_frontend_loc_js_values(){
			$include = array();
			$include['plugin_prefix'] = $this->option_prefix;
			$include['plugin_version'] = $this->version;

			foreach($this->options as $option){

				if ( isset( $option->in_js ) && $option->in_js == true ){
					$value = $option->get_value();
					$include[$name] = $value;
				}

			}	
			return $include;
		}
		/**
		 * Gets the current version of the plugin. If the plugin has an option named debug and if it's set to anything that returns true, the plugin will generate a rand unique ID for version. This can be used to always refresh scripts.
		 * @return (string) plugin verion or random string
		 */
		public function get_script_version(){
			$option = $this->prefix('debug');
			if (get_option( $option, $default = false )){
				return uniqid();
			} else {
				return $this->ver;
			}
		}
		/**
		 * Loops through each registered option
		 * @param (int) $current_tab 
		 * @return type
		 */
		public function update_admin_options($current_tab = null){
			$msg = '';
			$affected = 0;
			extract($_POST);
			// print_r($_POST);
			foreach ($this->options as $option) {
				$name = $option->name;
				$this->logger->_log("Inside loop to save $option->name...");
				if ( $option->type == 'info' ) {
					$this->logger->_log(PhoneNumberSwappy::$name . " was a info field, skipping save function.");
					continue;
				}
				if ($option->tab != $current_tab){
					$this->logger->_log(PhoneNumberSwappy::$name . " is not member of current tab $current_tab, skipping save function.");
					continue;
				}
				if ( $option->in_menu == false ){
					$this->logger->_log(PhoneNumberSwappy::$name  . " is not in_menu, skipping save function.");
				}
				// $this->logger->_log(print_r($option, true));
				$this->logger->_log("Current Tab: $current_tab");
				// rather than check if the value is bool, we'll just assume that when we get this far, if the post data is missing, the value is false;
				$newValue = isset($$name) ? $$name : "";
				if ( $option->in_menu ){
					// $this->logger->_log("set_value is being run. {$newValue}");
					if ($option->set_value($newValue) )
						$affected++;
				}
			}
			if( $affected > 0 ){
				// wp_redirect( $_SERVER['REQUEST_URI'] );
				$this->notifier->add( "Options have been saved.", "updated" );
				// exit;
			} else {
				$this->notifier->add( "No options were changed!", "notice" );
			}
		}
		public function is_custom_post_type_add_or_edit_page(){	
			if( $this->current_screen == null ){
			// 	throw new Exception("settings_page not yet registered. You probably used is_options_page() too early.");
			}
			return ( $this->current_screen->post_type == $this->options['cpt_name']->get_value() &&  in_array($this->current_screen->base, array("post") ) );
		}
		public function is_custom_post_type_page(){	
			if( $this->current_screen == null ){
			// 	throw new Exception("settings_page not yet registered. You probably used is_options_page() too early.");
			}
			return ( $this->current_screen->post_type == $this->options['cpt_name']->get_value() );
		}
		public function is_options_page(){	
			if( $this->current_screen == null ){
				throw new Exception("settings_page not yet registered. You probably used is_options_page() too early.");
			}
			return ( $this->current_screen->id == $this->settings_page );
		}
		/**
		 * Determines whether or not to save plugin settings. Checks if save_post variable is set by hidden field in this plugin, verifies nonce and user permission. If all passes, runs update_admin_options()
		 * @uses self::update_admin_options()
		 * @return (string) A message based on actions performed (or not performed).
		 */
		public function save_admin(){
			if ( $this->is_options_page() ){

				$savepost = PhoneNumberSwappy::$prefix . "save_post";
				if( isset( $_POST[$savepost] ) ) :
					$noncename = PhoneNumberSwappy::$prefix . 'nonce';
					$nonceaction = PhoneNumberSwappy::$prefix . 'do_save_nonce';
					$nonce = ( isset( $_POST[$noncename] ) ) ? $_POST[$noncename] : '' ;
					$current_tab = ( isset( $_POST['tab'] ) ? $_POST['tab'] : null );
					if ( wp_verify_nonce( $nonce, $nonceaction ) ){
						$this->logger->_log("Nonce verified for saving options.");
					 	if ( current_user_can( 'manage_options' ) ){
					 		$this->logger->_log("User verified for saving options.");
							$this->update_admin_options($current_tab);
						} else {
							$this->notifier->add( "You lack permission to modify these settings.", "error" );
						}
					} else {
						$this->notifier->add( "There was an error with the nonce field. Please try again.", "error" );
					}
				endif;
			}
		}
		/**
		 * Display admin page by printing html to page.
		 * @return void
		 */
		public function display_admin_page(){
			echo "<div class='wrap " . PhoneNumberSwappy::$prefix . "options-page " . PhoneNumberSwappy::$prefix . "wrap'>";
			// $msg = $this->save_admin();
			$current_tab = ( isset( $_GET['tab'] ) ) ? intval( $_GET['tab'] ) : 0 ;
			$msg = '';
			$noncename = PhoneNumberSwappy::$prefix . 'nonce';
			$nonceaction = PhoneNumberSwappy::$prefix . 'do_save_nonce';
			// $msg = $this->save_admin();
			echo "<h2 class='" . PhoneNumberSwappy::$prefix . "option-page-title'>Plugin Options</h2>";

			$this->do_tabs($current_tab);

			echo "<form action='' method='post'>";
			$this->generate_option_fields($current_tab);
			$key = intval($current_tab);
			$tabvals = $this->static['tabs'][$current_tab];
			$hidesave = ( isset($tabvals['informational']) ) ? $tabvals['informational'] : false;
			if ( ! $hidesave ){
				$savepost = PhoneNumberSwappy::$prefix . "save_post";
				echo "<input type='hidden' name='{$savepost}' value='1' />";
				echo "<input type='hidden' name='tab' value='{$current_tab}' />";
				wp_nonce_field( $nonceaction, $noncename );
				echo "<button class='button button-primary " . PhoneNumberSwappy::$prefix . "plugin-save-btn' type='submit'>Save Options</button>";
			}
			if (isset($_GET['debug']))
				$this->debug_info();
			echo "</form>";
			echo "</div><!-- EOF WRAP -->";
		}
		public function debug_info(){
			if ($this->logger){
				$this->logger->display_errors();
				foreach($this->options as $option){
					if ($option->logger){
						$option->logger->display_logs();
						$option->logger->display_errors();
					}
				}
			}
		}
		public function generate_option_fields($tab){
			foreach ($this->options as $option) {
				if( $option->tab != $tab ){
					continue;
				}
				// $this->fieldnumber++;
				// @uses static int $this->fieldnumber starting at 1
				if ( isset( $option->in_menu ) && $option->in_menu ){
					echo $option->get_option_header_html();
					echo $option->get_option_label_html();
					echo $option->get_option_field_html();
					echo $option->get_option_description_html();
					echo $option->get_option_footer_html();
				
				}
			}
		}
		/**
		 * Register the admin page
		 * @return type
		 */
		public function add_admin_page_to_menu(){
			
			$function = array($this, 'display_admin_page');
			$admin_page = $this->static['options_page'];
			$this->settings_page = add_submenu_page( $admin_page['parent_slug'], $admin_page['page_title'], $admin_page['menu_title'], $admin_page['capability'], $admin_page['menu_slug'], $function );
		}
		/**
		 * Creates information field that takes no input
		 * @param type $label 
		 * @param type $id 
		 * @param type $value 
		 * @param type $description 
		 * @return string
		 */
		public function info_field($label, $id, $value, $description){
			$html = "";
			if ( $label != '' ){
				$label = "<h2 id='{$id}'>$label</h2>";
			}
			$html .= $label;
			$html .= "<p>$value</p>";
			$html .= "<p class='description'>$description</p>";

			return $html;
		}
		public function frontend_enqueue_scripts_and_styles(){
			$version = $this->get_script_version();
			if ( $this->useFrontendCss ){
				wp_enqueue_style( 'lavafrontendcss', $this->cssdir . 'css/frontend.css', array(), $version, $media = 'all' );
			}

			if ( $this->useFrontendJs ){
				wp_register_script( 'lavafrontendjs', $this->jsdir . 'frontend.js', 'jquery', $version );

				$js_global = $this->get_localized_js_object_name();

				wp_localize_script( 
					'lavafrontendjs',
					$js_global,
					$this->set_frontend_loc_js_values()
				);

				wp_enqueue_script('lavafrontendjs');
			}
		}
		public function get_localized_js_object_name(){
			if ( !empty($this->localize_object) ){
				return $this->localize_object;
			} else {
				return strtoupper( PhoneNumberSwappy::$prefix );
			}
		}
		public function admin_enqueue_scripts_and_styles(){
			$version = $this->get_script_version();
			$name = PhoneNumberSwappy::$name;
			$editscreen = ( $this->is_options_page() );
			if ( $this->is_options_page() ) wp_enqueue_media();
			if ( $this->useAdminCss && $editscreen ){
				wp_enqueue_style( 'lavaadmincss', $this->cssdir . 'admin.css', array(), $version, $media = 'all' );
			}
			if ( $this->useAdminJs && $editscreen ){
				wp_register_script( 'lavaadminjs', $this->jsdir . 'admin.js', 'jquery', $version );
				// $js_global = $this->get_localized_js_object_name();
				wp_localize_script( 'lavaadminjs', "LAVA", $this->jsvars->get_values() );
				wp_enqueue_script('lavaadminjs');
			}
		}
	} /* EOF class SwappyOptions */
endif;

/*EOF*/