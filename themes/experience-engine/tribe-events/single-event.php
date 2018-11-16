<?php

the_post();

$website = tribe_get_event_website_url();
$cost = tribe_get_cost();

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<div>
		<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
			<?php echo esc_html( tribe_get_event_label_plural() ); ?>
		</a>
	</div>

	<?php tribe_the_notices(); ?>

	<h1><?php the_title(); ?></h1>

	<div>
		<div>
			<?php echo tribe_events_event_schedule_details(); ?>
		</div>

		<?php if ( ! empty( $website ) || ! empty( $cost ) ) : ?>
			<div>
				<?php if ( filter_var( $website, FILTER_VALIDATE_URL ) ) : ?>
					<div>
						<span>Website:</span>
						<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer">
							View Site
						</a>
					</div>
				<?php endif; ?>

				<?php if ( $cost ) : ?>
					<div>
						<span>Cost:</span>
						<?php echo $cost; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div>
			<?php echo preg_replace( '#\s*\<br\s*/?\>\s*#i', ', ', tribe_get_full_address() ); ?>
			<?php echo tribe_get_map_link_html(); ?>
		</div>
	</div>

	<div>
		<div>
			<?php get_template_part( 'partials/featured-media' ); ?>
			<?php the_content(); ?>

			<div>
				<?php the_tags( '<span>Tags</span>' ); ?>
			</div>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>
