<?php

/**
 * Admin class.
 *
 * Defines the admin-specific functionality of the plugin.
 *
 * Defines methods responsible for setting up and displaying the administrative interface of the plugin, including methods to register settings and setting callbacks.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/admin
 */
class EMT_Admin {

	/**
	 * Initialize the class.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}


	/**
	 * Register plugin settings.
	 *
	 * @since	1.0.0
	 */
	public function register_settings() {

		if ( !current_user_can('manage_options') ) {
			return;
		}

		$settings_page = 'media';
		$section_name = 'emt-settings';

		$option_group = 'media';
		$option1_name = 'emt_delete_old';
		$option2_name = 'emt_delete_all';

		$option1_title = esc_html__('Delete trashed files older than:', 'empty-media-trash');
		$option1_explain = esc_html__('Individual files will be removed from trash after the specified number of days. Tip: Setting the value to 0 will deactivate the option.', 'empty-media-trash');
		$option1_tooltip = EMT_Admin_Functions::tooltip($option1_explain);

		$option2_title = esc_html__('Periodically remove all files from trash:', 'empty-media-trash');
		$option2_explain = esc_html__('All files will be removed from trash at the specified time interval, regardless of how long they have been in the trash. Tip: Selecting "Never" will deactivate the option.', 'empty-media-trash');
		$option2_tooltip = EMT_Admin_Functions::tooltip($option2_explain);

		register_setting(
			$option_group,
			$option1_name,
			array( $this, 'validate_delete_old_files' ),
		);

		register_setting(
			$option_group,
			$option2_name,
			array( $this, 'validate_delete_all_files' ),
		);

	    add_settings_section(
	        $section_name,
	        esc_html__('Delete trashed media files', 'empty-media-trash'), 
			array( $this, 'settings_description_html' ), 
	        $settings_page,
	    );

	    add_settings_field(
			$option1_name,
			$option1_title . ' ' . $option1_tooltip,
			array( $this, 'delete_old_files_html' ), 
	        $settings_page, 
	        $section_name, 
	    );

		add_settings_field(
			$option2_name,
			$option2_title . ' ' . $option2_tooltip,
			array( $this, 'delete_all_files_html' ),
			$settings_page,
			$section_name,
		);

	}


	/**
	 * Display admin notices.
	 *
	 * @since	1.0.0
	 */
	public function display_admin_notices() {

		global $pagenow;

		if ( $pagenow !== 'options-media.php' || !current_user_can('manage_options') ) {
			return;
		}

		if ( MEDIA_TRASH === false ) {

			$class = "notice notice-error";
			$message = esc_html__("The value of the WP constant MEDIA_TRASH is currently set to false. For the Empty Media Trash plugin to work, the value of MEDIA_TRASH must be set to true.", 'empty-media-trash');
			echo "<div class='$class'><p>$message</p></div>";
		}

		if ( 'DISABLE_WP_CRON' === true ) {

			$class = "notice notice-error";
			$message = esc_html__("The value of the WP constant DISABLE_WP_CRON is currently set to true. For the Empty Media Trash plugin to work, the value of DISABLE_WP_CRON must be set to false.", 'empty-media-trash');
			echo "<div class='$class'><p>$message</p></div>";
		}

		if ( EMPTY_TRASH_DAYS < 30 ) {

			$class = "notice notice-warning";
			$message = esc_html__("The WP constant EMPTY_TRASH_DAYS currently has a value lower than 30 (its default value). This means that all trashed files, including trashed media files, will be permanently deleted after the number of days specified by the value. Consider setting the value of EMPTY_TRASH_DAYS to 30 to avoid potential conflict with the Empty Media Trash plugin.", 'empty-media-trash');
			echo "<div class='$class'><p>$message</p></div>";
		}
	}


	/**
	* Add link/shortcut to plugin settings page.
	*
	* @since	1.0.0
	*/
	public function add_settings_shortcut ( $actions, $plugin_file ) {

		if ( $plugin_file !== 'empty-media-trash/empty-media-trash.php' ) {
			return $actions;
		}

		$anchor_link_id = 'emt-settings';
		$href = admin_url( "options-media.php#$anchor_link_id" );
		$text = esc_html__('Settings', 'empty-media-trash');

		$link = "<a href='$href'>$text</a>";
		$actions[] = $link;

		return $actions;
	}


