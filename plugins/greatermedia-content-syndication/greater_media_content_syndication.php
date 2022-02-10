<?php
/**
 * Plugin Name: Greater Media Content Syndication
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Content Syndication to copy content from Corporate site to station sites
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  http://10up.com
 * License:     GPLv2+
 * Text Domain: gmr_syndication
 * Domain Path: /languages
 */

// Useful global constants
$version = '0.1.0';

// If .version.php file exists, the content of this file (timestamp) is added to the $version value set above
if ( file_exists( WP_CONTENT_DIR . '/themes/.version.php' ) ) {
	$suffix  = intval( file_get_contents( WP_CONTENT_DIR . '/themes/.version.php' ) );
	$version = $version . "." . $suffix;
}
define( 'GMR_SYNDICATION_VERSION', $version );
define( 'GMR_SYNDICATION_URL', plugins_url( '/', __FILE__ ) );

$wp_debug_log_path = str_replace('/themes', '', get_theme_root());
define( 'GMR_SYNDICATION_DEBUG_LOG_PATH', $wp_debug_log_path );
define( 'GMR_SYNDICATION_DEBUG_LOG_DATE', "[ ".date("d-M-Y h:i:sa")." ] SyndicationLogs : " );

if ( file_exists( __DIR__ . '/custom_packages/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/custom_packages/vendor/autoload.php';
}

require_once __DIR__ . '/includes/syndication-cpt.php';
require_once __DIR__ . '/includes/blog-data.php';
require_once __DIR__ . '/includes/cron-tasks.php';
require_once __DIR__ . '/includes/content-kit.php';
require_once __DIR__ . '/includes/instant-syndication.php';
require_once __DIR__ . '/includes/detach-post.php';

if ( defined( 'WP_CLI' ) && WP_CLI  ) {
	require_once __DIR__ . '/includes/CLI/DetachPostIterator.php';

	require_once __DIR__ . '/includes/syndication-cli.php';
}

register_activation_hook( __FILE__, 'gmr_content_syndication_activated' );
register_deactivation_hook( __FILE__, 'gmr_content_syndication_deactivated' );

function gmr_content_syndication_activated() {
	$content_kit = new \ContentKit();
	$content_kit->register_content_kit_cpt();

	$syndication = new \SyndicationCPT();
	$syndication->register_syndication_cpt();

	load_capabilities( 'content-kit' );
	load_capabilities( 'subscription' );

	flush_rewrite_rules();
}

function gmr_content_syndication_deactivated() {
	unload_capabilities( 'content-kit' );
	unload_capabilities( 'subscription' );

	flush_rewrite_rules();
}
