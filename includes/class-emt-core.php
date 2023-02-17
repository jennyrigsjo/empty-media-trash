<?php

/**
 * Core class.
 *
 * Defines the plugin's core functionality.
 *
 * Defines methods to add custom cron schedules, methods to schedule and unschedule custom hooks, and methods to empty the media trash.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/includes
 */
class EMT_Core {

	/**
	 * Initialize the class.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}


	/**
	 * Add plugin-specific cron schedules.
	 *
	 * @since	1.0.0
	 */
	public function add_cron_schedules( $schedules ) {

		$custom_schedules = EMT_Options::cron_schedules();

		foreach ($custom_schedules as $name => $schedule) {
			$schedules[$name] = $schedule;
		}

		return $schedules;

	}


	/**
	 * Add schedule for the 'emt_delete_old_files' hook.
	 *
	 * @since	1.0.0
	 */
	public function add_schedule_delete_old_files($option, $value) {

		if ( $option !== 'emt_delete_old' ) {
			return;
		}

		$this->schedule_delete_old_files($value);

	}


	/**
	 * Update schedule for the 'emt_delete_old_files' hook.
	 *
	 * @since	1.0.0
	 */
	public function update_schedule_delete_old_files($option, $old_value, $new_value) {

		if ( $option !== 'emt_delete_old' ) {
			return;
		}

		$this->schedule_delete_old_files($new_value);

	}


	/**
	 * Schedule the 'emt_delete_old_files' hook.
	 *
	 * @since	1.0.0
	 */
	private function schedule_delete_old_files($days) {

		if ( is_null($days) ) {
			return;
		}

		$hook = 'emt_delete_old_files';
		$time_of_next_run = wp_next_scheduled($hook);

		$args = array(
			'hook' => $hook,
			'timestamp' => time(),
			'recurrence' => 'daily',
			'action' => '',
		);

		if ( !$time_of_next_run && intval($days) > 0 ) {
			$args['action'] = "add";
		}

		if ( $time_of_next_run && intval($days) === 0 ) {
			$args['action'] = "delete";
		}

		if ( $args['action'] === '' ) {
			return; //return if no scheduling action will be performed
		}

		$this->schedule_hook($args);

	}


	/**
	 * Add schedule for the 'emt_delete_all_files' hook.
	 *
	 * @since	1.0.0
	 */
   public function add_schedule_delete_all_files($option, $value) {

	   if ( $option !== 'emt_delete_all' ) {
		   return;
	   }

	   $this->schedule_delete_all_files($value);

   }


	 /**
	  * Update schedule for the 'emt_delete_all_files' hook.
	  *
	  * @since	1.0.0
	  */
	public function update_schedule_delete_all_files($option, $old_value, $new_value) {

		if ( $option !== 'emt_delete_all' ) {
			return;
		}

		$this->schedule_delete_all_files($new_value);

	}


	/**
	 * Schedule the 'emt_delete_all_files' hook.
	 *
	 * @since	1.0.0
	 */
   private function schedule_delete_all_files($schedule) {

	   if ( is_null($schedule) ) {
		   return;
	   }

	   $hook = 'emt_delete_all_files';
	   $time_of_next_run = wp_next_scheduled($hook);

	   $args = array(
		   'hook' => $hook,
		   'timestamp' => time() + EMT_Options::cron_schedules()[$schedule]['interval'],
		   'recurrence' => $schedule,
		   'action' => '',
	   );

	   if ( !$time_of_next_run && $schedule !== 'emt_never' ) {
		   $args['action'] = "add";
	   }

	   if ( $time_of_next_run && $schedule !== 'emt_never' ) {
		   $args['action'] = "update";
	   }

	   if ( $time_of_next_run && $schedule === 'emt_never' ) {
		   $args['action'] = "delete";
	   }

	   if ( $args['action'] === '' ) {
		   return; //return if no scheduling action will be performed
	   }

	   $this->schedule_hook($args);

   }


	/**
	 * Schedule or unschedule a hook.
	 *
	 * @param array $args List of hook parameters.
	 *
	 * @since	1.0.0
	 */
	private function schedule_hook($args) {

		extract( $args );

		switch ( $action ) {
			case "add":
				wp_schedule_event( $timestamp, $recurrence, $hook );
				break;
			case "update":
				wp_clear_scheduled_hook( $hook );
				wp_schedule_event( $timestamp, $recurrence, $hook );
				break;
			case "delete":
				wp_clear_scheduled_hook( $hook );
				break;
		}

	}


	/**
	 * Delete trashed media files older than the number of days specified by the option 'emt_delete_old'.
	 *
	 * @since	1.0.0
	 */
	public function delete_old_files() {

		$days = EMT_Options::get_option_value('emt_delete_old');

		$args = array(
			'post_type' => 'attachment',
			'posts_per_page' => - 1,
			'post_status' => 'trash',
			'date_query' => array(
				array(
					'before' => "-$days days",
				),
			),
		);

		$this->delete_media_files($args);

	}


	/**
	 * Delete all media files currently in the trash.
	 *
	 * @since	1.0.0
	 */
	public function delete_all_files() {

		$args = array(
			'post_type' => 'attachment',
			'posts_per_page' => - 1,
			'post_status' => 'trash',
		);

		$this->delete_media_files($args);
	}


	/**
	 * Permanently delete media files.
	 *
	 * @param array $args List of parameters for deleting files.
	 *
	 * @since	1.0.0
	 */
	private function delete_media_files($args) {

		if ( MEDIA_TRASH === false || !wp_doing_cron() ) {
			return; //do not run if wp media trash or wp cron is disabled
		}

		$attachments = get_posts($args);
		$delete_permanently = true;

		foreach ($attachments as $attachment) {
			wp_delete_attachment($attachment->ID, $delete_permanently);
		}

	}

}
