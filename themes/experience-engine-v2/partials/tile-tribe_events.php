<div data-post-id="post-<?php the_ID(); ?>" <?php post_class( 'event-tile' ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>

	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?>
		<div class="event-dates">
			<?php echo tribe_events_event_schedule_details(); ?>
		</div>
	</div>
</div>
