<?php

get_header();

echo '<div class="', join( ' ', get_post_class() ), '">';
	if ( ee_is_first_page() ):
		get_template_part( 'partials/author/header' );
	endif;
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$current_page = get_query_var( 'paged' ) ?: 1;
	$args = array(
			'post_type'		=> array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'),
			'author'		=> $author->ID, //Author ID
			'post_status' => 'publish',
			'paged'			=> get_query_var( 'paged' ),
			'posts_per_page'=> '3'
	);
	// echo "<pre>", print_r($args), "</pre>"; exit;
	$author_query = new WP_Query( $args );

	if ( $author_query->have_posts() ) {
		echo '<div class="archive-tiles content-wrap ', ! is_post_type_archive( 'contest' ) && ! is_post_type_archive( 'tribe_events' ) ? '-grid -large' : '-list', '">';
		while ( $author_query->have_posts() ) {
			$author_query->the_post();
			// echo get_the_title(); ?>
			<div data-post-id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part( 'partials/tile/thumbnail' ); ?>
				<?php get_template_part( 'partials/tile/title' ); ?>
			</div>
			<?php
		}
		echo '</div>';
		echo '<div class="content-wrap">';
			ee_load_more( $author_query );
		echo '</div>';
	} else {
		echo '<div class="content-wrap">';
			ee_the_have_no_posts();
		echo '</div>';
	}
wp_reset_postdata();

/* if ( have_posts() ) :
	echo '<div class="archive-tiles content-wrap ', ! is_post_type_archive( 'contest' ) && ! is_post_type_archive( 'tribe_events' ) ? '-grid -large' : '-list', '">';
	while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/tile', get_post_type() );
	endwhile;
	echo '</div>';

	echo '<div class="content-wrap">';
	ee_load_more();
	echo '</div>';
else :
	echo '<div class="content-wrap">';
	ee_the_have_no_posts();
	echo '</div>';
endif; */

echo '</div>';

get_footer();
