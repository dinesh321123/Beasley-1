<?php

namespace GreaterMedia\HomepageCuration;

add_action( 'admin_menu', __NAMESPACE__ . '\add_settings_page' );
add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_scripts' );

add_filter( 'option_page_capability_homepage-curation', __NAMESPACE__ . '\get_capability' );

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
	$homepage_curation_post_types = apply_filters( 'gmr-homepage-curation-post-types', array( 'post', 'page', 'tribe_events' ) );

	// Fetch restricted post ids
	$query = new \WP_Query();
	$restricted_posts = $query->query( array(
		'post_type'           => $homepage_curation_post_types,
		'post_status'         => 'any',
		'posts_per_page'      => 50,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
		'meta_query'          => array(
			'relation' => 'OR',
			array(
				'key'     => 'post_age_restriction',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'post_login_restriction',
				'compare' => 'EXISTS',
			),
		),
	) );

	// Homepage Featured
	$option_name = 'gmr-homepage-featured';
	$render_args = array(
		'name' => $option_name,
		'pf_options' => array(
			'args' => array(
				'post_type' => $homepage_curation_post_types,
				'meta_key'  => '_thumbnail_id', // Forces the posts to have a featured image
				'exclude'   => $restricted_posts,
			),
			'limit' => 4,
		),
	);
	add_settings_field( $option_name, 'Featured', __NAMESPACE__ . '\render_post_finder', get_settings_page_slug(), get_settings_section(), $render_args );
	register_setting( get_settings_section(), $option_name, __NAMESPACE__ . '\sanitize_post_finder' );


	// Community Highlights
	$option_name = 'gmr-homepage-community';
	$render_args = array(
		'name' => $option_name,
		'pf_options' => array(
			'args' => array(
				'post_type' => $homepage_curation_post_types,
				'meta_key'  => '_thumbnail_id', // Forces the posts to have a featured image
				'exclude'   => $restricted_posts,
			),
			'limit' => 3,
		),
	);
	add_settings_field( $option_name, 'Don\'t Miss', __NAMESPACE__ . '\render_post_finder', get_settings_page_slug(), get_settings_section(), $render_args );
	register_setting( get_settings_section(), $option_name, __NAMESPACE__ . '\sanitize_post_finder' );


	// Fetch restricted post ids
	$query = new \WP_Query();
	$future_events = $query->query( array(
		'post_type'           => 'tribe_events',
		'post_status'         => array( 'publish', 'future', 'private' ),
		'posts_per_page'      => 2,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
		'suppress_filters'    => true, // have to suppress filters otherwise it won't work
		'meta_key'            => '_EventStartDate',
		'meta_type'           => 'DATETIME',
		'orderby'             => 'meta_value',
		'order'               => 'ASC',
		'meta_query'          => array(
			array(
				'key'     => '_EventStartDate',
				'value'   => current_time( 'mysql' ),
				'type'    => 'DATETIME',
				'compare' => '>',
			),
		),
	) );

	// Events - This section is optional - Either curated, or falls back. If you only curate one, we only show one. May be nice in the future to fill up to the max required, but that could also be confusing.
	$option_name = 'gmr-homepage-events';
	$render_args = array(
		'name' => $option_name,
		'pf_options' => array(
			'args' => array(
				'post_type' => array( 'tribe_events' ),
				'include'   => $future_events,
			),
			'limit' => 2,
		),
	);
	add_settings_field( $option_name, 'Events', __NAMESPACE__ . '\render_post_finder', get_settings_page_slug(), get_settings_section(), $render_args );
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
	global $gmr_homepage_curation, $gmr_homepage_curation_position;
	
	$gmr_homepage_curation_position = '2.88';
	$gmr_homepage_curation = add_menu_page( 'Homepage Curation', 'Homepage', 'edit_others_posts', get_settings_page_slug(), __NAMESPACE__ . '\render_homepage_curation', 'dashicons-admin-home', $gmr_homepage_curation_position );
}

function render_homepage_curation() {
	?>
	<div id="homepage-curation" class="wrap">

		<h2>Homepage Curation</h2>

		<?php settings_errors(); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'options.php?option_page=homepage-curation' ) ); ?>">
			<?php
			settings_fields( get_settings_page_slug() ); // the nonce, action, etc
			do_settings_sections( get_settings_page_slug() ); // the actual fields
			submit_button();
			?>
		</form>

	</div>
	<?php
}

function enqueue_admin_scripts( $page ) {
	global $gmr_homepage_curation, $typenow;
	if ( $gmr_homepage_curation == $page || 'show' == $typenow ) {
		wp_enqueue_style( 'homepage-curation', GMEDIA_HOMEPAGE_CURATION_URL . 'css/admin.css', null, GMEDIA_HOMEPAGE_CURATION_VERSION );
		wp_enqueue_script( 'homepage-curation', GMEDIA_HOMEPAGE_CURATION_URL . 'js/curation.js', array( 'jquery' ), GMEDIA_HOMEPAGE_CURATION_VERSION, true );
	}
}

function get_capability( $capability ) {
	global $menu, $gmr_homepage_curation_position;

	if ( $gmr_homepage_curation_position && ! empty( $menu[ $gmr_homepage_curation_position ][1] ) ) {
		$capability = $menu[ $gmr_homepage_curation_position ][1];
	}
	
	return $capability;
}