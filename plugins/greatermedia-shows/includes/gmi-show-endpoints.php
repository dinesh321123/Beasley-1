<?php

namespace GreaterMedia\Shows;

add_action( 'init', __NAMESPACE__ . '\add_show_section_tag' );
add_action( 'init', __NAMESPACE__ . '\add_rewrites' );
add_action( 'template_include', __NAMESPACE__ . '\filter_template' );

/**
 * Returns array of valid subsections of the shows post type.
 *
 * @return array
 */
function get_sections() {
	return array( 'about', 'podcasts', 'albums', 'videos' );
}

/**
 * Registers the show_section rewrite tag with WordPress so we can use it in our rewrite rules.
 */
function add_show_section_tag() {
	add_rewrite_tag( '%show_section%', '([^&]+)' );
}

/**
 * Adds the actual rewrite rules for the valid subsections of shows.
 */
function add_rewrites() {
	$post_type = \ShowsCPT::SHOW_CPT;
	$post_type_obj = \get_post_type_object( $post_type );
	$rewrite_base = $post_type_obj->rewrite['slug'];

	$sections = get_sections();

	foreach( $sections as $section ) {
		$rule = sprintf( '%1$s/([^/]+)/(%2$s)/?$', $rewrite_base, $section );

		\add_rewrite_rule( $rule, sprintf( 'index.php?%1$s=$matches[1]&show_section=$matches[2]&page=$matches[3]', $post_type ) , 'top' );
	}
}

/**
 * Filters the template we are using to also include single-{post_type}-{section} if it is available.
 *
 * Templates will be loaded in the following priority:
 *  - single-show-<section>.php
 *  - single-show.php
 *  - single.php
 *
 * @param string $template Path to the current template that has been selected.
 *
 * @return string The final template that we are going to use.
 */
function filter_template( $template ) {
	$section = get_query_var( 'show_section' );
	if ( $section && in_array( $section, get_sections() ) ) {
		$templates = array();
		$post_type = \ShowsCPT::SHOW_CPT;

		$templates[] = "single-{$post_type}-{$section}.php";
		$templates[] = "single-{$post_type}.php";
		$templates[] = "single.php";

		return get_query_template( 'single', $templates );
	}

	return $template;
}
