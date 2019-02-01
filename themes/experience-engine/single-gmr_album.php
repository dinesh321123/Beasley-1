<?php

get_header();

ee_switch_to_article_blog();
the_post(); 

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ee_is_first_page() ) : ?>
		<?php get_template_part( 'partials/show/header' ); ?>

		<div class="content-wrap album-header">
			<h1 class="album-title"><?php the_title(); ?></h1>
			<?php if ( ee_is_first_page() ) :
				get_template_part( 'partials/content/meta' );
				get_template_part( 'partials/featured-media' );
			endif; ?>
		</div>
	<?php endif; ?>

	<div class="entry-content content-wrap"><?php
		if ( ee_is_first_page() ) :
			get_template_part( 'partials/ads/sidebar-sticky' );
			ee_the_subtitle( 'Galleries' );
		endif;

		$query = ee_get_galleries_query( null, 'paged=' . get_query_var( 'paged' ) );
		if ( $query->have_posts() ) :
			?><div class="archive-tiles -grid -large">
				<?php ee_the_query_tiles( $query ); ?>
			</div><?php

			ee_load_more( $query );
		else :
			ee_the_have_no_posts();
		endif;
	?></div>
</div><?php

restore_current_blog();
get_footer();
