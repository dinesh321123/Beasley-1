<?php
if ( ! class_exists( 'AffiliateMarketingCPTFrontRendering' ) ) :
	return;
endif;
$affiliatemarketing_post_object = get_queried_object();

	$am_item_name 			=	\AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_name', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_name ) ) :
			$am_item_name = array();
		endif;
	$am_item_description 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_description', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_description ) ) :
			$am_item_description = array();
		endif;
	$am_item_photo 			= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_photo', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_photo ) ) :
			$am_item_photo = array();
		endif;
	$am_item_imagetype 		= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_imagetype', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_imagetype ) ) :
			$am_item_imagetype = array();
		endif;
	$am_item_imagecode 		= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_imagecode', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_imagecode ) ) :
			$am_item_imagecode = array();
		endif;
	$am_item_order 			= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_order', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_order ) ) :
			$am_item_order = array();
		endif;
	$am_item_unique_order 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_unique_order', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_unique_order ) ) :
			$am_item_oram_item_unique_orderder = array();
		endif;
	$am_item_getitnowtext	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_getitnowtext', $affiliatemarketing_post_object );
	if ( ! is_array( $am_item_getitnowtext ) ) :
		$am_item_getitnowtext = array();
	endif;
	$am_item_buttontext 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_buttontext', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_buttontext ) ) :
			$am_item_buttontext = array();
		endif;
	$am_item_buttonurl 		= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_buttonurl', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_buttonurl ) ) :
			$am_item_buttonurl = array();
		endif;
	$am_item_getitnowfromname 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_getitnowfromname', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_getitnowfromname ) ) :
			$am_item_getitnowfromname = array();
		endif;
	$am_item_getitnowfromurl 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_getitnowfromurl', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_getitnowfromurl ) ) :
			$am_item_getitnowfromurl = array();
		endif;
	$am_item_type 	= \AffiliateMarketingCPTFrontRendering::get_post_metadata_from_post( 'am_item_type', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_type ) ) :
			$am_item_type = array();
		endif;

echo ee_get_affiliatemarketing_html(
	$affiliatemarketing_post_object,
	$am_item_name,
	$am_item_description,
	$am_item_photo,
	$am_item_imagetype,
	$am_item_imagecode,
	$am_item_order,
	$am_item_unique_order,
	$am_item_getitnowtext,
	$am_item_buttontext,
	$am_item_buttonurl,
	$am_item_getitnowfromname,
	$am_item_getitnowfromurl,
	$am_item_type
);
