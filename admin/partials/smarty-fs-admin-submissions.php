<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/mnestorov/smarty-form-submissions
 * @since      1.0.0
 *
 * @package    Smarty_Form_Submissions
 * @subpackage Smarty_Form_Submissions/admin/partials
 * @author     Smarty Studio | Martin Nestorov
 */
?>

<div id="submission" class="wrap">
	<table class="form-table wp-list-table widefat fixed striped">
		<tbody>
			<tr>
				<th scope="row"><label for="first_name"><?= __('First Name:', 'smarty-form-submissions'); ?></label></th>
				<td><input type="text" id="first_name" name="first_name" value="<?= esc_attr($firstName); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="last_name"><?= __('Last Name:', 'smarty-form-submissions'); ?></label></th>
				<td><input type="text" id="last_name" name="last_name" value="<?= esc_attr($lastName); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="email"><?= __('Email:', 'smarty-form-submissions'); ?></label></th>
				<td><input type="email" id="email" name="email" value="<?= esc_attr($email); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="phone"><?= __('Phone:', 'smarty-form-submissions'); ?></label></th>
				<td><input type="text" id="phone" name="phone" value="<?= esc_attr($phone); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="subject"><?= __('Subject:', 'smarty-form-submissions'); ?></label></th>
				<td>
					<select id="subject" name="subject" class="regular-text">
						<?php $terms = get_terms(['taxonomy' => 'subject', 'hide_empty' => false]); ?>
						<?php foreach ($terms as $term) : ?>
							<option value="<?= esc_attr($term->slug); ?>" <?= selected($selected_subject_slug, $term->slug, false); ?>>
								<?= esc_html($term->name); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="message"><?= __('Message:', 'smarty-form-submissions'); ?></label></th>
				<td><textarea id="message" name="message" class="large-text" rows="6"><?= esc_textarea($message); ?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>