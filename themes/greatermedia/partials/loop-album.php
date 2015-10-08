<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$page = get_query_var( 'paged' );
if ( empty( $page ) ) {
	$page = 1;
}

$query_args = array(
	'post_type'      => array( 'gmr_album' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
	'posts_per_page' => 100,
	'offset'         => 0,
);

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

?>
<h2 class="section-header"><?php _e( 'Albums', 'greatermedia' ); ?></h2>
<?php

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
			'partial_name' => 'album',
			'auto_load'    => false,
		) );
	endif;

	$gmr_loadmore_num_pages = $query->max_num_pages;
	$gmr_loadmore_post_count = $query->post_count;

endif;
