<?php

$hp_comm_query = \GreaterMedia\HomepageCuration\get_community_query();
$events_query = \GreaterMedia\HomepageCuration\get_events_query();

?><div class="highlights__community">
	<h2 class="highlights__heading">Don't Miss</h2><?php

	if ( $hp_comm_query->have_posts() ) :
		while( $hp_comm_query->have_posts() ) :
			$hp_comm_query->the_post();
			?><div class="highlights__community--item">
				<a href="<?php the_permalink(); ?>">
					<div class="highlights__community--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)'></div>
					<h3 class="highlights__community--title"><?php the_title(); ?></h3>
				</a>
			</div><?php
		endwhile;
		wp_reset_query();
	endif;
?></div>

<div class="highlights__events">
	<div>
		<h2 class="highlights__heading">Upcoming Events</h2>
		<a class="highlights__events--more-btn" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">More</a>
	</div><?php

	if ( $events_query->have_posts() ) :
		while( $events_query->have_posts() ) :
			$events_query->the_post();

			$start = tribe_get_start_date( get_the_ID(), false, 'M j, Y' );
			$start_c = tribe_get_start_date( get_the_ID(), false, 'c' );
			$end = tribe_get_end_date( get_the_ID(), false, 'M j, Y' );
			$end_c = tribe_get_end_date( get_the_ID(), false, 'c' );

			?><div class="highlights__event--item">
				<a href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="highlights__event--thumb" style="background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)"></div>
					<?php endif; ?>

					<div class="highlights__event--meta">
						<h3 class="highlights__event--title"><?php the_title(); ?></h3>
						<?php
						/*
						 * Moved the class from the span to the time so I could add both the start and end times to the datetime attributes
						 */
						if ( $start != $end ) :
							printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time> - <time datetime="%3$s">%4$s</time></span>', $start_c, $start, $end_c, $end );
						else :
							printf( '<span class="highlights__event--date"><time datetime="%1$s">%2$s</time></span>', $start_c, $start );
						endif;
					?></div>
				</a>
			</div><?php
		endwhile;

		wp_reset_query();
	endif;
?></div>