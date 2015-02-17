<?php
/**
 * Plugin Name: Phone Number Swappy
 * Plugin URI: http://www.anchorwave.com
 * Description: Used to swap phone numbers
 * Version: 1.1.3
 * Author: Jameel Bokhari
 * Author URI: http://www.codeatlarge.com
 * License: GPL2
 */
/*
GitHub Plugin URI: https://github.com/jbokhari/phone-number-swappy
Copyright 2014  Jameel Bokhari  ( email : me@jameelbokhari.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


require_once( 'lava/class.lava.plugin.core.php' );
//logging/error class implementation
require_once "lava/interface/interface.lava.logger.php";
require_once "lava/interface/interface.lava.notifier.php";
//default class for debugging and error logging
require_once "lava/class.lava.configuration.php";
require_once "lava/class.lava.notifier.php";
require_once "lava/class.lava.logging.php";
//LavaFactory creates lavaoptions
require_once "lava/class.lava.factory.php";
// Load abstract LavaOption class extended by options
require_once "lava/class.lava.plugin.options.php";
//script manager for options
require_once "lava/class.lava.script.manager.default.php";

//class to create page templates
require_once "lava/class.lava.templatefactory.php";
//class to create custom post types
require_once "lava/class.lava.customposttype.php";

//class to create meta boxes
require_once "lava/class.lava.metabox.factory.php";

//class to store javascript globals
require_once "lava/class.lava.javascriptglobal.php";


define("PNS_PATH", dirname(__FILE__));
define("PNS_URL", plugin_dir_url( __FILE__ ) );
require_once(PNS_PATH . '/update.php');
if (is_admin()) {
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'phone-number-swappy', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/jbokhari/phone-number-swappy', // the GitHub API url of your GitHub repo
        'raw_url' => 'https://raw.github.com/jbokhari/phone-number-swappy/master', // the GitHub raw url of your GitHub repo
        'github_url' => 'https://github.com/jbokhari/phone-number-swappy', // the GitHub url of your GitHub repo
        'zip_url' => 'https://github.com/jbokhari/phone-number-swappy/zipball/master', // the zip url of the GitHub repo
        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '4.0', // which version of WordPress does your plugin require?
        'tested' => '4.1', // which version of WordPress is your plugin tested up to?
        'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
    );
    $githubupdater = new WP_GitHub_Updater($config);
}
require_once('lava/class.lava.plugin.core.php');
/**
 * Class LavaPlugin
 * @uses LavaCorePlugin Version 2.2
 * @package ECT Related Content
 */
