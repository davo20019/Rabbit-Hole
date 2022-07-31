<?php defined( 'ABSPATH' ) || exit; ?>
<select id="<?php echo $template_data['field_id']; ?>" name="<?php echo $template_data['field_id']; ?>">
	<?php foreach ( get_rh_behavior_field_options() as $name ): ?>
		<?php $selected = ( $name == $template_data['default_value'] ) ? 'selected' : ''; ?>
		<option value="<?php echo $name; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
	<?php endforeach; ?>
</select>
