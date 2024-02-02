<?php

/**
 * Register the custom post type.
 * 
 * @link       https://smartystudio.net/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/public
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Form_Submissions_Register_Post_Types {
    
    /**
     * Register the `Submissions` post type.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_submission_type() {
        /**
         * Post Type: Submissions
         */
        $labels = array(
            'name'          => __('Submissions', 'smarty-form-submissions'),
            'singular_name' => __('Submission', 'smarty-form-submissions'),
        );

        $args = array(
            'public'              => true,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'has_archive'         => false,
            'rewrite'             => array('slug' => 'submissions'),
            'supports'            => array('custom-fields'),
            'taxonomies'          => array('subject'),
            'menu_icon'           => 'dashicons-buddicons-pm',
        );

        register_post_type('submissions', $args);
    }
}