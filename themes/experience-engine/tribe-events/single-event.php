<?php

the_post();

$website = tribe_get_event_website_url();
$cost = tribe_get_cost();

$venue = array();
if ( tribe_has_venue() ) :
	$venue[] = tribe_get_venue();
	$venue[] = tribe_get_address();
	$venue[] = tribe_get_city();
	$venue[] = tribe_get_stateprovince();
	$venue[] = tribe_get_zip();
endif;

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="event-info">
		<div class="events-link">
			<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
				<?php echo esc_html( tribe_get_event_label_plural() ); ?>
			</a>
		</div>

		<h1 class="event-title">
			<?php the_title(); ?>
			<?php tribe_the_notices(); ?>
		</h1>

		<div class="event-date">
			<?php echo tribe_events_event_schedule_details(); ?>
		</div>

		<?php if ( ! empty( $website ) || ! empty( $cost ) ) : ?>
			<div class="event-meta">
				<?php if ( $cost ) : ?>
					<div class="event-cost">
						<strong>Cost: </strong>
						<?php echo " $" . $cost; ?>
					</div>
				<?php endif; ?>

				<?php if ( filter_var( $website, FILTER_VALIDATE_URL ) ) : ?>
					<div class="event-website">
						<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener">
							View Event Site
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $venue ) ) : ?>
			<div class="event-venue">
				<span class="tribe-address"><?php echo implode( ', ', $venue ) ?></span>
				<?php echo tribe_get_map_link_html(); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php get_template_part( 'partials/featured-media', 'autoheight' ); ?>
			<?php the_content(); ?>

			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>
