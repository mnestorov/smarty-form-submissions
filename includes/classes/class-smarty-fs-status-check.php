<?php

/**
 * The API functionality for checking plugin status on the client site.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.1
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/includes/classes
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Form_Submissions_Status_Check {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Constructor to initialize the plugin status check class.
     *
     * @since    1.0.1
     * @param    string $plugin_name The unique identifier for the plugin.
     * @param    string $version The current version of the plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Registers the REST API route for plugin status check.
     *
     * @since    1.0.1
     */
    public function register_routes() {
        register_rest_route('smarty-form-submissions/v1', '/plugin-status', [
            'methods'             => 'GET',
            'callback'            => [$this, 'status_check'],
            'permission_callback' => '__return_true', // Allow public access to this endpoint
        ]);
    }

    /**
     * Checks if the plugin is active and returns the status.
     *
     * @since    1.0.1
     * @return   WP_REST_Response JSON response with plugin status.
     */
    public function status_check() {
		return new WP_REST_Response([
            'status'  => 'active',
        ], 200);
	}
}