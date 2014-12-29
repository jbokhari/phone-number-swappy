<?php if ( ! defined( "ECT_RELATED_CONTENT_PATH" ) ) {
  wp_die();
}

$dynamic = array( //array of available options

	/**************************************
	::::::::::::::::EXTERNAL:::::::::::::::
	**************************************/
	array(
	      "name" => "phoneNumber1",
	      "label" => "Default Phone Number 1",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
  	array(
	      "name" => "swappyNumber1",
	      "label" => "Referral Phone Number 1 (Twilio)",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "phoneNumber2",
	      "label" => "Default Phone Number 2",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "swappyNumber2",
	      "label" => "Referral Phone Number 2 (Twilio)",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "phoneNumber3",
	      "label" => "Default Phone Number 3",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "swappyNumber3",
	      "label" => "Referral Phone Number 3 (Twilio)",
	      "type" => "str",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "jsTarget1",
	      "label" => "Javascript Target 1",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => ".phone-swap-1",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "jsTarget2",
	      "label" => "Javascript Target 2",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => ".phone-swap-2",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "jsTarget3",
	      "label" => "Javascript Target 3",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => ".phone-swap-3",
	      "tab" => 0,
	      "in_js" => true
	      ),
	array(
	      "name" => "use_get_var",
	      "label" => "Use Get Var",
	      "description" => "Use Get Value instead of search engine referral?",
	      "type" => "bool",
	      "default" => false,
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
	      "label" => "Swappy Reset Link, e.g. example.com?swappy_reset",
	      "description" => "(get var, e.g. example.com?swappy_reset)",
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