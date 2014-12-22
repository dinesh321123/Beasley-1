<?php

namespace GreaterMedia\AdCodeManager;

add_filter( 'acm_ad_tag_ids', __NAMESPACE__ . '\filter_ad_tags' );

function filter_ad_tags() {
	$ad_tags = array(
		'leaderboard-top-of-site' => array(
			'tag' => 'leaderboard-top-of-site',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
			'min_width' => 728,
		),
		'leaderboard-body' => array(
			'tag' => 'leaderboard-body',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
			'min_width' => 728,
		),
		'live-links-rectangle' => array(
			'tag' => 'live-links-rectangle',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		'mrec-body' => array(
			'tag' => 'mrec-body',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		'mrec-lists' => array(
			'tag' => 'mrec-lists',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		'smartphone-wide-banner' => array(
			'tag' => 'smartphone-wide-banner',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
			'max_width' => 727, // since the main leaderboard comes in at 728
		),
	);

	return $ad_tags;
}

function get_ad_tag_meta( $tag_id ) {
	$tags = filter_ad_tags();

	if ( ! isset( $tags[ $tag_id ] ) ) {
		return false;
	}

	return $tags[ $tag_id ];
}
