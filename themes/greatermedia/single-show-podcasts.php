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
					if( $podcast_query->have_posts() ):
					while( $podcast_query->have_posts() ) : $podcast_query->the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">
							<?php GMP_Player::render_podcast_episode(); ?>
						</article>
						
					<?php
					endwhile;
					else:?>
						<article id="post-not-found" class="hentry cf">

							<header class="article-header">

								<h1><?php _e( 'Oops, Not Episodes Here!', 'greatermedia' ); ?></h1>

							</header>

						</article>
					<?php
					endif;
					echo GMP_Player::custom_pagination( $podcast_query );
					wp_reset_query();
					?>

				</div>
			</section>

		</div>

	</main>

<?php get_footer();