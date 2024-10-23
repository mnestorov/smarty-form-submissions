<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link                    https://github.com/mnestorov
 * @since                   1.0.0
 * @package                 Smarty_Form_Submissions
 *
 * @wordpress-plugin
 * Plugin Name:             SM - Form Submissions
 * Plugin URI:              https://github.com/mnestorov/smarty-form-submissions
 * Description:             Powerful and intuitive tool designed to enhance the interaction between website owners and their users through efficient form submission management.
 * Version:                 1.0.0
 * Author:                  Smarty Studio | Martin Nestorov
 * Author URI:              https://github.com/mnestorov
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             smarty-form-submissions
 * Domain Path:             /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Check if FS_VERSION is not already defined
if (!defined('FS_VERSION')) {
	/**
	 * Current plugin version.
	 * For the versioning of the plugin is used SemVer - https://semver.org
	 */
	define('FS_VERSION', '1.0.0');
}

// Check if FS_BASE_DIR is not already defined
if (!defined('FS_BASE_DIR')) {
	/**
	 * This constant is used as a base path for including other files or referencing directories within the plugin.
	 */
    define('FS_BASE_DIR', dirname(__FILE__));
}

// Check if FS_DB_PREFIX is not already defined
if (!defined('FS_DB_PREFIX')) {
	/**
	 * This constant is used to store the db table name prefix.
	 */
    define('FS_DB_PREFIX', 'smarty_');
}

// Check if FS_TABLE is not already defined
if (!defined('FS_TABLE')) {
	/**
	 * This constant is used to store the table name where form submissions data is kept.
	 */
    define('FS_TABLE', 'form_submissions');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/classes/class-smarty-fs-activator.php
 * 
 * @since    1.0.0
 */
function activate_fs() {
	require_once plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-fs-activator.php';
	Smarty_Form_Submissions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/classes/class-smarty-fs-deactivator.php
 * 
 * @since    1.0.0
 */
function deactivate_fs() {
	require_once plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-fs-deactivator.php';
	Smarty_Form_Submissions_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fs');
register_deactivation_hook(__FILE__, 'deactivate_fs');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-fs-locator.php';

/**
 * The plugin functions file that is used to define general functions, shortcodes etc.
 */
require plugin_dir_path(__FILE__) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fs() {
	$plugin = new Smarty_Form_Submissions_Locator();
	$plugin->run();
}

run_fs();
