<?php if ( ! defined( "ECT_RELATED_CONTENT_PATH" ) ) {
  wp_die();
}

$dynamic = array( //array of available options

	/**************************************
	::::::::::::::::EXTERNAL:::::::::::::::
	**************************************/
	array(
	      "name" => "cookie_length",
	      "label" => "Cookie Lifetime (in days)",
	      "type" => "int",
	      "rules" => array(
				"min" => 0,
				"step" => 1
			),
	      "default" => 7
	      ),
	array(
	      "name" => "phoneNumber1",
	      "label" => "Phone Number 1",
	      "type" => "str"
	      ),
  	array(
	      "name" => "swappyNumber1",
	      "label" => "Phone 1 Alt",
	      "type" => "str"
	      ),
	array(
	      "name" => "phoneNumber2",
	      "label" => "Phone Number 2",
	      "type" => "str"
	      ),
	array(
	      "name" => "swappyNumber2",
	      "label" => "Phone 2 Alt",
	      "type" => "str"
	      ),
	array(
	      "name" => "phoneNumber3",
	      "label" => "Phone Number 3",
	      "type" => "str"
	      ),
	array(
	      "name" => "swappyNumber3",
	      "label" => "Phone 3 Alt",
	      "type" => "str"
	      ),
	array(
	      "name" => "jsTarget1",
	      "label" => "Javascript Target 1",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => "#phone1"
	      ),
	array(
	      "name" => "jsTarget2",
	      "label" => "Javascript Target 2",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => "#phone2"
	      ),
	array(
	      "name" => "jsTarget3",
	      "label" => "Javascript Target 3",
	      "description" => "Span ID for first phone number",
	      "type" => "str",
	      "default" => "#phone3"
	      )

);

$static = array(
	'tabs' => array(
		0 => array(
			'label' => __('Phone Number Swappy Options', 'text_domain'),
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