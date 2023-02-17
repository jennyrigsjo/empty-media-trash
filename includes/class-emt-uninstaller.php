<?php

/**
 * Uninstall class.
 *
 * Defines code to run during plugin removal/uninstallation.
 *
 * Defines methods to remove all data created by the plugin, including plugin-specific options and hooks.
 *
 * @since      1.0.0
 * @package    EMT/includes
 */
class EMT_Uninstaller {

	public static function uninstall() {

		if ( !current_user_can('install_plugins') ) {
			return;
		}

		self::delete_options();
		self::remove_hooks();

	}


	private static function delete_options() {

		$options = array_keys(EMT_Options::options_default());

		foreach ($options as $option) {
			delete_option( $option );
		}
	}


	private static function remove_hooks() {

		$hooks = EMT_Options::hooks();

		foreach ($hooks as $hook) {

			$time_of_next_run = wp_next_scheduled( $hook );

			if ( $time_of_next_run ) {
				wp_clear_scheduled_hook($hook);
			}
		}

	}

}
