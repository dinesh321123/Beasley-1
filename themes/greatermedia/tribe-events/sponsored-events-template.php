<?php
/**
 * Sponsored Events Template
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header(); ?>
<main class="main" role="main">

	<div class="container">

		<section class="content">

			<div id="tribe-events-pg-template">
				<div class="ad__events-sponsorship">
					<?php do_action( 'acm_tag', 'events-sponsorship' ); ?>
				</div>
				<?php tribe_events_before_html(); ?>
				<h2 class="content__heading" itemprop="headline"><?php _e( 'Upcoming Events', 'greatermedia' ); ?></h2>
				<?php tribe_get_view(); ?>
				<?php tribe_events_after_html(); ?>
			</div> <!-- #tribe-events-pg-template -->

		</section>

	</div>

</main>
<?php get_footer(); ?>
