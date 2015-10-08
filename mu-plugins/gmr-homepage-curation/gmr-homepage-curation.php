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
	if ( ! current_theme_supports( 'homepage-curation' ) ) {
		return;
	}

	include __DIR__ . '/includes/settings-page.php';
	include __DIR__ . '/includes/queries.php';
}
\add_action ( 'after_setup_theme', __NAMESPACE__ . '\load' );