	/**
	 * Include admin-specific CSS.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

	    if ( 'options-media.php' !== $hook_suffix ) {
	        return;
	    }

	    wp_enqueue_style( 'emt_admin_css', plugin_dir_url( __FILE__ ).'css/emt-admin.css' );
	}


	/**
	 * Validate setting value for 'delete old files'.
	 *
	 * @since	1.0.0
	 */
	private function validate_delete_old_files($days) {

		$days = sanitize_text_field( $days );
		$min_and_max = EMT_Options::get_option_allowed('emt_delete_old');
		extract($min_and_max);

		if ( intval($days) < $min || intval($days) > $max ) {
			$days = null;
		}

		return $days;

	}


	/**
	 * Validate setting value for 'delete all files'.
	 *
	 * @since	1.0.0
	 */
	private function validate_delete_all_files($schedule) {

		$schedule = sanitize_text_field( $schedule );
		$valid_schedules = EMT_Options::get_option_allowed('emt_delete_all');

		if ( !in_array($schedule, $valid_schedules) ) {
			$schedule = null;
		}

		return $schedule;

	}


	/**
	 * Print settings description.
	 *
	 * @since	1.0.0
	 */
	public function settings_description_html() {

		$description = esc_html__('Customize how long media files are left in the trash before they are permanently deleted', 'empty-media-trash');
		$anchor_link_id = 'emt-settings'; // Used to create settings shortcut on plugins page
		echo "<p id='$anchor_link_id'>$description.</p>";

	}


	/**
	 * Print markup for setting 'delete old files'.
	 *
	 * @since	1.0.0
	 */
	public function delete_old_files_html() {

		$option1_name = 'emt_delete_old';
		$current_value = EMT_Options::get_option_value($option1_name);

		$min_and_max = EMT_Options::get_option_allowed('emt_delete_old');
		extract($min_and_max);

		$days = esc_html__('days', 'empty-media-trash');

		echo "<input id='$option1_name' name='$option1_name' type='number' min='$min' max='$max' step='1' value='$current_value' required>";
		echo "<label for='$option1_name'> $days</label>";

		if (intval($current_value) > 0) {
			$description = esc_html__('Trashed files will be removed after', 'empty-media-trash');
			echo "<p class='emt_setting_description'>$description $current_value $days.</p>";
		} else {
			$description = esc_html__('Option deactivated.', 'empty-media-trash');
			echo "<p class='emt_setting_description'>$description</p>";
		}

	}


	/**
	 * Print markup for setting 'delete all files'.
	 *
	 * @since	1.0.0
	 */
	public function delete_all_files_html( ) {

		$option2_name = 'emt_delete_all';
		$current_value = EMT_Options::get_option_value($option2_name);
		$cron_schedules = EMT_Options::cron_schedules();

		echo "<select id='$option2_name' name='$option2_name'>";

		foreach ($cron_schedules as $value => $schedule) {
			$selected = selected( $value === $current_value, true, false );
			$label = $schedule['display'];
			echo "<option value='$value' $selected>$label</option>";
		}

		echo "</select>";

		$next_scheduled = wp_next_scheduled('emt_delete_all_files');

		if ( $next_scheduled ) {

			$description = esc_html__('All currently trashed files will be deleted:', 'empty-media-trash');

			$default_date_format = 'Y-m-d, H:i:s';
			$custom_date_format = apply_filters('emt_delete_all_files_date_format', $default_date_format);

			$next_run_date = date( $default_date_format, $next_scheduled );
			$next_run_localtime = get_date_from_gmt( $next_run_date, $custom_date_format );

			echo "<p class='emt_setting_description'>$description <b>$next_run_localtime</b></p>";

		} else {
			$description = esc_html__('Option deactivated.', 'empty-media-trash');
			echo "<p class='emt_setting_description'>$description</p>";
		}


	}

}
