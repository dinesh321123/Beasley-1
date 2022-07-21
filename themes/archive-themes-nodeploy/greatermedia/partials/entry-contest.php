<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<section class="entry2__thumbnail">
		<a href="<?php the_permalink(); ?>">
			<div class="entry2__thumbnail__image" style="background-image: url('<?php bbgi_post_thumbnail_url( null, true, 400, 300 ); ?>')"></div>
			<div class="entry2__thumbnail__overlay"></div>
			<div class="entry2__thumbnail__icon"></div>

			<div class="entry2__thumbnail--contest-type">
				<?php echo esc_html( gmr_contest_get_type_label() ); ?>
			</div>
			<div class="entry2__thumbnail--end-date">
				<?php $end_date = get_post_meta( get_the_ID(), 'contest-end', true ); ?>
				<?php if ( ! empty( $end_date ) ) : ?>
					<?php $end_date += get_option( 'gmt_offset' ) * HOUR_IN_SECONDS; ?>
					<?php if ( $end_date > current_time( 'timestamp', 1 ) ) : ?>
						<div class="entry2__thumbnail--day-of-week">Ends <?php echo date( 'l', $end_date ); ?></div>
						<div class="entry2__thumbnail--month-and-day"><?php echo date( 'M j', $end_date ); ?></div>
					<?php else : ?>
						<div class="entry2__thumbnail--month-and-day">Ended</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</a>
	</section>

	<section class="entry2__meta">
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<div class="entry2__event--details">
			<?php echo strip_tags( get_post_meta( get_the_ID(), 'prizes-desc', true ) ); ?>
		</div>

		<p><?php the_excerpt(); ?></p>
	</section>

	<footer class="entry2__footer">
		<?php if ( ( $category = get_the_category() ) && ! empty( $category[0] ) ) : ?>
			<a href="<?php echo esc_url( get_category_link($category[0]->term_id ) ); ?>" class="entry2__footer--category">
				<?php echo esc_html( $category[0]->cat_name ); ?>
			</a>
		<?php endif; ?>
	</footer>
</article>
