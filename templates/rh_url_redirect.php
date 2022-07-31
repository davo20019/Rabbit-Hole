<?php defined( 'ABSPATH' ) || exit; ?>
<div class="post-url-redirect">
	<input type="url" pattern="https://.*" id="<?php echo $template_data['field_id']; ?>" name="<?php echo $template_data['field_id']; ?>" value="<?php echo $template_data['default_value']; ?>" class="form-text">
	<em>Enter a URL to redirect to. This is used only for the Page Redirect option. Leave blank to disable.</em>
</div>
