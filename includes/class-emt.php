<?php

/**
 * Main plugin class.
 *
 * Defines internationalization, admin hooks and core hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/includes
 */
class EMT {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      EMT_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * Set the plugin name and the plugin version.
	 * Load the dependencies, define the locale and add hooks.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = EMT_NAME;
		$this->version = EMT_VERSION;

		$this->include_dependencies();
		$this->initiate_loader();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_core_hooks();
	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - EMT_Loader. Orchestrates the hooks of the plugin.
	 * - EMT_i18n. Defines internationalization functionality.
	 * - EMT_Admin. Defines all hooks for the admin area.
	 * - EMT_Core. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function include_dependencies() {

		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emt-loader.php';


		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emt-i18n.php';


		if ( is_admin() ) {
			/**
			* The class responsible for defining all actions that occur in the admin area.
			*/
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-emt-admin.php';

			/**
			* Class containing functions used in the admin area.
			*/
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/functions/class-emt-admin-functions.php';

			/**
			* Class containing code that runs when plugin is activated.
			*/
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emt-activator.php';
		}


		/**
		 * The class responsible for defining the core functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emt-core.php';


		/**
		 * The class that contains the plugin's default values.
		 */
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emt-options.php';

	}


	/**
	 * Initiate the loader.
	 *
	 * Creates a new instance of the EMT_Loader class.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function initiate_loader() {

		$this->loader = new EMT_Loader();

	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the EMT_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new EMT_i18n($this->get_plugin_name());

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	/**
	 * Register all hooks related to the admin functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		if ( is_admin() ) {
			$plugin_admin = new EMT_Admin();
			$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
			$this->loader->add_action('admin_notices', $plugin_admin, 'display_admin_notices');
			$this->loader->add_filter("plugin_action_links", $plugin_admin, 'add_settings_shortcut', 10, 2);
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');

			$plugin_activator = new EMT_Activator();
			$this->loader->add_action('admin_init', $plugin_activator, 'activate_plugin');
		}

	}


	/**
	 * Register all hooks related to the core functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks() {

		$plugin_core = new EMT_Core();

		// add plugin-specific cron schedules
		$this->loader->add_filter('cron_schedules', $plugin_core, 'add_cron_schedules');

		// schedule plugin-specific "cleanup hooks" when database options are added
		$this->loader->add_action("added_option", $plugin_core, 'add_schedule_delete_old_files', 10, 2);
		$this->loader->add_action("added_option", $plugin_core, 'add_schedule_delete_all_files', 10, 2);

		// schedule plugin-specific "cleanup hooks" when database options are updated
		$this->loader->add_action("updated_option", $plugin_core, 'update_schedule_delete_old_files', 10, 3);
		$this->loader->add_action("updated_option", $plugin_core, 'update_schedule_delete_all_files', 10, 3);

		// attach events to the plugin-specific cleanup hooks
		$this->loader->add_action('emt_delete_old_files', $plugin_core, 'delete_old_files');
		$this->loader->add_action('emt_delete_all_files', $plugin_core, 'delete_all_files');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    EMT_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
