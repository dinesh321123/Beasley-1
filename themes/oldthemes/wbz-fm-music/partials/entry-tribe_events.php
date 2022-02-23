<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php if ( has_post_thumbnail() || 'tribe_events' == $post->post_type ) : ?>
		<section class="entry2__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<div class="entry2__thumbnail__image" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 400, has_post_format( 'audio' ) ? 400 : 300 ); ?>)"></div>
			</a>
		</section>
	<?php endif; ?>

	<section class="entry2__meta">
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<div class="entry2__event--details">
			<?php echo tribe_get_start_date( get_the_ID(), false, 'l' ); ?>, <?php echo tribe_get_start_date( get_the_ID(), false, 'M j' ); ?>
			@
			<?php echo esc_html( implode( ', ', array_filter( array( tribe_get_start_time(), tribe_get_venue(), tribe_get_cost( null, true ) ) ) ) ); ?>
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
