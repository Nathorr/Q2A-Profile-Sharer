<?php

/*
	Plugin Name: Q2A Profile Sharer
	Plugin URI: 
	Plugin Description: Allows profiles to be shared in Facebook
	Plugin Version: 1.0
	Plugin Date: 2015-06-16
	Plugin Author: Nathorr
	Plugin Author URI:
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_module(
	'widget', // type of module
	'qa-profile-sharer-widget.php', // PHP file containing module class
	'qa_profile_sharer_widget', // module class name in that PHP file
	'Profile Share Button' // human-readable name of module
);

qa_register_plugin_module(
	'page',
	'qa-profile-sharer-page.php',
	'qa_profile_sharer_page',
	'Profile Share Page'
);