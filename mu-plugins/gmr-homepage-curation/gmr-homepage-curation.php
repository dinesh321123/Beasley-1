<?php
/**
 * Homepage Curation Functionality
 *
 * Themes *must* declare support for homepage curation using add_theme_support( 'homepage-curation' )
 */

namespace GreaterMedia\HomepageCuration;

define( 'GMEDIA_HOMEPAGE_CURATION_VERSION', '1.0.0' );
define( 'GMEDIA_HOMEPAGE_CURATION_URL', plugin_dir_url( __FILE__ ) );

function load() {
	if ( current_theme_supports( 'homepage-curation' ) ) {
		require __DIR__ . '/includes/homepage_cpt.php';
		require __DIR__ . '/includes/queries.php';
		require __DIR__ . '/includes/homepage-exclude.php';
//		require __DIR__ . '/includes/feeds/current-homepage.php';
//		require __DIR__ . '/includes/feeds/current-homepage-featured.php';
	}
}

add_action ( 'after_setup_theme', __NAMESPACE__ . '\load', 50 );

/**
 * Loads the specified template with variables scoped to the template.
 *
 * @param string $name Name of the template file to include including extension.
 * @param array  $args Associative array of arguments that will be extracted into the template's scope.
 */
function load_template( $name, $args = [] ) {
	$file_path = __DIR__ . '/templates/' . $name;
	if ( file_exists( $file_path ) ) {
		extract( $args );
		require $file_path;
	}
}

/**
 * Renders homepage template based on current homepage settings.
 */
function do_frontpage_highlights() {
	$homepage = get_current_homepage();
	if ( ! $homepage ) {
		return;
	}

	$template = get_page_template_slug( $homepage->ID );
	if ( empty( $template ) ) {
		$template = is_news_site()
			? 'page-templates/homepage-news.php'
			: 'page-templates/homepage-music.php';
	}

	if ( ! empty( $template ) ) {
		locate_template( $template, true, false );
	}
}
add_action( 'do_frontpage_highlights', __NAMESPACE__ . '\do_frontpage_highlights' );