<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">

				<div class="videos">

					<?php
					$video_query = \GreaterMedia\Shows\get_show_video_query();

					while( $video_query->have_posts() ) : $video_query->the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

							<header class="entry-header">
								<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							</header>

							<div class="entry-content">
								<?php the_excerpt(); ?>
							</div>

						</article>
					<?php
					endwhile;
					wp_reset_query();
					?>

					<div class="video-paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $video_query ); ?></div>

				</div>
				
			</section>

		</div>

	</main>

<?php get_footer();