<?php

get_header();

the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		get_template_part( 'partials/podcast-information' );

		?><div><?php
			if ( ( $feed_url = ee_get_podcast_meta( null, 'feed_url' ) ) ) :
				?><a href="<?php echo esc_url( $feed_url ); ?>" target="_blank" rel="noopener noreferrer">
					Podcast Feed
				</a><?php
			endif;

			if ( ( $itunes_url = ee_get_podcast_meta( null, 'itunes_url' ) ) ) :
				?><a href="<?php echo esc_url( $itunes_url ); ?>" target="_blank" rel="noopener noreferrer">
					Subscribe in iTunes
				</a><?php
			endif;

			if ( ( $google_play_url = ee_get_podcast_meta( null, 'google_play_url' ) ) ) :
				?><a href="<?php echo esc_url( $google_play_url ); ?>" target="_blank" rel="noopener noreferrer">
					Subscribe in Google Play
				</a><?php
			endif;

			get_template_part( 'partials/add-to-favorite' );
		?></div><?php
	endif;

	$query = ee_get_episodes_query( null, 'paged=' . get_query_var( 'paged' ) );
	if ( $query->have_posts() ) :
		?><div class="archive-tiles">
			<?php ee_the_query_tiles( $query ); ?>
		</div><?php

		ee_load_more( $query );
	endif;
?></div><?php

get_footer();
