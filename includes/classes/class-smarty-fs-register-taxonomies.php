<?php

/**
 * Register the taxonomies for the post type.
 * 
 * @link       https://smartystudio.net/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/public
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Form_Submissions_Register_Taxonomies {
    
    /**
     * Register the `Subject` taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_subject_taxonomy() {
        /**
         * Taxonomy: Subject
         */
        $labels = array(
            'name'              => _x('Subjects', 'taxonomy general name', 'smarty-form-submissions'),
            'singular_name'     => _x('Subject', 'taxonomy singular name', 'smarty-form-submissions'),
            'search_items'      => __('Search Subjects', 'smarty-form-submissions'),
            'all_items'         => __('All Subjects', 'smarty-form-submissions'),
            'parent_item'       => __('Parent Subject', 'smarty-form-submissions'),
            'parent_item_colon' => __('Parent Subject:', 'smarty-form-submissions'),
            'edit_item'         => __('Edit Subject', 'smarty-form-submissions'),
            'update_item'       => __('Update Subject', 'smarty-form-submissions'),
            'add_new_item'      => __('Add New Subject', 'smarty-form-submissions'),
            'new_item_name'     => __('New Subject Name', 'smarty-form-submissions'),
            'menu_name'         => __('Subjects', 'smarty-form-submissions'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'subject'),
            'show_in_rest'      => true,
        );

        // Associate post types.
        $post_types = array('submission');

        register_taxonomy('size', $post_types, $args);
    }
}