class PhoneNumberSwappy extends PhoneNumberSwappyCore {
	static $prefix = 'pns_';
	static $ver = '1.1.3';
	static $name = 'pns';
	public $option_prefix = 'pns_';
	public $localize_object = 'PNS';
	protected $plugin_slug;
	private static $instance;
	protected $templates;
	public function init(){
		$this->useFrontendCss = false;
		$this->useFrontendJs = false;
		$this->useAdminCss = true;
		$this->useAdminJs = true;
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array($this, 'add_settings_page') );
		$this->appendJS();
	}
	
	public static function get_instance() {
		if (null == self::$instance ) {
			self::$instance = new PhoneNumberSwappy();
		}
		return self::$instance;
	}
	/**
	 * Overrides default functionality
	 * @return type
	 */
	function add_settings_page($links) { 
	  $settings_link = '<a href="'.$this->static['options_page']['parent_slug'].'?page='.$this->static['options_page']['menu_slug'].'">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	function swappy_header_stuff(){
		// if (is_user_logged_in())
		// 	return;
		/*
		 * $sereferral (bool) used to track whether this is a refrral or not 
		 */
		$sereferral = null;
		/**
		 * $phoneNumbers (array) used to store/display phone numbers
		 */
		// $phoneNumbers;
		//if cookie is not set
		$cookieName = $this->prefix . "referral";
		$use_get_var = $this->options['use_get_var']->get_value();
		$get_tracking_var = $this->options['get_tracking_var']->get_value();
		$swappy_reset_link = $this->options['swappy_reset_link']->get_value();
		
		
		if ( isset( $_GET['swappy_cookie_reset'] ) && $_GET['swappy_cookie_reset'] == '1' ){
			setcookie( $cookieName, "", time()-3600);
			return;
		}

		/**
		 * TODO FIX THIS LOGIC TO INCORP BOTH, GETVAR OR SEARCH ENGINE TRAFFIC
		 */
		if ( ! isset( $_COOKIE[$cookieName] ) ){

		    if( isset( $_SERVER['HTTP_REFERER']) ) {
		        // if so parse url...
		        $ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		        // and check if referral from google, yahoo, bing
		        if( strpos( $ref, "google.com" ) !== false || strpos( $ref, "yahoo.com" ) !== false || strpos( $ref, "bing.com" ) !== false ) { 
		            // is a referral
		            $sereferral = true;
		            $this->set_referral_cookie(true);
		        } else {
		            // doesn't look like it was a referrral
		            $sereferral = false;
		        	$this->set_referral_cookie(false);

		        }
		    // default to non referral if var is unvailable
		    } else if ( $use_get_var == "true" && isset( $_GET[ $get_tracking_var ] ) ){
		    	$sereferral = true;
		        $this->set_referral_cookie(true);

		    } else {
		        $sereferral = false;
		        $this->set_referral_cookie(false);

		    }
		} else {
		    //otherwise consult the almighty cookie
		    if ( $_COOKIE[ $cookieName ] == "true" ){
		        $sereferral = true;
		    } else {
		        $sereferral = false;
		    }
		    if ( $_COOKIE[ $cookieName ] == "true" ){
		        $sereferral = true;
		    } else {
		        $sereferral = false;
		    }
		}
		$this->referral= $sereferral;

	}
	/**
	 * Set Referral Cookie - accepts bool true of false and does the rest, ie gets domain, cookie name, path and expiration based on settings.
	 * @param bool $val, converts into string to save as cookie value
	 * @return void
	 */
	function set_referral_cookie($val){

		$cookieName = $this->prefix . "referral";
		$domain = $this->options['domain']->get_value();
		$path = $this->options['path']->get_value();
		$days = $this->options['cookie_length']->get_value();
		$time = time() + ( 60 * 60 * 24 * $days );

		if ($val === true){
			$cookieval = "true";
		} else {
			$cookieval = "false";
		}

		if ( $domain == '' ){
			$domain = null;
		}
		$this->_log("Path is set to $path");
 		setcookie( $cookieName, $cookieval, $time, $path, $domain );

	}
	function is_referral(){
		if ( ! isset( $this->referral ) || empty( $this->referral ) )
			return false;
		else
			return $this->referral;
	}
	function get_numbers(){
		if ( isset($this->numbers) || ! empty($this->numbers) )
			return;
		$phoneNumbers = array();
		if ( $this->is_referral() ){
		    $phoneNumbers[0] = $this->options["swappyNumber1"]->get_value();
		    $phoneNumbers[1] = $this->options["swappyNumber2"]->get_value();
		    $phoneNumbers[2] = $this->options["swappyNumber3"]->get_value();
		} else {    
		    $phoneNumbers[0] = $this->options["phoneNumber1"]->get_value();
		    $phoneNumbers[1] = $this->options["phoneNumber2"]->get_value();
		    $phoneNumbers[2] = $this->options["phoneNumber3"]->get_value();
		}
		$this->numbers = $phoneNumbers;
	}	
	function swappyNumber1(){
		return $this->numbers[0];
	}
	function swappyNumber2(){
		return $this->numbers[1];
	}
	function swappyNumber3(){
		return $this->numbers[2];
	}
	function appendJS(){
		$infooter = $this->options['infooter']->get_value() == "true" ? true : false;
		wp_register_script( 'phone_number_swappy_javascript', $this->jsdir . 'frontend.js', array( 'jquery' ), $this->ver, $infooter );
		//makes sure numbers are set
		
		// print_r($this);

		add_action( 'wp_enqueue_scripts', array( $this, 'enque_phone_number_swappy_javascript' ) );
		
	}
	function enque_phone_number_swappy_javascript(){
		$this->get_numbers();
		$phoneNumbers = $this->numbers;
		
		$jsTarget1 = $this->options["jsTarget1"]->get_value();
		$jsTarget2 = $this->options["jsTarget2"]->get_value();
		$jsTarget3 = $this->options["jsTarget3"]->get_value();

		$jsvars = array(
			"jsTarget1" => $jsTarget1,
			"jsTarget2" => $jsTarget2,
			"jsTarget3" => $jsTarget3,
			"phoneNumbers" => $phoneNumbers
		);
		wp_localize_script( 'phone_number_swappy_javascript', $this->localize_object, $jsvars );
		wp_enqueue_script( 'phone_number_swappy_javascript' );
	}
}


function activation_test() {
	if ( ! get_option("PhoneNumberSwappyVersion") ) {
		update_option( "PhoneNumberSwappyVersion", PhoneNumberSwappy::$ver );
		if ( get_option( "pns_use_get_var" ) == "true" ){
			update_option( "pns_use_get_var", array( "getvar" ) );
		}
	}
}
// register_activation_hook( __FILE__, 'phone_number_swappy_activate' );
add_action( 'plugins_loaded', 'activation_test' );

$optionfactory = new SwappyFactory();
$loggingobject = new SwappyLogging( PhoneNumberSwappy::$name );
$notifierobject = new SwappyNotifier( PhoneNumberSwappy::$prefix );
$scriptmgmt = new SwappyScriptManagerDefault();
$metabox = new SwappyMetaBoxFactory();
$pns = new PhoneNumberSwappy($optionfactory, $loggingobject, $notifierobject, $scriptmgmt, $metabox);

add_action("init", array( $pns, "swappy_header_stuff") );
// // add_action("wp_head", array( PhoneNumberSwappy::get_instance(), "appendJS") );
// add_shortcode("swappy1", array( PhoneNumberSwappy::get_instance(), "swappyNumber1") );
// add_shortcode("swappy2", array( PhoneNumberSwappy::get_instance(), "swappyNumber2") );
// add_shortcode("swappy3", array( PhoneNumberSwappy::get_instance(), "swappyNumber3") );