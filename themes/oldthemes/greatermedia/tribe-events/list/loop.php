<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/loop.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $more;
$more = false;

do_action( 'tribe_events_before_header' ); ?>

<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>

	<!-- Header Navigation -->
	<?php do_action( 'tribe_events_before_header_nav' ); ?>
	<?php tribe_get_template_part( 'list/nav', 'header' ); ?>
	<?php do_action( 'tribe_events_after_header_nav' ); ?>

</div>
<!-- #tribe-events-header -->

<?php do_action( 'tribe_events_after_header' );

do_action( 'tribe_events_before_loop' );

while ( have_posts() ) :
	the_post();

	do_action( 'tribe_events_inside_before_loop' );
	get_template_part( 'partials/entry', 'tribe_events' );
	do_action( 'tribe_events_inside_after_loop' );
endwhile;

do_action( 'tribe_events_after_loop' );