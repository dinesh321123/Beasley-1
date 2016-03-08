<?php
/**
 * Flexible Feature Images
 *
 * Provides different layout options for the feature image on posts and certain custom post types.
 */

namespace Greater_Media\Flexible_Feature_Images;

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GMR_FLEXIBLE_FEATURE_IMAGES_VERSION', '1.0.0' );
define( 'GMR_FLEXIBLE_FEATURE_IMAGES_PATH', dirname( __FILE__ ) );
define( 'GMR_FLEXIBLE_FEATURE_IMAGES_URL', trailingslashit( get_template_directory_uri() ) . 'includes/flexible-feature-images/' );

include trailingslashit( GMR_FLEXIBLE_FEATURE_IMAGES_PATH ) . 'includes/class-flexible-feature-images.php';
include trailingslashit( GMR_FLEXIBLE_FEATURE_IMAGES_PATH ) . 'includes/functions.php';
