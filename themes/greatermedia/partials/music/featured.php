<?php
/**
 * Partial for the Front Page Featured Content for the Music Theme
 *
 * @package Greater Media
 * @since   0.1.0
 */
$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
	<div class="featured__article">
		<a href="<?php the_permalink(); ?>">
			<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-primary', null, true ); ?>);'>
				<?php image_attribution(); ?>
			</div>
			<div class="featured__article--content">
				<div class="featured__article--heading">
					<?php the_title(); ?>
				</div>
			</div>
		</a>
	</div>
<?php endif;

// if we still have more posts (we almost always will), render the 3 below the main section
if ( $hp_featured_query->have_posts() ) : ?>
	<div class="featured__content">
		<?php while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
			<div class="featured__content--block">
				<a href="<?php the_permalink(); ?>">
					<div class="featured__content--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'></div>
					<div class="featured__content--meta">
						<h2 class="featured__content--title"><?php the_title(); ?></h2>
						<div class="featured__content--link">
							<span class="featured__content--btn">Read More</span>
						</div>
					</div>
				</a>
			</div>
		<?php endwhile; ?>
	</div>
<?php
endif;
wp_reset_query();

// Start Countdown Clock
if ( function_exists( 'GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock' ) ) {
	GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock();
}
// End Countdown Clock
