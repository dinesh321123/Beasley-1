<?php
/**
 * Partial for the Front Page Featured Content for the News/Sports Theme
 *
 * @package Greater Media
 * @since   0.1.0
 */

$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

if ( $hp_featured_query->have_posts() ) : ?>
<div class="featured__articles">
		<div class="featured__article--primary">
			<?php if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
				<div class="featured__article">
					<a href="<?php the_permalink(); ?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail', null, true ); ?>);'>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								<?php the_title(); ?>
							</div>
						</div>
						<?php if ( has_post_format( 'video' ) ) { ?>
							<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
						<?php } ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

	<?php
	// if we still have more posts (we almost always will), render the 3 below the main section

	$count = 0;
	if ( $hp_featured_query->have_posts() ) :
	?>
		<div class="featured__article--secondary">
			<?php
				while ( $hp_featured_query->have_posts() && $count < 2 ) : $hp_featured_query->the_post();
				$count++; ?>
				<div class="featured__article">
					<a href="<?php the_permalink(); ?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								<?php the_title(); ?>
							</div>
						</div>
						<?php if ( has_post_format( 'video' ) ) { ?>
							<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
						<?php } ?>
					</a>
				</div>
			<?php
				endwhile;

			endif;

			?>
		</div>
		<?php
		// Start Countdown Clocks
		if ( function_exists( 'GreaterMedia\HomepageCountdownClock\current_countdown_clock_query' ) ) {
			$countdown_clock_query = GreaterMedia\HomepageCountdownClock\current_countdown_clock_query();

			if ( $countdown_clock_query->have_posts() ) : $countdown_clock_query->the_post(); ?>
			<div class="homepage_countdown_clock_wrapper">
				<div class="homepage_countdown_clock" style='background-image: url(<?php gm_post_thumbnail_url( 'full', null, true ); ?>);'>
					<div class="homepage_countdown_clock_container">
						<div class="homepage_countdown_clock_message">
							<div class="homepage_countdown_clock_message_counting">
								<?php if ( ( $countdown_mesage = trim( get_post_meta( get_the_ID(), 'countdown-message', true ) ) ) ) : ?>
										<?php echo wpautop( do_shortcode( $countdown_mesage ) ); ?>
								<?php endif; ?>
							</div>
							<div class="homepage_countdown_clock_message_reached" style="display:none;">
								<?php if ( ( $reached_message = trim( get_post_meta( get_the_ID(), 'reached-message', true ) ) ) ) : ?>
										<?php echo wpautop( do_shortcode( $reached_message ) ); ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="homepage_countdown_clock_ticker_wrapper">
							<?php if ( ( $countdown_date = trim( get_post_meta( get_the_ID(), 'countdown-date', true ) ) ) ) : ?>
							<div class="homepage_countdown_clock_ticker" data-countdown-target="<?php echo $countdown_date."000"; ?>">
								<!-- Fill in countdown here -->
							</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="ad__countdown-clock-sponsorship">
						<span class="homepage_countdown_clock_sponsored_by">Sponsored By:</span>
						<?php do_action( 'acm_tag', 'countdown-clock-sponsorship' ); ?>
					</div>
					<div style="clear:both"></div>
				</div>
			</div>
			<?php endif;
		}
		// End Countdown Clocks
		?>
		<div class="featured__content">
			<?php
			if ( $hp_featured_query->have_posts() ) :
				while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post();
			?>
					<div class="featured__content--block">
						<a href="<?php the_permalink(); ?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title"><?php the_title(); ?></h2>
							</div>
							<?php if ( has_post_format( 'video' ) ) { ?>
								<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
							<?php } ?>
						</a>
					</div>
			<?php
				endwhile;
			endif;
			?>
		</div>
	<?php wp_reset_query(); ?>
</div>
<?php

else :
endif;
