<?php
if ( ! class_exists( 'ListicleCPTFrontRendering' ) ) :
	return;
endif;
$cpt_post_object = get_queried_object();

	$cpt_item_name 			=	\ListicleCPTFrontRendering::get_post_metadata_from_post( 'cpt_item_name', $cpt_post_object );
		if ( ! is_array( $cpt_item_name ) ) :
			$cpt_item_name = array();
		endif;
	$cpt_item_description 	= \ListicleCPTFrontRendering::get_post_metadata_from_post( 'cpt_item_description', $cpt_post_object );
		if ( ! is_array( $cpt_item_description ) ) :
			$cpt_item_description = array();
		endif;
	$cpt_item_order 	= \ListicleCPTFrontRendering::get_post_metadata_from_post( 'cpt_item_order', $cpt_post_object );
		if ( ! is_array( $cpt_item_order ) ) :
			$cpt_item_order = array();
		endif;
	$cpt_item_type 	= \ListicleCPTFrontRendering::get_post_metadata_from_post( 'cpt_item_type', $cpt_post_object );
		if ( ! is_array( $cpt_item_type ) ) :
			$cpt_item_type = array();
		endif;

echo ee_get_listiclecpt_html(
	$cpt_post_object,
	$cpt_item_name,
	$cpt_item_description,
	$cpt_item_order,
	$cpt_item_type
);
