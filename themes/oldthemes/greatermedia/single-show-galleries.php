<?php get_header(); ?>

	<div class="container">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="gallery__archive">

			<div class="galleries">

				<?php get_template_part( 'partials/gallery-archive' ); ?>

				<div
					class="show__paging"><?php /* echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $album_query ); */ ?></div>

			</div>

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer();