<?php

get_header();
wp_reset_query();

if ( ee_is_first_page() ):
	get_template_part( 'partials/category/meta' );
endif;

$ca_obj = get_queried_object();
$ca_data = ee_get_category_archive_details( $ca_obj );
$ca_posts_query = $ca_data['query'];
$ca_featured = $ca_data['featured'];
$ca_posts = !empty($ca_data['query']) ? $ca_data['query']->posts : array();

if ( ( count($ca_posts) < 1 ) && ( count($ca_featured) < 1 ) ) {
	ee_ca_no_post();
}

if ( ee_is_first_page() ) {
	$ca_posts = array_merge($ca_featured, $ca_posts);
	if( count($ca_posts) > 0 ) {
		$ca_featured_section_posts = array_slice($ca_posts, 0, 5);
		set_query_var( 'featured_posts', $ca_featured_section_posts );
		get_template_part( 'partials/category/featured' );
	}
	$ca_posts = array_slice($ca_posts, 5);
}

ee_ca_get_articles( $ca_obj, $ca_posts, $ca_data );

ee_load_more_ca( $ca_posts_query );

get_footer();