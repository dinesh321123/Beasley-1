<?php
/**
 * Partial for the Front Page Highlights - Community and Events
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>

<section class="home__highlights">

	<div class="highlights__col">

		<div class="highlights__events">

			<h2 class="highlights__heading"><?php _e( 'Upcoming Events', 'greatermedia' ); ?></h2>

			<?php
			$events_query = \GreaterMedia\HomepageCuration\get_events_query();
			while( $events_query->have_posts() ) : $events_query->the_post(); ?>
				<div class="highlights__event--item">
					<a href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="highlights__event--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)'></div>
						<?php endif; ?>
						<div class="highlights__event--meta">
							<h3 class="highlights__event--title"><?php the_title(); ?></h3>
							<?php
							/*
							 * Moved the class from the span to the time so I could add both the start and end times to the datetime attributes
							 */
							$start = tribe_get_start_date( get_the_ID(), false, 'M j, Y' );
							$start_c = tribe_get_start_date( get_the_ID(), false, 'c' );
							$end = tribe_get_end_date( get_the_ID(), false, 'M j, Y' );
							$end_c = tribe_get_end_date( get_the_ID(), false, 'c' );
							$start_time = tribe_get_start_time( get_the_ID(), false, 'g:iA' );
							$start_time_m = tribe_get_start_time( get_the_ID(), false, 'G:i' );
							$end_time = tribe_get_end_time( get_the_ID(), false, 'g:iA' );
							$end_time_m = tribe_get_end_time( get_the_ID(), false, 'G:i' );
							if ( $start != $end ) {
								printf( '<div class="highlights__event--date"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></div>', $start_c, $start, $end_c, $end );
								printf( '<div class="highlights__event--time"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></div>', $start_time_m, $start_time, $end_time_m, $end_time);
							} else {
								printf( '<div class="highlights__event--date"><time datetime="%1$s">%2$s</time></div>', $start_c, $start );
								printf( '<div class="highlights__event--time"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></div>', $start_time_m, $start_time, $end_time_m, $end_time);
							}
							?>
						</div>
					</a>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>

		</div>

		<div class="highlights__contests">

			<h2 class="highlights__heading"><?php _e( 'Contests', 'greatermedia' ); ?></h2>


			<?php
			$contests_query = \GreaterMedia\HomepageCuration\get_contests_query();
			while( $contests_query->have_posts() ) : $contests_query->the_post(); ?>

				<div class="highlights__contest--item">
					<div class="highlights__contest--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)'></div>

						<div class="highlights__contest--meta">
							<h3 class="highlights__contest--title"><?php the_title(); ?></h3>
							<?php
							$date_format = 'M j, Y';
							$date_format_c = 'c';
							$time_format = 'g:iA';
							$time_format_m = 'G:i';

							$start = get_post_meta( $post->ID, 'contest-start', true );
							$end = get_post_meta( $post->ID, 'contest-end', true );
							$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

							echo '<div class="highlights__contest--date"><time datetime="' . date( $date_format_c, $start + $offset ) . '">' . date( $date_format, $start + $offset ) . '</time> - <time datetime="' . date( $date_format_c, $end + $offset ) . '">' . date( $date_format, $end + $offset ) . '</time></div>';
							echo '<div class="highlights__contest--time"><time datetime="' . date( $time_format_m, $end + $offset ) . '">' . date( $time_format, $end + $offset ) . '</time></div>';
							?>
							<a href="<?php the_permalink(); ?>" class="highlights__contest--btn"><?php _e( 'Enter To Win', 'thefanatic' ); ?></a>
						</div>

				</div>

			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
		</div>

		<div class="highlights__ad">

			<div class="highlights__ad--desktop">
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop', array( 'min_width' => 1024 ) ); ?>
			</div>
			<div class="highlights__ad--mobile">
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile', array( 'max_width' => 1023 ) ); ?>
			</div>

		</div>

	</div>

</section>