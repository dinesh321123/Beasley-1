<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$event_id = get_the_ID();

?>

<div id="tribe-events-content" class="tribe-events-single vevent hentry">

	<div class="tribe-events-back">
		<a href="<?php echo tribe_get_events_link() ?>"> <?php _e( '&laquo; All Events', 'tribe-events-calendar' ) ?></a>
	</div>

	<?php while ( have_posts() ) :  the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php bbgi_featured_image_layout_is( get_the_ID(), 'poster' ) ? get_template_part( 'partials/feature-image-event' ) : ''; ?>

			<h2 class="entry__title"><a href="<?php the_permalink(); ?>" title="<?php the_title() ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<?php bbgi_featured_image_layout_is( get_the_ID(), 'top' ) ? get_template_part( 'partials/feature-image-event' ) : ''; ?>

			<?php ob_start(); ?>

			<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

			<div class="event__thumbnail-inline">
				<?php bbgi_featured_image_layout_is( get_the_ID(), 'inline' ) ? get_template_part( 'partials/feature-image-event' ) : ''; ?>
			</div>

			<?php
			$event_secondary_content = ob_get_clean();
			echo apply_filters( 'the_secondary_content', $event_secondary_content );
			?>

			<div class="event__info">

			<!-- Event meta -->
			<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
			<?php
			/**
			 * The tribe_events_single_event_meta() function has been deprecated and has been
			 * left in place only to help customers with existing meta factory customizations
			 * to transition: if you are one of those users, please review the new meta templates
			 * and make the switch!
			 */

			ob_start();

			if ( ! apply_filters( 'tribe_events_single_event_meta_legacy_mode', false ) ) {
				tribe_get_template_part( 'modules/meta' );
			} else {
				echo tribe_events_single_event_meta();
			}

			$event_secondary_content = ob_get_clean();
			echo apply_filters( 'the_secondary_content', $event_secondary_content );

			?>
			<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
			<!-- Event content -->
			<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
			<div class="tribe-events-single-event-description tribe-events-content entry-content description">
				<?php the_content(); ?>
			</div>

			<!-- .tribe-events-single-event-description -->
		</div> <!-- #post-x -->
	<?php endwhile; ?>

	<!-- Event footer -->
	<div id="tribe-events-footer">
		<!-- Navigation -->
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php _e( 'Event Navigation', 'tribe-events-calendar' ) ?></h3>
		<!-- .tribe-events-sub-nav -->
	</div>
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
