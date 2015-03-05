<?php get_header(); ?>

	<div class="container">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="content">

			<div class="videos">

				<?php
				$video_query = \GreaterMedia\Shows\get_show_video_query();

				while ( $video_query->have_posts() ) : $video_query->the_post();

					get_template_part( 'partials/entry', get_post_field( 'post_type', null ) );

				endwhile;
				wp_reset_query();
				?>

				<div
					class="show__paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $video_query ); ?></div>

			</div>

		</section>

	</div>

<?php get_footer();