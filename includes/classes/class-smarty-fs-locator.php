<?php

/**
 * The core plugin class.
 *
 * This is used to define attributes, functions, internationalization used across
 * both the admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/includes/classes
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Form_Submissions_Locator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks
	 * that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smarty_Form_Submissions_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('FS_VERSION')) {
			$this->version = FS_VERSION;
		} else {
			$this->version = '1.0.1';
		}

		$this->plugin_name = 'smarty-form-submissions';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_status_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smarty_Form_Submissions_Loader. Orchestrates the hooks of the plugin.
	 * - Smarty_Form_Submissions_i18n. Defines internationalization functionality.
	 * - Smarty_Form_Submissions_Admin. Defines all hooks for the admin area.
	 * - Smarty_Form_Submissions_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-fs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-fs-i18n.php';

		/**
		 * The class responsible for interacting with the API.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-fs-api.php';
		
		/**
		 * The class responsible for registering REST Route for plugn status check.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-fs-status-check.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/class-smarty-fs-admin.php';

		/**
		 * The class responsible for Activity & Logging functionality in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/tabs/class-smarty-fs-activity-logging.php';

		/**
		 * The class responsible for License functionality in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/tabs/class-smarty-fs-license.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../public/class-smarty-fs-public.php';

		// Run the loader
		$this->loader = new Smarty_Form_Submissions_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smarty_Form_Submissions_I18n class in order to set the domain and to
	 * register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Smarty_Form_Submissions_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Smarty_Form_Submissions_Admin($this->get_plugin_name(), $this->get_version());

		$plugin_activity_logging = new Smarty_Fs_Activity_Logging();
		$plugin_license = new Smarty_Fs_License();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'fs_add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'fs_settings_init');
		$this->loader->add_action('admin_notices', $plugin_admin, 'fs_success_notice');
		$this->loader->add_action('admin_notices', $plugin_admin, 'fs_admin_notice');
		$this->loader->add_action('init', $plugin_admin, 'register_submission_type');
		$this->loader->add_action('init', $plugin_admin, 'register_subject_taxonomy', 0);
		$this->loader->add_action('admin_menu', $plugin_admin, 'remove_add_new_submenu');
		$this->loader->add_action('rest_api_init', $plugin_admin, 'register_submission_routes');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_submission_meta_boxes');
		$this->loader->add_action('save_post', $plugin_admin, 'save_submission_meta_box');
		$this->loader->add_action('admin_init', $plugin_admin, 'auto_publish_submission_on_edit');
		$this->loader->add_filter('post_row_actions', $plugin_admin, 'remove_quick_edit', 10, 2);
		$this->loader->add_filter('manage_submission_posts_columns', $plugin_admin, 'modify_submission_columns');
		$this->loader->add_action('manage_submission_posts_custom_column', $plugin_admin, 'custom_submission_column', 10, 2);
		$this->loader->add_filter('manage_edit-submission_sortable_columns', $plugin_admin, 'make_submission_columns_sortable');
		$this->loader->add_action('pre_get_posts', $plugin_admin, 'exclude_submissions_from_feed');
		$this->loader->add_action('wp_ajax_delete_comment', $plugin_admin, 'delete_comment_ajax');

		// Register hooks for Activity & Logging
		$this->loader->add_action('admin_init', $plugin_activity_logging, 'fs_al_settings_init');
        $this->loader->add_action('wp_ajax_smarty_fs_clear_logs', $plugin_activity_logging, 'fs_handle_ajax_clear_logs');

		// Register hooks for License management
		$this->loader->add_action('admin_init', $plugin_license, 'fs_l_settings_init');
		$this->loader->add_action('updated_option', $plugin_license, 'fs_handle_license_status_check', 10, 3);
		$this->loader->add_action('admin_notices', $plugin_license, 'fs_license_notice');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Smarty_Form_Submissions_Public($this->get_plugin_name(), $this->get_version());
		
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}
	
	/**
	 * Register all of the hooks related to the REST Route functionality of the plugin.
	 *
	 * @since    1.0.1
	 * @access   private
	 */
	private function define_status_hooks() {
		$plugin_status = new Smarty_Form_Submissions_Status_Check($this->get_plugin_name(), $this->get_version());
		
		$this->loader->add_action('rest_api_init', $plugin_status, 'register_routes');
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
	 * @return    Smarty_Form_Submissions_Loader    Orchestrates the hooks of the plugin.
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