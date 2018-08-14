<?php
/*
 * Plugin Name: Greater Media Must Use Plugins
 * Description: Plugins that are critical to the Greater Media Site
 * Author: 10up
 * Author URI: http://10up.com
 *
 * IMPORTANT: Not everything should be added to mu-plugins. Before adding anything here, please check with Chris or Dave
 */

define( 'ACF_LITE', true );

include __DIR__ . '/term-data-store/term-data-store.php';
include __DIR__ . '/visual-shortcode/visual-shortcode.php';
include __DIR__ . '/dependencies/dependencies.php';
include __DIR__ . '/post-finder/post-finder.php';
//include __DIR__ . '/force-frontend-http/force-frontend-http.php';
include __DIR__ . '/capabilities/capabilities.php';
include __DIR__ . '/edit-flow-notification-block/edit-flow-notification-block.php';

// These are going to be activated no matter what to ensure that themes can always rely on the functionality
include __DIR__ . '/gmr-template-tags/gmr-template-tags.php';
include __DIR__ . '/gmr-homepage-curation/gmr-homepage-curation.php';
include __DIR__ . '/legacy-redirects/class-CMM_Legacy_Redirects.php';
include __DIR__ . '/gmr-fallback-thumbnails/gmr-fallback-thumbnails.php';
include __DIR__ . '/gmr-mobile-homepage-curation/gmr-mobile-homepage-curation.php';
include __DIR__ . '/advanced-custom-fields/acf.php';
include __DIR__ . '/async-thumbnail-regeneration/async-thumbnail-regeneration.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include __DIR__ . '/cli/cli.php';
}

// Allows overriding options with constants
add_filter( 'configure_smtp__options', function( $options ) {
	/*
	 *  Set any of the following values in the constant, ONLY if you want to override the UI defined values
	 *
	 *  [use_gmail] =>
	 *  [host] => localhost
	 *  [port] => 25
	 *  [smtp_secure] => None
	 *  [smtp_auth] =>
	 *  [smtp_user] =>
	 *  [smtp_pass] =>
	 *  [wordwrap] =>
	 *  [debug] =>
	 *  [from_email] =>
	 *  [from_name] =>
	 *  [_version] => 3.1
	 */
	if ( defined('CONFIGURE_SMTP_OVERRIDES' ) && is_array( CONFIGURE_SMTP_OVERRIDES ) ) {
		$options = wp_parse_args( CONFIGURE_SMTP_OVERRIDES, $options );
	}

	return $options;
} );

// Force URLs in srcset attributes into HTTPS scheme.
add_filter( 'wp_calculate_image_srcset', function( $sources ) {
	if ( ! is_ssl() ) {
		return $sources;
	}

	if ( ! is_array( $sources ) ) {
		return $sources;
	}

	foreach ( $sources as &$source ) {
		if ( isset( $source['url'] ) ) {
			$source['url'] = set_url_scheme( $source['url'], 'https' );
		}
	}

	return $sources;
} );

// Filters the IP address passed to Restricted Site Access so use IP address provided by CloudFlare if available.
add_filter( 'restricted_site_access_remote_ip', function( $remote_ip ) {
	// lets use IP address from CloudFlare if available
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && filter_var( $_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP ) ) {
		return $_SERVER['HTTP_CF_CONNECTING_IP'] ;
	}

	return $remote_ip;
} );

/**
 * Tribe added a new "Chunker" that tries to split post meta among many keys, and automatically combine when querying
 * This basically killed our database, and didn't ACTUALLY end up splitting anything, because our infrastructure
 * is actually capable of storing lots of data.
 *
 * Disabling this new functionality by removing support from all the post types.
 *
 * Verified this fixed the excessive CPU load as well.
 *
 * 5/22/2017 - Chris Marslender
 */
add_filter( 'tribe_meta_chunker_post_types', '__return_empty_array', 15 );

/**
 * Since media is from S3, the default domain won't be replaced automatically, unless we overwrite it
 */
add_filter( 'dynamic_cdn_site_domain', function ( $domain ) {
	if ( defined( 'DYNAMIC_CDN_SITE_DOMAIN' ) ) {
		return DYNAMIC_CDN_SITE_DOMAIN;
	}

	return 'files.greatermedia.com.s3.amazonaws.com';
} );

add_filter( 'dynamic_cdn_extensions', function ( $extensions ) {
	$extensions = array_merge( $extensions, array( 'mp3', 'ogg', 'wma', 'm4a', 'wav' ) );

	return $extensions;
} );

// Filters the post types that will be indexed by ElasticPress.
add_filter( 'ep_indexable_post_types', function() {
	// Index all post types that are not excluded from search
	return get_post_types( array( 'exclude_from_search' => false ) );
} );
