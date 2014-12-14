<?php
/**
 * Homepage Curation Functionality
 *
 * Themes *must* declare support for homepage curation using add_theme_support( 'homepage-curation' )
 */

namespace GreaterMedia\HomepageCuration;

function load() {
	if ( ! current_theme_supports( 'homepage-curation' ) ) {
		return;
	}

	include __DIR__ . '/includes/settings-page.php';
	include __DIR__ . '/includes/queries.php';
}
\add_action ( 'after_setup_theme', __NAMESPACE__ . '\load' );
