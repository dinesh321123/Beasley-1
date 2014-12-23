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

			<div class="highlights__community">

				<h2 class="highlights__heading"><?php bloginfo( 'name' ); ?><?php _e( ' Community Highlights', 'greatermedia' ); ?></h2>

				<?php
				$hp_comm_query = \GreaterMedia\HomepageCuration\get_community_query();
				while( $hp_comm_query->have_posts() ) : $hp_comm_query->the_post(); ?>
					<div class="highlights__community--item">

						<div class="highlights__community--thumb">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'gmr-featured-secondary' ); ?>
							</a>
						</div>

						<h3 class="highlights__community--title">
							<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h3>

					</div>
				<?php endwhile; ?>
				<?php wp_reset_query(); ?>
			</div>

			<div class="highlights__events">

				<h2 class="highlights__heading"><?php _e( 'Upcoming Events', 'greatermedia' ); ?></h2>

				<?php
				$events_query = \GreaterMedia\HomepageCuration\get_events_query();
				while( $events_query->have_posts() ) : $events_query->the_post(); ?>
					<div class="highlights__event--item">

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="highlights__event--thumb">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'gmr-featured-secondary' ); ?></a>
							</div>
						<?php endif; ?>

						<div class="highlights__event--meta">
							<h3 class="highlights__event--title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
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

					</div>
				<?php endwhile; ?>
				<?php wp_reset_query(); ?>

			</div>

		</div>

</section>