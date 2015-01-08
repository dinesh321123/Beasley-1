<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php
					if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
							<?php global $post;
							$itunes_url = get_post_meta( $post->ID, 'gmp_podcast_itunes_url', true );
							?>
							<header class="entry-header">
								<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<?php echo '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe</a>'; ?>
							</header>
							<?php $podcast_obj = GMP_Player::render_podcasts(); ?>

						</article>

					<?php endwhile;
					else : ?>

						<article id="post-not-found" class="hentry cf">

							<header class="article-header">

								<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>

							</header>

							<section class="entry-content">

								<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

							</section>

						</article>

					<?php endif;
					echo GMP_Player::custom_pagination( $podcast_obj );
					wp_reset_query();
					?>

			</section>

		</div>

	</main>

<?php get_footer();