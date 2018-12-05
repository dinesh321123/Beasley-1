<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ee_is_first_page() ) : ?>
		<?php get_template_part( 'partials/show/header' ); ?>

		<div class="content-wrap">
			<h1><?php the_title(); ?></h1>
		</div>
	<?php endif; ?>

	<div class="content-wrap"><?php
		get_template_part( 'partials/ads/sidebar-sticky' );

		if ( ee_is_first_page() ) :
			get_template_part( 'partials/content/meta' );
			get_template_part( 'partials/featured-media' );
			ee_the_content_with_ads();

			ee_the_subtitle( 'Galleries' );
		endif;

		$query = ee_get_galleries_query( null, 'paged=' . get_query_var( 'paged' ) );
		if ( $query->have_posts() ) :
			?><div class="archive-tiles">
				<?php ee_the_query_tiles( $query ); ?>
			</div><?php

			ee_load_more( $query );
		else :
			ee_the_have_no_posts();
		endif;
	?></div>
</div>

<?php get_footer(); ?>
