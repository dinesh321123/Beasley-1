<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<?php ee_the_subtitle( 'Podcasts' ); ?>

	<div><?php
		$query = \GreaterMedia\Shows\get_show_podcast_query();
		if ( $query->have_posts() ) :
			?><div class="archive-tiles">
				<?php ee_the_query_tiles( $query ); ?>
			</div><?php

			ee_load_more( $query );
		endif;
	?></div>
</div>

<?php get_footer(); ?>