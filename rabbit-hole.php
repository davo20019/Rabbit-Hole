<?php
/**
 * Plugin Name:     Rabbit Hole
 * Plugin URI:      https://davidloor.com/
 * Description:     Rabbit Hole is a WordPress plugin that adds the ability to control what should happen when a post is being viewed at its own page.
 * Author:          davo20019
 * Author URI:      https://davidloor.com/
 * Text Domain:     rabbit-hole
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Rabbit_Hole
 */

/**
 * Add the admin page.
 */
function rabbit_hole_add_settings_page() {
	add_options_page( 'Rabbit Hole', 'Rabbit Hole', 'manage_options', 'rabbit-hole-plugin', 'rabbit_hole_plugin_settings_page' );
}
add_action( 'admin_menu', 'rabbit_hole_add_settings_page' );

/**
 * Render the admin page.
 */
function rabbit_hole_plugin_settings_page() {
	?>
	<h1>Rabbit Hole Settings</h1>
	<p>Below you see the list of posts created in the site. Select the setting that makes the most sense to you when somebody tries to see a post of that type.</p>
	<form action="options.php" method="post">
		<?php
			settings_errors();
			settings_fields( 'rabbit_hole_plugin_options' );
			do_settings_sections( 'rabbit-hole-plugin' );
			submit_button();
		?>
	</form>
	<?php
}

/**
 * Register the settings.
 */
function rabbit_hole_register_settings() {

	$post_types = get_post_types( ['public' => true], 'objects' );

	foreach( $post_types as $post_type_obj ) {
		$section_name = 'rabbit_hole_plugin_section_' . $post_type_obj->name;
		add_settings_section( $section_name, $post_type_obj->name, 'rabbit_hole_plugin_section_callback', 'rabbit-hole-plugin' );
		foreach ( get_admin_fields() as $field_name => $values ) {
			$field_id = get_field_id_by_post_type($post_type_obj->name, $field_name);
			$field_label = $values['label'] . ': ';
			$option_name = 'rabbit_hole_plugin_options';
			$args = [
				'field_id' => $field_id,
				'field_name' => $field_name,
				'field' => $values,
				'post_type' => $post_type_obj,
				'option_name' => $option_name,
			];
			add_settings_field( $field_id, $field_label, 'rabbit_hole_plugin_field_callback', 'rabbit-hole-plugin', 'rabbit_hole_plugin_section_' . $post_type_obj->name, $args );
			register_setting( $option_name, $field_id, 'esc_attr');
		}
	}
}
add_action( 'admin_init', 'rabbit_hole_register_settings' );

/**
 * list of options for the behaviour of the posts.
 */
function get_rh_behavior_field_options() {
	return [
		'display_page' => 'Display the page',
		'access_denied' => 'Access denied',
		'page_not_found' => 'Page not found',
		'page_redirect' => 'Page redirect'
	];
}

/**
 * @return string[][]
 */
function get_admin_fields() {
	return [
		// TODO: Allow the user to select the behavior of each post.
		/*'rh_override' => [
			'label' => 'Allow these settings to be overridden for individual posts of type',
			'type' => 'checkbox',
			'default' => '1'
		],*/
		'rh_behavior' => [
			'label' => 'Behavior',
			'type' => 'select',
			'default' => 'access_denied',
		],
		'rh_url_redirect' => [
			'label' => 'URL to redirect to',
			'type' => 'text',
			'default' => '',
		]
	];
}

/**
 * @return void
 */
function rabbit_hole_plugin_section_callback() {
	echo ''; // Todo: add a description of the section.
}

/**
 * @param $args
 * @return void
 */
function rabbit_hole_plugin_field_callback( $args ) {
	// Get the value store in the databadbase for the field.
	$field_value = get_option( $args['field_id'] );
	$args['default_value'] = $field_value;

	$field_callback = 'rabbit_hole_plugin_field_' . $args['field_name'] . '_callback';
	echo $field_callback( $args );
}

/**
 * @param $args
 * @return void
 */
function rabbit_hole_plugin_field_rh_override_callback( $args ) {
	$args['default_value'] = ($args['default_value']) ? 'checked' : '';
	return render_template($args['field_name'], $args);
}

/**
 * @param $args
 * @return void
 */
function rabbit_hole_plugin_field_rh_behavior_callback( $args ) {
	return render_template($args['field_name'], $args);
}

/**
 * @param $args
 * @return void
 */
function rabbit_hole_plugin_field_rh_url_redirect_callback( $args ) {
	return render_template($args['field_name'], $args);
}

/**
 * Helper function to render the HTML from a template file.
 * @param $template_name
 * @param $template_data
 * @return false|string
 */
function render_template( $template_name, $template_data ) {
	$template_path = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name . '.php';
	$output = '';
	if ( file_exists( $template_path ) ) {
		ob_start();
		include( $template_path );
		$output = ob_get_clean();
	}

	return $output;
}

/**
 * @return void
 */
function rabbit_hole_404_and_403_event() {
	global $post;
	$type = get_post_type( $post );

	if (!rabbit_hole_can_we_process_page()) {
		return;
	}
	global $wp_query;

	$behavior = get_option( get_field_id_by_post_type($type, 'rh_behavior') );
	switch (strtolower($behavior)) {
		case 'page not found':
			$wp_query->set_404();
			status_header(404);
			break;

		case 'access denied':
			status_header(403);
			get_template_part('403');
			exit;
		default:
			break;
	}
}
add_action( 'wp', 'rabbit_hole_404_and_403_event' );

/**
 * helper function to get the id for the admin fields.
 * @param $post_type
 * @param $field_name
 * @return string
 */
function get_field_id_by_post_type($post_type, $field_name) {
	return 'rabbit_hole_plugin_field_' . $post_type . '_' . $field_name;
}

/**
 * @return void
 */
function redirect_post_type_single() {
	global $post;
	$type = get_post_type( $post );

	if (!rabbit_hole_can_we_process_page()) {
		return;
	}

	$behavior = get_option( get_field_id_by_post_type($type, 'rh_behavior') );

	if (strtolower($behavior) == 'page redirect') {
		$url = get_option( get_field_id_by_post_type($type, 'rh_url_redirect') );
		wp_redirect($url, 301);
		exit;
	}
}
add_action( 'template_redirect', 'redirect_post_type_single' );

/**
 * @return bool
 */
function rabbit_hole_can_we_process_page() {
	global $post;
	$type = get_post_type( $post );

	if (is_singular($type)) {
		return TRUE;
	}

	return FALSE;
}
