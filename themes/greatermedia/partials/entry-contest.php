<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<section class="entry2__thumbnail" style="background-image: url('<?php echo has_post_thumbnail() ? esc_url( gm_get_post_thumbnail_url( 'gm-entry-thumbnail-4-3' ) ) : ''; ?>')">
		<a href="<?php the_permalink(); ?>">
			<div class="entry2__thumbnail--start-date">
				<?php $start_date = get_post_meta( get_the_ID(), 'contest-start', true ); ?>
				<?php if ( ! empty( $start_date ) ) : ?>
					<div class="entry2__thumbnail--day-of-week"><?php echo date( 'l', $start_date ); ?></div>
					<div class="entry2__thumbnail--month-and-day"><?php echo date( 'M j', $start_date ); ?></div>
				<?php endif; ?>
			</div>

			<div class="entry2__thumbnail--end-date">
				<?php $start_date = get_post_meta( get_the_ID(), 'contest-end', true ); ?>
				<?php if ( ! empty( $start_date ) ) : ?>
					<div class="entry2__thumbnail--day-of-week"><?php echo date( 'l', $start_date ); ?></div>
					<div class="entry2__thumbnail--month-and-day"><?php echo date( 'M j', $start_date ); ?></div>
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
