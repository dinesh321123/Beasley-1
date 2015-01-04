<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="gallery__archive">

				<div class="galleries">

					<?php get_template_part( 'partials/gallery-archive' ); ?>

					<div class="video-paging"><?php /* echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $album_query ); */ ?></div>

				</div>
				
			</section>

		</div>

	</main>

<?php get_footer();