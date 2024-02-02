<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://smartystudio.net/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/admin/partials
 * @author     Smarty Studio | Martin Nestorov
 */
?>

<div class="wrap">
	<div class="smarty-fs-settings-section">
		<table class="form-table" style="width: auto;">
			<tbody>
				<tr>
					<td>
						<label for="first_name"><b><?= __('First Name:', 'smarty-form-submissions'); ?></b></label><br>
						<input type="text" id="first_name" name="first_name" value="<?= esc_attr($firstName); ?>" />
					</td>
					<td>
						<label for="last_name"><b><?= __('Last Name:', 'smarty-form-submissions'); ?></b></label><br>
						<input type="text" id="last_name" name="last_name" value="<?=  esc_attr($lastName); ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="email"><b><?= __('Email:', 'smarty-form-submissions'); ?></b></label><br>
						<input type="email" id="email" name="email" value="<?= esc_attr($email); ?>" />
					</td>
					<td>
						<label for="phone"><b><?= __('Phone:', 'smarty-form-submissions'); ?></b></label><br>
						<input type="text" id="phone" name="phone" value="<?= esc_attr($phone); ?>" />
					</td>
				</tr>
				<tr>	
					<td colspan="3">
						<label for="subject"><b><?= __('Subject:', 'smarty-form-submissions'); ?></b></label><br>
						<select id="subject" name="subject">
						<?php $terms = get_terms(array('taxonomy' => 'subject', 'hide_empty' => false, )); ?>
						<?php foreach ($terms as $term) : ?>
							<option value="<?= esc_attr($term->slug) . ' ' . selected($selected_subject_slug, $term->slug, false); ?>">
								<?= esc_html($term->name); ?>
							</option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<label for="message"><b><?= __('Message:', 'smarty-form-submissions'); ?></b></label><br>
						<textarea id="message" name="message" class="widefat" rows="6">
							<?= esc_textarea($message); ?>
						</textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>