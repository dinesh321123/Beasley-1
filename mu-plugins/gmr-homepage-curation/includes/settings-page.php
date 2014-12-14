<?php

namespace GreaterMedia\HomepageCuration;

add_action( 'admin_menu', __NAMESPACE__ . '\add_settings_page' );
add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );


/* Define sections, page slugs, etc */
function get_settings_page_slug() {
	return 'homepage-curation';
}

function get_settings_section() {
	return 'homepage-curation';
}


/* The Settings */
function register_settings() {
	// Add our section, so we can actually get these guys to render..
	add_settings_section( get_settings_section(), 'Homepage Curation', '__return_null', get_settings_page_slug() );

	// Can hook into this to add more post types
	$homepage_curation_post_types = apply_filters( 'gmr-homepage-curation-post-types', array( 'post', 'tribe_events' ) );

	// Homepage Featured
	$option_name = 'gmr-homepage-featured';
	$render_args = array(
		'name' => $option_name,
		'pf_options' => array(
			'args' => array(
				'post_type' => $homepage_curation_post_types,
				'meta_key' => '_thumbnail_id', // Forces the posts to have a featured image
			),
			'limit' => 4,
		),
	);
	add_settings_field( $option_name, 'Featured', __NAMESPACE__ . '\render_post_finder', get_settings_page_slug(), get_settings_section(), $render_args );
	register_setting( get_settings_section(), $option_name, __NAMESPACE__ . '\sanitize_post_finder' );


	// Community Highlights
	$option_name = 'gmr-homepage-highlights';
	$render_args = array(
		'name' => $option_name,
		'pf_options' => array(
			'args' => array(
				'post_type' => $homepage_curation_post_types,
				'meta_key' => '_thumbnail_id', // Forces the posts to have a featured image
			),
			'limit' => 3,
		),
	);
	add_settings_field( $option_name, 'Community Highlights', __NAMESPACE__ . '\render_post_finder', get_settings_page_slug(), get_settings_section(), $render_args );
	register_setting( get_settings_section(), $option_name, __NAMESPACE__ . '\sanitize_post_finder' );
}

function render_post_finder( $args ) {
	$defaults = array(
		'name' => false,
		'pf_options' => array(),
	);

	$args = wp_parse_args( $args, $defaults );

	$option_name = $args['name'];

	if ( ! $option_name ) {
		return;
	}

	$current_posts = get_option( $option_name );
	$options = $args['pf_options'];

	\pf_render( $option_name, $current_posts, $options );
}

function sanitize_post_finder( $unsanitized ) {
	$sanitized = implode( ',', array_map( 'intval', explode( ',', $unsanitized ) ) );

	return $sanitized;
}


/* The settings page */
function add_settings_page() {
	add_menu_page( 'Homepage Curation', 'Homepage Curation', 'edit_others_posts', get_settings_page_slug(), __NAMESPACE__ . '\render_homepage_curation', 'dashicons-admin-home', '2.88' );
}

function render_homepage_curation() {
	?>
	<div class="wrap">

		<h2>Homepage Curation</h2>

		<?php settings_errors(); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php
			settings_fields( get_settings_page_slug() ); // the nonce, action, etc
			do_settings_sections( get_settings_page_slug() ); // the actual fields
			submit_button();
			?>
		</form>

	</div>
	<?php
}
