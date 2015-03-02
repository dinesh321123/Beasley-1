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
							<div class="highlights__event--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary' ) ?>)'></div>
						<?php endif; ?>

						<div class="highlights__event--meta">
							<h3 class="highlights__event--title"><?php the_title(); ?></h3>
							<?php
							/*
							 * Moved the class from the span to the time so I could add both the start and end times to the datetime attributes
							 */
							$start = tribe_get_start_date( get_the_ID(), false, 'M d' );
							$start_c = tribe_get_start_date( get_the_ID(), false, 'c' );
							$end = tribe_get_end_date( get_the_ID(), false, 'M d' );
							$end_c = tribe_get_end_date( get_the_ID(), false, 'c' );
							if ( $start != $end ) {
								printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></span>', $start_c, $start, $end_c, $end );
							} else {
								printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time></span>', $start_c, $start );
							}
							?>
						</div>
					</a>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>

		</div>

		<div class="highlights__contests">

			<h2 class="highlights__heading">Contests</h2>

			<div class="highlights__contest--item">
				<div class="highlights__contest--thumb" style='background-image: url(http://lorempixel.com/200/200/sports/)'></div>

					<div class="highlights__contest--meta">
						<h3 class="highlights__contest--title">John Mellencamp Christmas Live</h3>
						<span class="highlights__event--date"><time datetime="">Sat, Dec 23th</time></span>
						<a href="#" class="highlights__contest--btn">Enter To Win</a>
					</div>

			</div>
		</div>

		<div class="highlights__ad">

			<img src="http://placehold.it/300x250">

		</div>

	</div>

</section>