<?php
/**
 * Single episode template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div class="container">

			<?php get_template_part( 'partials/show-mini-nav' ); ?>

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<div class="ad__inline--right desktop">
						<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop', array( 'min_width' => 1024 ) ); ?>
					</div>

					<header class="article__header">

						<time class="article__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
						<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>

						<?php get_template_part( 'partials/social-share' ); ?>

						<div class="episode__buttons">
							<?php

							$itunes_url = $google_play_url = $parent_podcast = false;
							$parent_podcast_id = wp_get_post_parent_id( get_the_ID() );
							if ( $parent_podcast_id ) {
								$parent_podcast = get_post( $parent_podcast_id );
								$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
								$google_play_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_google_play_url', true );
							}

							$downloadable = get_post_meta( get_the_ID(), 'gmp_audio_downloadable', true );
							if ( $downloadable ) {
								$content = get_the_content();
								$pattern = get_shortcode_regex();

								if ( preg_match_all( '/'. $pattern .'/s', $content, $matches ) && array_key_exists( 2, $matches ) && in_array( 'audio', $matches[2] ) && ! empty( $matches[3] ) ) {
									$mp3_src = false;
									$atts = shortcode_parse_atts( $matches[3][0] );
									$formats = array( 'mp3', 'ogg', 'wma', 'm4a', 'wav' );
									foreach ( $formats as $format ) {
										if ( ! empty( $atts[ $format ] ) && filter_var( $atts[ $format ], FILTER_VALIDATE_URL ) ) {
											$mp3_src = $atts[ $format ];
											break;
										}
									}

									if ( ! empty( $mp3_src ) ) {
										echo '<a href="', esc_attr( $mp3_src ), '" download="', esc_attr( $mp3_src ), '" class="podcast__download--btn" download>Download</a>';
									}
								}
							}

							if ( $parent_podcast ) {

								$feed_url = esc_url_raw( get_post_meta( $parent_podcast_id, 'gmp_podcast_feed', true ) );
								if ( ! $feed_url || $feed_url == '' || strlen( $feed_url ) == 0 ) {
									$feed_url = home_url( '/' ) . '?feed=podcast&podcast_series=' . $parent_podcast->post_name;
								}

								echo '<a class="podcast__rss" href="', esc_url( $feed_url ), '" target="_blank">Podcast Feed</a>';
								echo '<a class="podcast__link" href="', esc_url( get_permalink( $parent_podcast_id ) ), '">More From ', esc_html( $parent_podcast->post_title ), '</a>';

							}

							?>
						</div>

					</header>

					<section class="article__content" itemprop="articleBody">
						<?php the_content(); ?>
					</section>

					<?php get_template_part( 'partials/article-footer' ); ?>

					<div class="ad__inline--right mobile">
						<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile', array( 'max_width' => 1023 ) ); ?>
					</div>

					<?php if ( post_type_supports( get_post_type(), 'comments' ) ) { // If comments are open or we have at least one comment, load up the comment template. ?>
						<div class='article__comments'>
							<?php comments_template(); ?>
						</div>
					<?php } ?>


					<?php if ( function_exists( 'related_posts' ) ): ?>
						<?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
					<?php endif; ?>

				</article>

			</section>

		</div>

	<?php endwhile; ?>

<?php get_footer();
