<?php

global $gmr_last_song, $gmr_moved_song, $wp_query;

// add a trailing song from previous page to the current page
if ( ! empty( $gmr_moved_song ) ) :
	array_unshift( $wp_query->posts, $gmr_moved_song );
endif;

$date_pattern = 'M j, Y';
$current_date = null;
while( have_posts() ) :
	the_post();

	$date = get_the_time( $date_pattern );
	if ( $current_date != $date ) :
		// break the loop if the trailing song has different date to eliminate
		// a gap in the songs list, we will add this song on the next page
		if ( ! have_posts() ) :
			break;
		endif;

		if ( ! is_null( $current_date ) ) :
				?></ul>
			</div><?php
		endif;

		$not_same_date = ! $gmr_last_song || $date != get_the_time( $date_pattern, $gmr_last_song );

		?><div class="songs__group<?php echo $not_same_date ? ' songs__group--new-date' : ''; ?>">
			<div class="songs__group--date"><?php echo $not_same_date ? esc_html( $date ) : '&nbsp;'; ?></div>
			<ul class="songs__group--list"><?php

		$current_date = $date;
	endif;

	$artist = get_post_meta( get_the_ID(), 'artist', true );
	$link = get_post_meta( get_the_ID(), 'purchase_link', true );
	
	?><li class="song__item">
		<div class="song__time">
			<?php echo get_the_time( 'h:i A' ); ?>
		</div>
		<div class="song__meta">
		<?php if ( filter_var( $link, FILTER_VALIDATE_URL ) ) : ?>
			<a href="<?php echo esc_url( $link ); ?>">
				<span class="song__title"><?php the_title(); ?></span>
			</a>
		<?php else : ?>
			<span class="song__title"><?php the_title(); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $artist ) ) : ?>
			<span class="song__artist">
				&#8212; <?php echo esc_html( $artist ); ?>
			</span>
		<?php endif; ?>
		</div>
	</li><?php
endwhile;

// close opened .songs__group
	?></ul>
</div>
