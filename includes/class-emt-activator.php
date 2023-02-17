<?php

/**
 * Activator class.
 *
 * Defines code to run during inital plugin activation.
 *
 * Defines methods to activate the plugin and add options with default values.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/includes
 */
class EMT_Activator {

	public function __construct() {

	}

	/**
	 * Activate plugin.
	 *
	 * @since    1.0.0
	 */
	public function activate_plugin() {

		$plugin_initialized = get_option('emt_initialized'); // get option directly from database

		if ( !current_user_can('activate_plugins') || $plugin_initialized === 'yes' ) {
			return;
		}

		$this->add_default_options();
	}


	/**
	 * Add options with default values to database.
	 *
	 * @since    1.0.0
	 */
	private function add_default_options() {

		$options = EMT_Options::options_default();

		foreach ($options as $option => $default_value) {
			add_option( $option, $default_value );
		}

	}

}
