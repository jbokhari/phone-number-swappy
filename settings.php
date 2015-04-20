<?php if ( ! defined( "ABSPATH" ) ) {
  die();
}

$dynamic = array( //array of available options

	/**************************************
	::::::::::::::::EXTERNAL:::::::::::::::
	**************************************/
	/**
	 * @since 1.1.3
	 * Used in lieu of old string values
	 * Replacement for phoneNumber1, swappyNumber1, jsTarget1, phoneNumber2, ..., jsTarget3
	 * Now one repeater, also has note field for internal use
	 */
	array(
		"name" => "phone_numbers",
		"label" => "Phone Numbers",
		"type" => "repeater",
		"in_menu" => true,
		"fields" => array(
			array(
			 	"name" => "default_number",
			 	"label" => "Default Phone #",
			 	"type" => "str"
			),
			array(
			 	"name" => "replacement_number",
			 	"label" => "Referral Phone #",
			 	"type" => "str"
			),
			array(
			 	"name" => "js_target",
			 	"label" => "JavaScript Target",
			 	"type" => "str",
			 	"default" => ".phone-swap-"
			),
			array(
			 	"name" => "filter_option",
			 	"label" => "Filter Option",
			 	"type" => "str",
			 	"default" => ""
			),
			array(
			 	"name" => "notes",
			 	"label" => "Notes",
			 	"type" => "textarea"
			)
		)
	),
	/**
	 * @since 1.1.3
	 * Replaces old bool value with three different options
	 */
	array(
	      "name" => "use_get_var",
	      "label" => "Referral Tracking",
	      "description" => "Use Get Value instead of search engine referral?",
	      "type" => "array",
	      "ui" => "radio",
	      "choices" => array(
	      	array(
	      		"label" => "Track search engine traffic only (default)",
	      		"value" => "search"
	      		),
	      	array( 
	      		"label" => "Track url with GET var only",
	      		"value" => "getvar"
	      		),
	      	array(
	      		"label" => "Track both: GET var and search engine traffic",
	      		"value" => "both"
	      		)
	      	),
	      "default" => array("search"),
	      "tab" => 0
	      ),
	array(
	      "name" => "get_tracking_var",
	      "label" => "Get variable name for tracking",
	      "description" => "When Use Get Var is checked, referrals are set when this GET var is in the visited url. e.g. example.com/?ppc",
	      "type" => "str",
	      "default" => "ppc",
	      "tab" => 0
	      ),
	array(
	      "name" => "swappy_reset_link",
	      "label" => "Swappy Reset Link",
	      "description" => "e.g. if you set this to <code>swappy_reset</code>, visiting thiswebsite.com?swappy_reset will reset the cookie)",
	      "type" => "str",
	      "default" => "swappy_reset",
	      "tab" => 1
	      ),
	array(
	      "name" => "cookie_length",
	      "label" => "Cookie Lifetime (in days)",
	      "type" => "int",
	      "rules" => array(
				"min" => 0,
				"step" => 1
			),
	      "default" => 30,
	      "tab" => 1
	      ),
	array(
	      "name" => "path",
	      "label" => "Cookie Path",
	      "type" => "str",
	      "default" => '/',
	      "tab" => 1
	      ),
	array(
	      "name" => "domain",
	      "label" => "Cookie Domain",
	      "type" => "str",
	      "default" => '',
	      "tab" => 1
	      ),
	array(
	      "name" => "infooter",
	      "label" => "Append script? Script will be placed in footer instead of <code>head</code> element",
	      "type" => "bool",
	      "default" => false,
	      "tab" => 1
	      ),

);

$static = array(
	'tabs' => array(
		0 => array(
			'label' => __('Phone Number Swappy Options', 'text_domain'),
			'capability' => 'manage_options', //an idea, not in use
			'informational' => false
		),
		1 => array(
			'label' => __('Advanced', 'text_domain'),
			'capability' => 'manage_options', //an idea, not in use
			'informational' => false
		)
	),
	'options_page' =>	array(
		'parent_slug' => 'options-general.php',
		'page_title'  => 'Phone Number Swappy',
		'menu_title'  => 'Phone Number Swappy',
		'capability'  => 'manage_options',
		'menu_slug'   => 'phone-number-swappy'
	)
);
/* EOF */