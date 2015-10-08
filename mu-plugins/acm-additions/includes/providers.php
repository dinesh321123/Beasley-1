<?php

namespace GreaterMedia\AdCodeManager;

add_filter( 'acm_register_provider_slug', __NAMESPACE__ . '\filter_providers' );
add_filter( 'acm_provider_slug', __NAMESPACE__ . '\filter_provider' );

function filter_providers( $providers ) {
	$custom_providers = new \stdClass();

	if ( class_exists( 'ACM_Provider' ) ) {
		require_once __DIR__ . '/providers/class-openx-acm-provider.php';
		require_once __DIR__ . '/providers/class-openx-acm-wp-list-table.php';

		$custom_providers->openx = array(
			'provider' => __NAMESPACE__ . '\OpenX_ACM_Provider',
			'table' => __NAMESPACE__ . '\OpenX_ACM_WP_List_Table',
		);
	}

	return $custom_providers;
}

function filter_provider() {
	return 'openx';
}
