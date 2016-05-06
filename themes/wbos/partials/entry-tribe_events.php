<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php 
	if ( has_post_thumbnail() || 'tribe_events' == $post->post_type ) :
		$thumbnail_size = has_post_format( 'audio' )
			? 'gm-entry-thumbnail-1-1'
			: 'gm-entry-thumbnail-4-3';
	?>
		<section class="entry2__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<div class="entry2__thumbnail__image" style="background-image: url(<?php gm_post_thumbnail_url( $thumbnail_size, null, true ); ?>)"></div>
				<div class="entry2__thumbnail__overlay"></div>
				<div class="entry2__thumbnail__icon"></div>
				<div class="entry2__thumbnail--event-date">
					<div class="entry2__thumbnail--day-of-week"><?php echo tribe_get_start_date( get_the_ID(), false, 'l' ); ?></div>
					<div class="entry2__thumbnail--month-and-day"><?php echo tribe_get_start_date( get_the_ID(), false, 'M j' ); ?></div>
				</div>
			</a>								
		</section>
	<?php endif; ?>

	<section class="entry2__meta">
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
		<div class="entry2__event--details">
			<?php echo tribe_get_start_date( get_the_ID(), false, 'l' ); ?>, <?php echo tribe_get_start_date( get_the_ID(), false, 'M j' ); ?>
			@
			<?php
			// Put start time, venue, and cost on one line, separated by commas.  
			echo esc_html( implode( ', ', array_filter( array( tribe_get_start_time(), tribe_get_venue(), tribe_get_cost( null, true ) ) ) ) );
			?>
		</div>

		<p><?php the_excerpt(); ?></p>
	</section>

	<footer class="entry2__footer">
		<?php

		$category = get_the_category();
		if ( isset( $category[0] ) ) :
			echo '<a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '" class="entry2__footer--category">' . esc_html( $category[0]->cat_name ) . '</a>';
		endif;
		
		?>
	</footer>
</article>
