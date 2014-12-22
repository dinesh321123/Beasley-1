<?php

namespace GreaterMedia\AdCodeManager;

add_filter( 'acm_ad_tag_ids', __NAMESPACE__ . '\filter_ad_tags' );

function filter_ad_tags() {
	$ad_tags = array(
		array(
			'tag' => 'leaderboard-top-of-site',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		array(
			'tag' => 'leaderboard-body',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		array(
			'tag' => 'live-links-rectangle',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		array(
			'tag' => 'mrec-body',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		array(
			'tag' => 'mrec-lists',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
		array(
			'tag' => 'smartphone-wide-banner',
			'url_vars' => array(),
			'enable_ui_mapping' => true,
		),
	);

	return $ad_tags;
}
