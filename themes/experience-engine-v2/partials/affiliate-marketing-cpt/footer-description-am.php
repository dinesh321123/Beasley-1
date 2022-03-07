<?php
if ( ! class_exists( 'AffiliateMarketingCPTFrontRendering' ) ) :
	return;
endif;
$affiliatemarketing_post_object = get_queried_object();
	$am_footer_description	=	\AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_footer_description', $affiliatemarketing_post_object );

echo '<div class="am_footer_description">', $am_footer_description, '</div>';
