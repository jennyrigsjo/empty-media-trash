<?php

/**
 * Options class.
 *
 * Defines default plugin options and settings.
 *
 * Defines default values for the plugin's options and settings, and methods to access those values.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/includes
 */
class EMT_Options {

	/**
	 * Return a list of the plugin's hooks.
	 *
	 * @return array
	 *
	 * @since	1.0.0
	 */
	public static function hooks() {

		return array(
			'emt_delete_old_files',
			'emt_delete_all_files',
		);

	}

	/**
	 * Return a list of the plugin's options with default values.
	 *
	 * @return array
	 *
	 * @since	1.0.0
	 */
	public static function options_default() {

		return array(
			'emt_delete_old' => 30,
			'emt_delete_all' => 'emt_never',
			'emt_initialized' => 'yes',
		);

	}


	/**
	 * Return a list of the plugin's options with their allowed values.
	 *
	 * @return array
	 *
	 * @uses EMT_Options::cron_schedules()
	 *
	 * @since	1.0.0
	 */
	public static function options_allowed() {

		return array(
			'emt_delete_old' => array(
				'min' => 0,
				'max' => 30,
			),
			'emt_delete_all' => array_keys( self::cron_schedules() ),
			'emt_initialized' => 'yes',
		);

	}


	/**
	 * Return a list of plugin-specific cron schedules.
	 *
	 * @return array A multidimensional array.
	 *
	 * @since	1.0.0
	 */
	public static function cron_schedules() {

		return array(
			'emt_never' => array(
				'interval' => YEAR_IN_SECONDS * 20,
				'display' => esc_html__('Never', 'empty-media-trash')
			),
			'emt_daily' => array(
				'interval' => DAY_IN_SECONDS,
				'display' => esc_html__('Daily', 'empty-media-trash')
			),
			'emt_weekly' => array(
				'interval' => WEEK_IN_SECONDS,
				'display' => esc_html__('Once a week', 'empty-media-trash')
			),
			'emt_2weeks' => array(
				'interval' => WEEK_IN_SECONDS * 2,
				'display' => esc_html__('Once every two weeks', 'empty-media-trash')
			),
			'emt_3weeks' => array(
				'interval' => WEEK_IN_SECONDS * 3,
				'display' => esc_html__('Once every three weeks', 'empty-media-trash')
			),
			'emt_4weeks' => array(
				'interval' => WEEK_IN_SECONDS * 4,
				'display' => esc_html__('Once every four weeks', 'empty-media-trash')
			),
		);

	}


	/**
	 * Get the default value of an option.
	 *
	 * @param string $option_name	The name of the option.
	 *
	 * @return mixed				The option's default value; null if the option does not exist.
	 *
	 * @uses EMT_Options::options_default
	 *
	 * @since	1.0.0
	 */
	public static function get_option_default($option_name) {

		$default_options = self::options_default();

		if ( isset($default_options[$option_name]) ) {
			return $default_options[$option_name];
		} else {
			return null;
		}

	}


	/**
	 * Get the allowed value(s) of an option.
	 *
	 * @param string $option_name	The name of the option.
	 *
	 * @return mixed				The option's allowed value(s); null if the option does not exist.
	 *
	 * @uses EMT_Options::options_alllowed
	 *
	 * @since	1.0.0
	 */
	public static function get_option_allowed($option_name) {

		$allowed_values = self::options_allowed();

		if ( isset($allowed_values[$option_name]) ) {
			return $allowed_values[$option_name];
		} else {
			return null;
		}

	}


	/**
	 * Get the current value of an option from the database.
	 *
	 * @param string $option_name	The name of the option.
	 *
	 * @return mixed				The current value of the option; default value if the option has no value stored in the database; null if the option does not exist.
	 *
	 * @uses EMT_Options::options_default
	 *
	 * @since	1.0.0
	 */
	public static function get_option_value($option_name) {

		$option = get_option( $option_name, self::options_default() );
		$value = is_array($option) && isset( $option[$option_name] ) ? sanitize_text_field( $option[$option_name] ) : sanitize_text_field( $option );
		return $value;

	}

}
