<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$page = get_query_var( 'paged' );
if ( empty( $page ) ) {
	$page = 1;
}

$per_page = 16;

$query_args = array(
	'post_type'      => array( 'gmr_gallery' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
	'posts_per_page' => $per_page,
	'offset'         => $per_page * ( $page - 1 ),
);

$featured = greatermedia_get_featured_gallery();
if ( $featured ) {
	$query_args['post__not_in'] = array( $featured->ID );
}

if ( 'show' == get_post_type() ) {
	$term = \TDS\get_related_term( get_the_ID() );
	if ( $term ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => '_shows',
				'field'    => 'slug',
				'terms'    => $term->slug,
			)
		);
	}
}

$query = new WP_Query( $query_args );

if ( $query->have_posts() ) :
	if ( $page == 1 && ! is_post_type_archive( 'gmr_gallery' ) ) :
		?><h2 class="section-header">Galleries</h2><?php
	endif;

	while ( $query->have_posts() ) :
		$query->the_post();
		get_template_part( 'partials/gallery-grid' );
	endwhile;

	wp_reset_query();

	$gmr_loadmore_paged = get_query_var( 'paged', 1 );
	if ( $gmr_loadmore_paged < 2 ) :
		greatermedia_load_more_button( array(
			'query'        => $query,
			'partial_slug' => 'partials/loop',
			'partial_name' => 'gallery',
			'auto_load'    => false,
		) );
	endif;

	$gmr_loadmore_num_pages = $query->max_num_pages;
	$gmr_loadmore_post_count = $query->post_count;

endif;
