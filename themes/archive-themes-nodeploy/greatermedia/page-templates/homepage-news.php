<?php
/**
 * Template Name: News Site
 * Template Post Type: gmr_homepage
 */
// Start Countdown Clock
if ( function_exists( 'GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock' ) ) {
	GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock();
}
// End Countdown Clock
?><section id="featured" class="home__featured home__featured_news">
	<?php get_template_part( 'partials/news/featured'); ?>
</section>

<section class="home__highlights">
	<div class="highlights__col">
		<?php get_template_part( 'partials/news/highlights'); ?>
	</div>
</section>
