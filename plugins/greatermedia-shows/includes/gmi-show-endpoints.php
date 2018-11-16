<?php

namespace GreaterMedia\Shows;

add_action( 'init', __NAMESPACE__ . '\add_show_section_tag' );
add_action( 'init', __NAMESPACE__ . '\add_rewrites' );
add_action( 'template_redirect', __NAMESPACE__ . '\template_redirect' );
add_action( 'template_include', __NAMESPACE__ . '\filter_template' );

/**
 * Returns array of valid subsections of the shows post type.
 *
 * @return array
 */
function get_sections() {
	return array( 'about', 'podcasts', 'galleries', 'videos', 'live-links' );
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
		$rule = sprintf( '%1$s/([^/]+)/(%2$s)(/page/([0-9]+))?/?$', $rewrite_base, $section );
		\add_rewrite_rule( $rule, sprintf( 'index.php?%1$s=$matches[1]&show_section=$matches[2]&paged=$matches[4]', $post_type ) , 'top' );
	}
}

/**
 * Checks if we should be allowing access to this show's homepage or not, based on the settings on the show page in wp-admin.
 *
 * Checks for Homepage support (applies to about also), in addition to Galleries, Podcasts, and Videos.
 */
function template_redirect() {
	if ( is_post_type_archive( \ShowsCPT::SHOW_CPT ) ) {
		return;
	}

	if ( get_post_type() !== \ShowsCPT::SHOW_CPT || ! is_singular( \ShowsCPT::SHOW_CPT ) ) {
		return;
	}

	$section = get_query_var( 'show_section' );
	if ( $section && in_array( $section, get_sections() ) ) {
		switch( $section ) {
			case 'galleries':
				$allowed = supports_galleries( get_the_ID() );
				break;
			case 'podcasts':
				$allowed = supports_podcasts( get_the_ID() );
				break;
			case 'videos':
				$allowed = supports_videos( get_the_ID() );
				break;
			case 'about':
				// no break
			case 'live-links':
				// no break
			default:
				// These are just allowed through, as long as homepage is enabled, which it is if we're this far
				$allowed = true;
				break;
		}

		if ( ! $allowed ) {
			block_show_access();
			return;
		}
	}
}

/**
 * Blocks access to the current page by setting a 404 and clearing the show_section.
 *
 * Clearing the section is required, otherwise we will end up overridding the 404 template in the filter_tempalte function below
 */
function block_show_access() {
	global $wp_query;

	if ( get_post_type() !== \ShowsCPT::SHOW_CPT ) {
		return;
	}

	$wp_query->set_404();
	$wp_query->set( 'show_section', null );
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
