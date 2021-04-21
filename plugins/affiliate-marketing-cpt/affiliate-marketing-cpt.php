<?php
/*
Plugin Name: Affiliate Marketing CPT
Description: Custom Post Type - CSS tweak for photo captions/affiliate marketing page
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'AFFILIATE_MARKETING_CPT_VERSION', '0.0.1' );
define( 'AFFILIATE_MARKETING_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'AFFILIATE_MARKETING_CPT_PATH', dirname( __FILE__ ) );
define( 'AFFILIATE_MARKETING_CPT_TEXT_DOMAIN', 'affiliate_marketing_textdomain' );

include __DIR__ . '/includes/post-types.php';
include __DIR__ . '/includes/affiliatemarketing-metaboxes.php';
include __DIR__ . '/includes/rendering.php';

register_activation_hook( __FILE__, 'affiliate_marketing_cpt_activated' );
register_deactivation_hook( __FILE__, 'affiliate_marketing_cpt_deactivated' );

function affiliate_marketing_cpt_activated() {
	\AffiliateMarketingCPT::affiliate_cpt_init();

	load_capabilities( AffiliateMarketingCPT::AFFILIATE_MARKETING_POST_TYPE );
}

function affiliate_marketing_cpt_deactivated() {
	unload_capabilities( AffiliateMarketingCPT::AFFILIATE_MARKETING_POST_TYPE );
}
