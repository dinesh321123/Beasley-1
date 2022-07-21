<?php get_header(); ?>

	<div class="container">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="content">

			<div class="videos">

				<?php

				global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

				$video_query = \GreaterMedia\Shows\get_show_video_query();
				while ( $video_query->have_posts() ) : $video_query->the_post();
					get_template_part( 'partials/entry', get_post_field( 'post_type', null ) );
				endwhile;

				wp_reset_query();

				$gmr_loadmore_paged = get_query_var( 'paged', 1 );
				if ( $gmr_loadmore_paged < 2 ) :
					greatermedia_load_more_button( array(
						'query'        => $video_query,
						'partial_slug' => 'partials/loop',
						'partial_name' => 'show-video',
					) );
				endif;

				$gmr_loadmore_num_pages = $video_query->max_num_pages;
				$gmr_loadmore_post_count = $video_query->post_count;

				?>
				
			</div>

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer();