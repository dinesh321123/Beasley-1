<?php
/*
 * This file is NOT in the GreaterMedia\AdCodeManager namspace, because this would make the conditionals list incredibly long and ugly
 */


add_filter( 'acm_whitelisted_conditionals', 'gmr_filter_acm_conditionals' );

function gmr_filter_acm_conditionals( $conditionals ) {
	$new_conditionals = array(
		'gmr_is_show',
	);

	return array_merge( $conditionals, $new_conditionals );
}

function gmr_is_show( $show = null ) {
	if ( ! class_exists( 'ShowsCPT' ) ) {
		return false;
	}

	if ( get_post_type() !== ShowsCPT::SHOW_CPT ) {
		return false;
	}

	// Were only checking for shows in general, not a specific show
	if ( is_null( $show ) ) {
		return true;
	}

	if ( get_query_var( 'show' ) == $show ) {
		return true;
	}

	return false;
}
