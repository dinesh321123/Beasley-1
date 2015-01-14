<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline">Videos from <?php the_title(); ?></h2>

				</header>

				<div class="videos">

					<?php
					$video_query = \GreaterMedia\Shows\get_show_video_query();

					while( $video_query->have_posts() ) : $video_query->the_post(); ?>
						
						<?php get_template_part('partials/entry'); ?>

					<?php
					endwhile;
					wp_reset_query();
					?>

					<div class="show__paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $video_query ); ?></div>

				</div>
				
			</section>

		</div>

	</main>

<?php get_footer();