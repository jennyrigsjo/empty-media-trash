<?php

/**
 * Fires when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    EMT
 */


// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'includes/class-emt-uninstaller.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-emt-options.php';

EMT_Uninstaller::uninstall();
