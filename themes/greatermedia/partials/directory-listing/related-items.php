<?php

$post = get_queried_object();
$args = array(
	'post_type'           => 'listing',
	'posts_per_page'      => 5,
	'no_found_rows'       => true,
	'ignore_sticky_posts' => true,
	'post__not_in'        => array( $post->ID ),
);

$category = current( wp_get_post_terms( $post->ID, 'listing-category' ) );
if ( is_a( $category, '\WP_Term' ) ) :
	$args['tax_query'] = array(
		array(
			'taxonomy' => $category->taxonomy,
			'terms'    => array( $category->term_id ),
		),
	);
endif;

$query = new \WP_Query( $args );
if ( ! $query->have_posts() ) :
	return;
endif;

?><div class="directory-related">
	<h4 class="directory-related__title">Related posts</h4>
	<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<?php get_template_part( 'partials/directory-listing/list-item' ); ?>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
</div>
