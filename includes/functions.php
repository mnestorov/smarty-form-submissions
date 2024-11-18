<?php

/**
 * The plugin functions file.
 *
 * This is used to define general functions, shortcodes etc.
 * 
 * Important: Always use the `smarty_` prefix for function names.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/public
 * @author     Smarty Studio | Martin Nestorov
 */

if (!function_exists('smarty_fs_get_browser_and_device_type')) {
    /**
     * Helper function to check browser and device type.
     * 
     * @since      1.0.1
     */
    function smarty_fs_get_browser_and_device_type() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Browser detection based on user agent (simplified)
        $browser = 'Unknown';
        if (stripos($user_agent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($user_agent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($user_agent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (stripos($user_agent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (stripos($user_agent, 'MSIE') !== false || stripos($user_agent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        }
        
        // Device type based on user agent
        $device_type = (preg_match('/Mobile|Android|iPhone|iPad/i', $user_agent)) ? 'Mobile' : 'Desktop';

        return [
            'browser' => $browser,
            'device_type' => $device_type
        ];
    }
}

if (!function_exists('smarty_fs_get_os')) {
    /**
     * Helper function to check operating system.
     * 
     * @since      1.0.1
     */
    function smarty_fs_get_os() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS";

        // Define an array of OS platforms to match against the user agent
        $os_array = [
            '/windows nt 10/i'     => 'Windows 10',
            '/windows nt 6.3/i'    => 'Windows 8.1',
            '/windows nt 6.2/i'    => 'Windows 8',
            '/windows nt 6.1/i'    => 'Windows 7',
            '/windows nt 6.0/i'    => 'Windows Vista',
            '/windows nt 5.1/i'    => 'Windows XP',
            '/macintosh|mac os x/i'=> 'Mac OS X',
            '/linux/i'             => 'Linux',
            '/iphone/i'            => 'iOS',
            '/android/i'           => 'Android',
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }

        return $os_platform;
    }
}

if (!function_exists('_fs_write_logs')) {
	/**
     * Writes logs for the plugin.
     * 
     * @since      1.0.1
     * @param string $message Message to be logged.
     * @param mixed $data Additional data to log, optional.
     */
    function _fs_write_logs($message, $data = null) {
        $log_entry = '[' . current_time('mysql') . '] ' . $message;
    
        if (!is_null($data)) {
            $log_entry .= ' - ' . print_r($data, true);
        }

        $logs_file = fopen(FS_BASE_DIR . DIRECTORY_SEPARATOR . "logs.txt", "a+");
        fwrite($logs_file, $log_entry . "\n");
        fclose($logs_file);
    }
}