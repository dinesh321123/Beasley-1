<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">

				<div class="podcasts">

					<h2>Podcasts</h2>

					<?php
					$podcast_query = \GreaterMedia\Shows\get_show_podcast_query();

					while( $podcast_query->have_posts() ) : $podcast_query->the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">

							<div class="podcast__play">
								<button class="podcast__btn--play"></button>
								<span class="podcast__runtime">RUNTIME</span><?php // todo Podcasts: runtime ?>
							</div>
							<div class="podcast__meta">
								<time datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'd F' ); ?></time>
								<button class="podcast__download">Download</button><?php // todo Podcasts: Download ?>
								<h3><?php the_title(); ?></h3>
								<?php the_excerpt(); ?>
							</div>

						</article>
						
					<?php
					endwhile;
					wp_reset_query();
					?>

					<div class="podcast-paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $podcast_query ); ?></div>

				</div>

			</section>

		</div>

	</main>

<?php get_footer();