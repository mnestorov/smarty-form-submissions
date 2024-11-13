<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.1
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/admin/partials
 * @author     Smarty Studio | Martin Nestorov
 */
?>

<?php $license_options = get_option('smarty_fs_settings_license'); ?>
<?php $api_key = $license_options['api_key'] ?? ''; ?>

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<h2 class="nav-tab-wrapper">
		<?php foreach ($tabs as $tab_key => $tab_caption) : ?>
			<?php $active = $current_tab == $tab_key ? 'nav-tab-active' : ''; ?>
			<a class="nav-tab <?php echo $active; ?>" href="?page=smarty-fs-settings&tab=<?php echo $tab_key; ?>">
				<?php echo $tab_caption; ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<?php if ($this->license->fs_is_valid_api_key($api_key)) : ?>
		<form action="options.php" method="post">
			<?php if ($current_tab == 'general') : ?>
				<?php settings_fields('smarty_fs_options_general'); ?>
				<?php do_settings_sections('smarty_fs_options_general'); ?>
			<?php elseif ($current_tab == 'activity-logging') : ?>
				<?php settings_fields('smarty_fs_options_activity_logging'); ?>
				<?php do_settings_sections('smarty_fs_options_activity_logging'); ?>
			<?php elseif ($current_tab == 'license') : ?>
				<?php settings_fields('smarty_fs_options_license'); ?>
				<?php do_settings_sections('smarty_fs_options_license'); ?>
			<?php endif; ?>
			<?php submit_button(); ?>
		</form>
	<?php else: ?>
		<form action="options.php" method="post">
			<?php if ($current_tab == 'license') : ?>
				<?php settings_fields('smarty_fs_options_license'); ?>
				<?php do_settings_sections('smarty_fs_options_license'); ?>
				<?php submit_button(__('Save Settings', 'smarty-form-submissions')); ?>
			<?php else: ?>
				<p class="description smarty-error" style="margin: 30px 0;"><?php echo esc_html__('Please enter a valid license key in the License tab to access this setting.', 'smarty-form-submissions'); ?></p>
			<?php endif; ?>
		</form>
	<?php endif; ?>
</div>