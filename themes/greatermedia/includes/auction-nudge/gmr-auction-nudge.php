<?php
/**
 * Auction Nudge Ebay Widget
 *
 * Provides a shortcode to add an Auction Nudge Ebay Widget to a post.
 */

namespace Greater_Media\Auction_Nudge;

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GMR_AUCTION_NUDGE_VERSION', '1.0.0' );
define( 'GMR_AUCTION_NUDGE_PATH', dirname( __FILE__ ) );
define( 'GMR_AUCTION_NUDGE_URL', trailingslashit( get_template_directory_uri() ) . 'includes/auction-nudge/' );

include trailingslashit( GMR_AUCTION_NUDGE_PATH ) . 'includes/ShortcodeHandler.php';

new \Greater_Media\Auction_Nudge\ShortcodeHandler();
