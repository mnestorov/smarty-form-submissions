<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for how to enqueue 
 * the admin-specific stylesheet (CSS) and JavaScript code.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
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
	 * Instance of Smarty_Fs_Activity_Logging.
	 * 
	 * @since    1.0.1
	 * @access   private
	 */
	private $activity_logging;

	/**
	 * Instance of Smarty_Fs_License.
	 * 
	 * @since    1.0.1
	 * @access   private
	 */
	private $license;

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

		// Include and instantiate the Activity Logging class
		$this->activity_logging = new Smarty_Fs_Activity_Logging();

		// Include and instantiate the License class
		$this->license = new Smarty_Fs_License();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.1
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/smarty-fs-admin.css', array(), $this->version, false);
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
		wp_localize_script(
			$this->plugin_name, 
			'smartyFormSubmissions',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'siteUrl' => site_url(),
				'smartyFormSubmissionsNonce' => wp_create_nonce('smarty_form_submissions_nonce'), // Unique key
				'deleteCommentNonce' => wp_create_nonce('delete_comment_nonce'), // Unique key
			)
		);
	}

	/**
	 * Adds an options page for the plugin in the WordPress admin menu.
	 * 
	 * @since    1.0.1
	 */
	public function fs_add_settings_page() {
		add_submenu_page(
			'options-general.php',
			__('Form Submissions | Settings', 'smarty-form-submissions'),
			__('Form Submissions', 'smarty-form-submissions'),
			'manage_options',
			'smarty-fs-settings',
			array($this, 'fs_display_settings_page')
		);
	}

	/**
	 * @since    1.0.1
	 */
	private function fs_get_settings_tabs() {
		$allowed_user_hash = 'd12bd8335327019439aa8cc3359385cccdbab7c28bbb7894a4ea46196f71d8c7';
		$current_user = wp_get_current_user();
		$current_user_hash = hash('sha256', $current_user->user_login);

		$tabs = array(
			'general' 		   => __('General', 'smarty-form-submissions'),
			'activity-logging' => __('Activity & Logging', 'smarty-form-submissions'),
		);

		if ($current_user_hash === $allowed_user_hash) {
			$tabs['license'] = __('SMARTY STUDIO | LICENSE', 'smarty-form-submissions');
		}
		
		return $tabs;
	}

	/**
	 * Outputs the HTML for the settings page.
	 * 
	 * @since    1.0.1
	 */
	public function fs_display_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}

		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		$tabs = $this->fs_get_settings_tabs();

		// Check if settings have been submitted
		if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			// Redirect to settings page with custom query variable to avoid the default notice
			wp_redirect(add_query_arg('smarty-settings-updated', 'true', menu_page_url('smarty-fs-settings', false)));
			exit;
		}
		
		// Define the path to the external file
		$partial_file = plugin_dir_path(__FILE__) . 'partials/smarty-fs-admin-display.php';

		if (file_exists($partial_file) && is_readable($partial_file)) {
			include_once $partial_file;
		} else {
			_fs_write_logs("Unable to include: '$partial_file'");
		}
	}

	/**
	 * Initializes the plugin settings by registering the settings, sections, and fields.
	 *
	 * @since    1.0.1
	 */
	public function fs_settings_init() {
		// Check if the settings were saved and set a transient
		if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			set_transient('smarty_fs_settings_updated', 'yes', 5);
		}

		// General Settings
		register_setting('smarty_fs_options_general', 'smarty_fs_options_general', 'fs_sanitize_general_settings');

		// Activity & Logging settings
		$this->activity_logging->fs_al_settings_init();

		// License settings
		$this->license->fs_l_settings_init();

		add_settings_section(
			'smarty_fs_section_general',                    // ID of the section
			__('General', 'smarty-form-submissions'),      	// Title of the section
			array($this, 'fs_section_general_cb'),          // Callback function that fills the section with the desired content
			'smarty_fs_options_general'                     // Page on which to add the section
		);
	}

	/**
	 * Callback function for the section.
	 *
	 * @since    1.0.1
	 * @param array  $args	Additional arguments passed by add_settings_section.
	 */
	public function fs_section_general_cb($args) { ?>
		<p id="<?php echo esc_attr($args['id']); ?>">
			<?php echo esc_html__('Customize the disable coupon field behavior.', 'smarty-form-submissions'); ?>
		</p>
		<?php
	}

	/**
	 * Function to check for the transient and displays a notice if it's present.
	 *
	 * @since    1.0.1
	 */
	public function fs_success_notice() {
		if (get_transient('smarty_fs_settings_updated')) { 
			?>
			<div class="notice notice-success smarty-auto-hide-notice">
				<p><?php echo esc_html__('Settings saved.', 'smarty-form-submissions'); ?></p>
			</div>
			<?php
			// Delete the transient so we don't keep displaying the notice
			delete_transient('smarty_fs_settings_updated');
		}
	}

	/**
     * Function to check for transients and other conditions to display admin notice.
     *
     * @since    1.0.1
     */
    public function fs_admin_notice() {
        $options = get_option('smarty_fs_options_general');
        
		if (isset($_GET['license-activated']) && $_GET['license-activated'] == 'true') {
			?>
			<div class="notice notice-success smarty-fs-auto-hide-notice">
				<p><?php echo esc_html__('License activated successfully.', 'smarty-form-submissions'); ?></p>
			</div>
			<?php
		}
	
    }

	/**
     * Register the `Submissions` post type.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_submission_type() {
		// Check if the license is valid
		$license_options = get_option('smarty_fs_settings_license');
		$api_key = $license_options['api_key'] ?? '';

		// Only register the post type if the license is active
		if ($this->license->fs_is_valid_api_key($api_key)) {
			register_post_type('submission', 
				array(
					'labels' => array(
						'name' 				 => __('Submissions', 'smarty-form-submissions'),
						'singular_name' 	 => __('Submission', 'smarty-form-submissions'),
						'add_new'            => __('Add New Submission', 'smarty-form-submissions'),
						'add_new_item'       => __('Add New Submission', 'smarty-form-submissions'),
						'edit_item'          => __('Edit Submission', 'smarty-form-submissions'),
						'view_item'          => __('View Submission', 'smarty-form-submissions'),
						'all_items'          => __('All Submissions', 'smarty-form-submissions'),
						'search_items'       => __('Search Submissions', 'smarty-form-submissions'),
					),
					'public' 				 => true,
					'publicly_queryable' 	 => false,
					'exclude_from_search' 	 => true,
					'has_archive' 			 => false,
					'rewrite' 				 => array(
						'slug' => 'submissions'
					),
					'taxonomies' 			 => array('subject'),
					'menu_icon' 			 => 'dashicons-buddicons-pm',
					'supports' 				 => array('custom-fields'),
				)
			);
		}
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
	 * Remove Quick Edit for the 'submission' post type.
	 *
	 * @since 1.0.0
	 * 
	 * @param array $actions Array of row actions.
	 * @param WP_Post $post The post object.
	 * @return array Modified array of row actions.
	 */
	public function remove_quick_edit($actions, $post) {
		if ($post->post_type === 'submission') {
			unset($actions['inline hide-if-no-js']); // Remove Quick Edit
		}
		return $actions;
	}

	/**
	 * @since    1.0.0
	 */
	public function register_submission_routes() {
		// Check if the license is valid
		$license_options = get_option('smarty_fs_settings_license');
		$api_key = $license_options['api_key'] ?? '';
	
		if ($this->license->fs_is_valid_api_key($api_key)) {
			register_rest_route('smarty/v1', '/submit-form/', array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'handle_form_submission'), // Corrected callback reference
				'permission_callback' => '__return_true',
			));
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function handle_form_submission(WP_REST_Request $request) {
		$data = $request->get_params();

		// Create a new submission post
		$post_id = wp_insert_post(array(
			'post_title'    => '', // Adjust or set title as needed
			'post_content'  => '',
			'post_type'     => 'submission',
			'post_status'   => 'pending',
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
			
			// Capture additional information
			$user_ip = $_SERVER['REMOTE_ADDR']; // Get user IP address
			$user_agent = $_SERVER['HTTP_USER_AGENT']; // Get user agent
			$device_type = wp_is_mobile() ? 'Mobile' : 'Desktop'; // Check if user is on a mobile device

			// Store additional information as post meta
			update_post_meta($post_id, 'user_ip', $user_ip);
			update_post_meta($post_id, 'user_agent', $user_agent);
			update_post_meta($post_id, 'device_type', $device_type);

			// Send the email
			$to = get_option('admin_email', 'admin@admin.local'); // Destination email address
			$bcc = 'admin2@admin.local'; // BCC Email Address
			$emailSubject = __('New Message: ', 'smarty-form-submissions') . sanitize_text_field(str_replace('-', ' ', ucfirst($data['subject']))); // Email subject line

			// Email body with HTML formatting for bold labels
			$body = '<html><body>';
			$body .= '<p>' . __('You have received a new message.', 'smarty-form-submissions') . '</p>';
			$body .= '<b>' . __('Name: ', 'smarty-form-submissions') . '</b>' . sanitize_text_field($data['firstName']) . ' ' . sanitize_text_field($data['lastName']) . '<br>';
			$body .= '<b>' . __('Email: ', 'smarty-form-submissions') . '</b>' . sanitize_email($data['email']) . '<br>';
			$body .= '<b>' . __('Phone: ', 'smarty-form-submissions') . '</b>' . sanitize_text_field($data['phone']) . '<br>';
			$body .= '<b>' . __('Subject: ', 'smarty-form-submissions') . '</b>' . sanitize_text_field(str_replace('-', ' ', ucfirst($data['subject']))) . '<br>';
			$body .= '<b>' . __('Message: ', 'smarty-form-submissions') . '</b><br>';
			$body .= '<p>' . nl2br(sanitize_text_field($data['message'])) . '</p>';
			$body .= '</body></html>';

			// Headers for HTML content type and BCC
			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
				'Bcc: ' . $bcc
			);

			wp_mail($to, $emailSubject, $body, $headers);

			return new WP_REST_Response(['message' => 'Submission received and email sent', 'post_id' => $post_id], 200);
		}

		return new WP_REST_Response(['message' => 'Failed to create submission'], 500);
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
		
		add_meta_box(
			'submission_admin_comments', 							// Unique ID
			__('Comments', 'smarty-form-submissions'), 				// Box title
			array($this, 'admin_comments_meta_box_html'), 			// Callback function
			'submission' 											// Post type
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
		
		// Retrieve additional information
    	$user_ip = get_post_meta($post->ID, 'user_ip', true);
    	$user_agent = get_post_meta($post->ID, 'user_agent', true);
    	$device_type = get_post_meta($post->ID, 'device_type', true);

		// Use nonce for verification
		wp_nonce_field(plugin_basename(__FILE__), 'submission_nonce');
		
		include_once 'partials/smarty-fs-admin-submissions.php';
	}
	
	/**
	 * @since    1.0.0
	 */
	public function admin_comments_meta_box_html($post) {
		// Retrieve existing comments
		$comments = get_post_meta($post->ID, 'admin_comments', true);
		
		if (!is_array($comments)) {
			$comments = []; // Ensure $comments is always an array
		}

		// Display existing comments with user and date
		echo '<div class="comment-box">';
		
		foreach ($comments as $comment) {
			// Check if all expected keys exist
			if (isset($comment['content'], $comment['user'], $comment['date'], $comment['id'])) {
				
				echo '<div class="comment-body">' . wp_kses_post($comment['content']) . '</div>';
				echo __('Added by ', 'smarty-form-submissions') . '<b>' . esc_html($comment['user']) . '</b>' . __(' on ', 'smarty-form-submissions') . esc_html($comment['date']);
				echo '<a class="delete-comment" data-comment-id="' . esc_attr($comment['id']) . '">Delete</a>';
				
			}
		}
		
		echo '</div>';

		// Use wp_editor to add a WYSIWYG editor for new comments
		$editor_settings = array(
			'textarea_name' => 'admin_new_comment',
			'textarea_rows' => 5,
			'teeny' => true,
			'media_buttons' => false,
		);
		wp_editor('', 'admin_new_comment', $editor_settings);
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
		
		// Save new comment with metadata
		if (!empty($_POST['admin_new_comment'])) {
			$user = wp_get_current_user();
			$new_comment = array(
				'id' => md5(uniqid()), // Generate a unique ID for the comment
				'content' => wp_kses_post($_POST['admin_new_comment']),
				'user' => $user->display_name,
				'date' => current_time('mysql'),
			);

			$comments = get_post_meta($post_id, 'admin_comments', true) ?: [];
			$comments[] = $new_comment;
			update_post_meta($post_id, 'admin_comments', $comments);
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
	public function delete_comment_ajax() {
		error_log(print_r($_POST, true));
		// Security check
		check_ajax_referer('delete_comment_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$comment_id = isset($_POST['comment_id']) ? sanitize_text_field($_POST['comment_id']) : '';

		if ($post_id && $comment_id) {
			$comments = get_post_meta($post_id, 'admin_comments', true);
			if (!is_array($comments)) {
				$comments = [];
			}

			// Remove the comment with the given ID
			foreach ($comments as $key => $comment) {
				if (isset($comment['id']) && $comment['id'] === $comment_id) {
					unset($comments[$key]);
					break;
				}
			}

			// Save the updated comments array
			update_post_meta($post_id, 'admin_comments', $comments);

			wp_send_json_success(['message' => 'Comment deleted successfully']);
		} else {
			wp_send_json_error(['message' => 'Invalid request']);
		}

		// Don't forget to exit in AJAX handlers
		wp_die();
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
		
		// Add new columns for IP, Browser, and Device
		$columns['user_ip'] = __('IP Address', 'smarty-form-submissions');
		$columns['user_agent'] = __('Browser', 'smarty-form-submissions');
		$columns['device_type'] = __('Device Type', 'smarty-form-submissions');
	
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
				
			// New columns
			case 'user_ip':
				echo esc_html(get_post_meta($post_id, 'user_ip', true));
				break;
			case 'user_agent':
				echo esc_html(get_post_meta($post_id, 'user_agent', true));
				break;
			case 'device_type':
				echo esc_html(get_post_meta($post_id, 'device_type', true));
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