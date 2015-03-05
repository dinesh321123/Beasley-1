<?php
/**
 * Fallback Thumbnails
 *
 * Provides fallback thumbnails for galleries, albums, and podcasts.
 */

namespace Greater_Media\Fallback_Thumbnails;

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GMR_FALLBACK_THUMBNAILS_VERSION', '1.0.0' );
define( 'GMR_FALLBACK_THUMBNAILS_PATH', dirname( __FILE__ ) );
define( 'GMR_FALLBACK_THUMBNAILS_URL', plugin_dir_url( __FILE__ ) );

include trailingslashit( GMR_FALLBACK_THUMBNAILS_PATH ) . 'includes/class-thumbnail-filter.php';
include trailingslashit( GMR_FALLBACK_THUMBNAILS_PATH ) . 'includes/functions.php';

new Thumbnail_Filter(); 
