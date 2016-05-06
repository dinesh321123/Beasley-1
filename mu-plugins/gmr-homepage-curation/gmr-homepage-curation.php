<?php
/**
 * Homepage Curation Functionality
 *
 * Themes *must* declare support for homepage curation using add_theme_support( 'homepage-curation' )
 */

namespace GreaterMedia\HomepageCuration;

define( 'GMEDIA_HOMEPAGE_CURATION_VERSION', '1.0.0' );
define( 'GMEDIA_HOMEPAGE_CURATION_URL', plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_HOMEPAGE_CURATION_PATH', plugin_dir_path( __FILE__ ) );

function load() {
	if ( ! current_theme_supports( 'homepage-curation' ) ) {
		return;
	}

	require GMEDIA_HOMEPAGE_CURATION_PATH . '/includes/functions.php';
	include __DIR__ . '/includes/homepage_cpt.php';
	include __DIR__ . '/includes/queries.php';
	require GMEDIA_HOMEPAGE_CURATION_PATH . '/includes/homepage-exclude.php';
}
\add_action ( 'after_setup_theme', __NAMESPACE__ . '\load' );
