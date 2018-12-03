<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<?php
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$feed_url   = esc_url_raw( get_post_meta( $post->ID, 'gmp_podcast_feed', true ) );
		$itunes_url = get_post_meta( $post->ID, 'gmp_podcast_itunes_url', true );
		if ( ! $feed_url || $feed_url == '' || strlen( $feed_url ) == 0 ) {
			$feed_url = home_url( '/' ) . '?feed=podcast&podcast_series=' . $post->post_name;
		}
		$google_play_url = get_post_meta( $post->ID, 'gmp_podcast_google_play_url', true );
		?>
		<div class="container">

			<?php get_template_part( 'partials/show-mini-nav' ); ?>

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast' ); ?> role="article" itemscope
				         itemtype="http://schema.org/BlogPosting">
					<header class="podcast__header">
						<h2 class="content__heading" itemprop="headline"><?php the_title(); ?></h2>

						<a class="podcast__rss" href="<?php echo esc_url( $feed_url ) ?>" target="_blank">Podcast
							Feed</a>
						<?php
						if ( $itunes_url != '' ) {
							?>
							<a class="podcast__subscribe" href="<?php echo esc_url( $itunes_url ); ?>" target="_blank">Apple Podcasts</a>
						<?php
						}
						if ( $google_play_url != '' ) {
							?>
							<a class="podcast__subscribe" href="<?php echo esc_url( $google_play_url ); ?>" target="_blank">Google Podcasts</a>
						<?php
						}
						?>
					</header>
					<?php the_content() ?>
				</article>
			<?php
			endwhile;
			else : ?>

				<article id="post-not-found" class="hentry cf">

					<header class="article-header">

						<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>

					</header>

					<section class="entry-content">

						<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

					</section>

				</article>

		</div>

	<?php endif; ?>

	</section>

	<?php get_sidebar(); ?>

	<?php wp_reset_query(); ?>

<?php get_footer();
