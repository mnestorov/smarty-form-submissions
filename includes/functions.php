<?php

/**
 * The plugin functions file.
 *
 * This is used to define general functions, shortcodes etc.
 * 
 * Important: Always use the `__smarty` prefix for function names.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/public
 * @author     Smarty Studio | Martin Nestorov
 */

if (!function_exists('__smarty_fs_logs')) {
	/**
     * Writes logs for the Form Submissions plugin.
     * 
     * @param string $message Message to be logged.
     * @param mixed $data Additional data to log, optional.
     */
    function __smarty_fs_logs($message, $data = null) {
        $log_entry = '[' . current_time('mysql') . '] ' . $message;
    
        if (!is_null($data)) {
            $log_entry .= ' - ' . print_r($data, true);
        }

        $logs_file = fopen(FS_BASE_DIR . DIRECTORY_SEPARATOR . "logs.txt", "a+");
        fwrite($logs_file, $log_entry . "\n");
        fclose($logs_file);
    }
}