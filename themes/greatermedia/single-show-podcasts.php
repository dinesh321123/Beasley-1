<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="entry-header">

							<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?> Podcasts</a></h2>

						</header>

						<div class="entry-content">
							single-show-podcasts.php
						</div>

					</article>

					<div class="podcasts">

						<?php
						$podcast_query = \GreaterMedia\Shows\get_show_podcast_query();

						while( $podcast_query->have_posts() ) : $podcast_query->the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">

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

						<div class="podcast-paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $podcast_query ); ?></div>

					</div>

			</section>

		</div>

	</main>

<?php get_footer();