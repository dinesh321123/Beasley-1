<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
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
				<div class="tribe-clearfix">
				<?php
					if( is_tax() ) {
				  	echo term_description( get_queried_object_id(), 'tribe_events_cat' );
					} ?>
				</div>
				<?php tribe_get_view(); ?>
				<?php tribe_events_after_html(); ?>
			</div> <!-- #tribe-events-pg-template -->

		</section>

	</div>

</main>
<?php get_footer(); ?>
