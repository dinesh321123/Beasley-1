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
		
		if ( is_singular( ShowsCPT::get_instance()->get_supported_post_types() ) ) {
			$id = get_the_ID();
			$id = empty( $id ) ? get_queried_object_id() : $id; // In case we haven't gotten to the loop yet (ex: in the header)
			$show_terms = wp_get_object_terms( $id, ShowsCPT::SHOW_TAXONOMY );
			$show_term_slugs = wp_list_pluck( $show_terms, 'slug' );

			if ( in_array( $show, $show_term_slugs ) ) {
				return true;
			}
		}

	}

	// Get the show query var, so we can tell if we are on one of the show sections at all
	$show_qv = get_query_var( 'show' );

	// Were only checking for shows in general, not a specific show, if $show is null, so just make sure that there is a show QV set, which means we're on a show section
	if ( empty( $show ) && ! empty( $show_qv ) ) {
		return true;
	}

	// Check if the specific show we are targeting matches our current show section
	if ( $show_qv == $show ) {
		return true;
	}

	return false;
}
