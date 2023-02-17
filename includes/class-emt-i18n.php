<?php

/**
 * Internationalization class.
 *
 * Defines the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/includes
 */
class EMT_i18n {

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $text_domain   The name of the text domain of this plugin.
	 */
	private $text_domain;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $text_domain   The name of the text domain of this plugin.
	 */
	public function __construct( $text_domain ) {
		$this->text_domain = $text_domain;
	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$main_dir = plugin_dir_path( __DIR__ ); // should return path to main plugin directory
		load_plugin_textdomain( $this->text_domain, false, $main_dir . 'languages/' );

	}



}
