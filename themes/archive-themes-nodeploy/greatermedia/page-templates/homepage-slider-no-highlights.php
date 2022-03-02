<?php
/**
 * Template Name: Hero Slider, No Highlights
 * Template Post Type: gmr_homepage
 */

// Start Countdown Clock
if ( function_exists( 'GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock' ) ) {
	GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock();
}
// End Countdown Clock
?><section id="featured" class="home__featured home__featured_hero_slider">
	<?php get_template_part( 'partials/hero-slider/featured' ); ?>
</section>
