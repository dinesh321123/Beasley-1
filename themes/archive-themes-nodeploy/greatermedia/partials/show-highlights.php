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


			<?php
			$hp_comm_query = \GreaterMedia\Shows\get_show_favorites_query();
			if ( $hp_comm_query->have_posts() ) { ?>
			<div class="highlights__community">

				<h2 class="highlights__heading"><?php //bloginfo( 'name' ); ?><?php _e( ' Our Favorites', 'greatermedia' ); ?></h2>

				<?php while( $hp_comm_query->have_posts() ) : $hp_comm_query->the_post(); ?>

				<div class="highlights__community--item">

					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

						<div class="highlights__community--thumb" style='background-image: url(<?php bbgi_post_thumbnail_url( null, true, 336, 224 ); ?>)'></div>

						<h3 class="highlights__community--title">
							<?php the_title(); ?>
						</h3>

					</a>

				</div>

				<?php endwhile; ?>

				<?php wp_reset_query(); ?>
			</div>
			<?php } ?>


			<?php
			global $post;
			$events = \GreaterMedia\Shows\get_show_events();
			if ( $events ) { ?>
				<div class="highlights__events">

				<h2 class="highlights__heading"><?php _e( 'Upcoming Events', 'greatermedia' ); ?></h2>

				<?php foreach( $events as $post ): setup_postdata( $post ); ?>
					<div class="highlights__event--item">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="highlights__event--thumb" style='background-image: url(<?php bbgi_post_thumbnail_url( null, true, 336, 224 ); ?>)'></div>
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
								if ( $start != $end ) {
									printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></span>', $start_c, $start, $end_c, $end );
								} else {
									printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time></span>', $start_c, $start );
								}
								?>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
				<?php wp_reset_query(); ?>
				</div>
			<?php } ?>


		</div>

</section>
