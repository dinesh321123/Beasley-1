<?php
/**
 * Template Name: Music Site
 * Template Post Type: gmr_homepage
 */
// Start Countdown Clock
if ( function_exists( 'GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock' ) ) {
	GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock();
}
// End Countdown Clock
?><section id="featured" class="home__featured home__featured_music">
	<?php get_template_part( 'partials/music/featured' ); ?>
</section>

<section class="home__highlights">
	<div class="highlights__col">
		<?php get_template_part( 'partials/music/highlights' ); ?>
	</div>
</section>
