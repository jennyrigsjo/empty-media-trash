<?php

/*
Plugin Name:		Empty Media Trash
Description:		Plugin to customize the frequency with which the media library trash is emptied.
Author:				Jenny Rigsjö
Author URI:			https://jennyrigsjo.se
Tags:				media, image, trash, customize, empty
Version:			1.0.0
Requires PHP:		7.4
Requires at least:	6.1
Tested up to:		6.1
Text Domain:		empty-media-trash
Domain Path:		/languages
License:			GPL v2 or later
License URI:		https://www.gnu.org/licenses/gpl-2.0.txt
*/


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Plugin name.
 */
define( 'EMT_NAME', basename( __FILE__ ) );


/**
 * Current plugin version.
 */
define( 'EMT_VERSION', '1.0.0' );


require plugin_dir_path( __FILE__ ) . 'includes/class-emt.php';


function run_emt() {

	$plugin = new EMT();
	$plugin->run();

}

run_emt();
