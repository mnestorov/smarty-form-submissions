<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for how to enqueue 
 * the admin-specific stylesheet (CSS) and JavaScript code.
 *
 * @link       https://smartystudio.net/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/admin
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Form_Submissions_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version         The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name     The name of this plugin.
	 * @param    string    $version         The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function enqueues custom CSS for the plugin settings in WordPress admin.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Form_Submissions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Form_Submissions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $pagenow, $typenow;

   		if ($pagenow == 'edit.php' && $typenow == 'submission') {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/smarty-fs-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function enqueues custom JavaScript for the plugin settings in WordPress admin.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Form_Submissions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Form_Submissions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/smarty-fs-admin.js', array('jquery'), $this->version, false);
	}

	/**
     * Register the `Submissions` post type.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_submission_type() {
        register_post_type('submission', 
			array(
				'labels' => array(
					'name' 				 => __('Submissions', 'smarty-form-submissions'),
					'singular_name' 	 => __('Submission', 'smarty-form-submissions'),
					'add_new'            => __('Add New Submission', 'smarty-form-submissions'),
            		'add_new_item'       => __('Add New Submission', 'smarty-form-submissions'),
				),
				'public' 				 => true,
				'publicly_queryable' 	 => false,
				'exclude_from_search' 	 => true,
				'has_archive' 			 => false,
				'rewrite' 				 => array(
					'slug' => 'submissions'
				),
				'supports' 				 => array('custom-fields'),
        		'taxonomies' 			=> array('subject'),
				'menu_icon' 			=> 'dashicons-buddicons-pm',
			)
		);
    }

	/**
     * Register the `Subject` taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_subject_taxonomy() {
        $labels = array(
			'name' 				=> _x('Subjects', 'smarty-form-submissions'),
			'singular_name' 	=> _x('Subject', 'smarty-form-submissions'),
			'search_items' 		=> __('Search Subjects', 'smarty-form-submissions'),
			'all_items' 		=> __('All Subjects', 'smarty-form-submissions'),
			'parent_item' 		=> __('Parent Subject', 'smarty-form-submissions'),
			'parent_item_colon' => __('Parent Subject:', 'smarty-form-submissions'),
			'edit_item' 		=> __('Edit Subject', 'smarty-form-submissions'),
			'update_item' 		=> __('Update Subject', 'smarty-form-submissions'),
			'add_new_item' 		=> __('Add New Subject', 'smarty-form-submissions'),
			'new_item_name' 	=> __('New Subject Name', 'smarty-form-submissions'),
			'menu_name' 		=> __('Subjects', 'smarty-form-submissions'),
		);
	
		$args = array(
			'hierarchical' 		=> true,
			'labels' 			=> $labels,
			'show_ui' 			=> true,
			'show_admin_column' => true,
			'query_var' 		=> true,
			'rewrite' 			=> array(
				'slug' => 'subject'
			),
			'show_in_rest' 		=> true,
		);
	
		register_taxonomy('subject', array('submission'), $args);
    }
	
	/**
	 * @since    1.0.0
	 */
	public function remove_add_new_submenu() {
		global $submenu;
		// Remove 'Add New' from the submenu
		unset($submenu['edit.php?post_type=submission'][10]);
	}

	/**
	 * @since    1.0.0
	 */
	public function register_submission_routes() {
		register_rest_route('smarty/v1', '/submit-form/', array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => array($this, 'handle_form_submission'), // Corrected callback reference
			'permission_callback' => '__return_true',
		));
	}

	/**
	 * @since    1.0.0
	 */
	public function handle_form_submission(WP_REST_Request $request) {
		$data = $request->get_params();
	
		// Create a new submission post
		$post_id = wp_insert_post(array(
			'post_title' 	=> '', // wp_strip_all_tags($data['firstName'] . ' ' . $data['lastName'])
			'post_content'  => '',
			'post_type' 	=> 'submission',
			'post_status' 	=> 'pending',
		));
	
		if ($post_id) {
			// Update custom fields
			update_post_meta($post_id, 'first_name', sanitize_text_field($data['firstName']));
			update_post_meta($post_id, 'last_name', sanitize_text_field($data['lastName']));
			update_post_meta($post_id, 'email', sanitize_email($data['email']));
			update_post_meta($post_id, 'phone', sanitize_text_field($data['phone']));

			if (!empty($data['subject'])) { // Handle the subject taxonomy
				// Assuming $data['subject'] contains the term's name
				// This will create the term if it doesn't exist or add the existing term to the post
				wp_set_object_terms($post_id, sanitize_text_field($data['subject']), 'subject', false);
			}

			update_post_meta($post_id, 'message', sanitize_text_field($data['message']));

			return new WP_REST_Response(array('message' => 'Submission successful', 'post_id' => $post_id), 200);
		}
	
		return new WP_REST_Response(array('message' => 'Failed to create submission'), 500);
	}

	/**
	 * @since    1.0.0
	 */
	public function add_submission_meta_boxes() {
		add_meta_box(
			'submission_meta_box',                             		// Unique ID
			__('Submission Details', 'smarty-form-submissions'), 	// Box title
			array($this, 'submission_meta_box_html'),               // Corrected callback
			'submission'                                        	// Post type
		);
	}

	/**
	 * @since    1.0.0
	 */
	public function submission_meta_box_html($post) {
		$firstName 				= get_post_meta($post->ID, 'first_name', true);
		$lastName 				= get_post_meta($post->ID, 'last_name', true);
		$email 					= get_post_meta($post->ID, 'email', true);
		$phone 					= get_post_meta($post->ID, 'phone', true);
		$selected_subject 		= get_the_terms($post->ID, 'subject'); // This will get the current subject terms assigned to the post
		$selected_subject_slug  = !empty($selected_subject) ? $selected_subject[0]->slug : ''; // Assuming one subject per submission for simplicity
		$message 				= get_post_meta($post->ID, 'message', true);

		// Use nonce for verification
		wp_nonce_field(plugin_basename(__FILE__), 'submission_nonce');
		
		include_once 'partials/smarty-fs-admin-display.php';
	}

	/**
	 * @since    1.0.0
	 */
	public function save_submission_meta_box($post_id) {
		// Check if our nonce is set.
		if (!isset($_POST['submission_nonce'])) {
			return;
		}

		// Verify that the nonce is valid.
		if (!wp_verify_nonce($_POST['submission_nonce'], plugin_basename(__FILE__))) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// Check the user's permissions.
		if (isset($_POST['post_type']) && 'submission' == $_POST['post_type']) {
			if (!current_user_can('edit_post', $post_id)) {
				return;
			}
		}

		// Now we can actually save the data
		$allowed_fields = [
			'first_name',
			'last_name',
			'email',
			'phone',
			'subject',
			'message',
		];

		foreach ($allowed_fields as $field) {
			if (array_key_exists($field, $_POST)) {
				update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
			}
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function auto_publish_submission_on_edit() {
		global $pagenow;
	
		// Check if we're on the post edit page in the admin
		if ('post.php' === $pagenow && isset($_GET['action']) && 'edit' === $_GET['action'] && isset($_GET['post'])) {
			$post_id = $_GET['post'];
			$post = get_post($post_id);
	
			// Check if this is a submission post type and currently pending
			if ($post instanceof WP_Post && 'submission' === $post->post_type && 'pending' === $post->post_status) {
				// Update the post status to 'publish'
				wp_update_post(array(
					'ID' => $post_id,
					'post_status' => 'publish',
				));
			}
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function modify_submission_columns($columns) {
		unset($columns['title']);
		unset($columns['date']);
		unset($columns['taxonomy-subject']);
		
		// Adding new columns
		$columns['submission_name']   = __('Name', 'smarty-form-submissions');
		$columns['submission_email']  = __('Email', 'smarty-form-submissions');
		$columns['submission_phone']  = __('Phone', 'smarty-form-submissions');
		$columns['taxonomy-subject']  = __('Subject', 'smarty-form-submissions');
		$columns['submission_status'] = __('Status', 'smarty-form-submissions');
		$columns['date'] 			  = __('Date', 'smarty-form-submissions');
	
		return $columns;
	}

	/**
	 * @since    1.0.0
	 */
	public function custom_submission_column($column, $post_id) {
		switch ($column) {
			case 'submission_name':
				$first_name = get_post_meta($post_id, 'first_name', true);
				$last_name = get_post_meta($post_id, 'last_name', true);
				$edit_link = get_edit_post_link($post_id);
				echo '<a href="' . $edit_link . '">'.esc_html($first_name . ' ' . $last_name).'</a>';
				break;
			
			case 'submission_email':
				$email = get_post_meta($post_id, 'email', true);
				echo esc_html($email);
				break;
				
			case 'submission_phone':
				$phone = get_post_meta($post_id, 'phone', true);
				echo esc_html($phone);
				break;
				
			case 'submission_status':
				$post_status = get_post_status($post_id);
				$status_name = get_post_status_object($post_status)->label;
				echo '<span class="submission-status ' . esc_attr($post_status) . '">' . esc_html($status_name) . '</span>';
				break;
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function make_submission_columns_sortable($columns) {
		$columns['submission_name'] = 'submission_name'; // The array key 'submission_name' should be the ID used in the smarty_modify_submission_columns function
		// Note: Actual sorting by first and last name would require additional code to modify the query
		return $columns;
	}

	/**
	 * @since    1.0.0
	 */
	public function exclude_submissions_from_feed($query) {
		if ($query->is_feed()) {
			$query->set('post_type', 'post'); // Only include standard posts in feeds
		}
	}
}
