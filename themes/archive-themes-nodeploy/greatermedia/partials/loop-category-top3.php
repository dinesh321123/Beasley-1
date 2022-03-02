<?php

global $wp_query;

$posts_per_page     = 10;
$posts_between_ads  = 6;

$current_page       = intval( $wp_query->query_vars['paged'] );
$current_page       = max( 1, $current_page );
$current_post_index = ( ( $current_page - 1 ) * $posts_per_page ) + 1; ?>
<section class="top-three__features">
<?php if ( have_posts() ) : the_post();

	get_template_part( 'partials/top-three', 'featured-primary' );

	$count = 0;
	if ( have_posts() ) :
		while ( have_posts() && $count < 2 ) : the_post();
			$count++;
			get_template_part( 'partials/top-three', 'featured-secondary' );
		endwhile; ?>
	</section>
	<section class="top-three__blogroll">
		<?php while ( have_posts() && $count >= 2 ) : the_post();
			get_template_part( 'partials/entry', get_post_type() );
		endwhile;
		greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop', 'auto_load' => false ) );
		?>
	</section>
	<?php endif;

	if ( $current_post_index % $posts_between_ads === 0 ) {
		get_template_part( 'partials/ad-in-loop' );
	}

	$current_post_index++;
endif; ?>