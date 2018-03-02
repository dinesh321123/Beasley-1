<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<section class="entry2__thumbnail">
		<a href="<?php the_permalink(); ?>">
			<div class="entry2__thumbnail__image" style="background-image: url('<?php gm_post_thumbnail_url( 'gm-entry-thumbnail-4-3', null, true ); ?>')"></div>
		</a>
	</section>

	<section class="entry2__meta">
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<div class="entry2__event--details">
			<?php $end_date = get_post_meta( get_the_ID(), 'contest-end', true ); ?>
			<?php if ( ! empty( $end_date ) ) : ?>
				Ends <?php echo date( 'l', $end_date ); ?>, <?php echo date( 'M j', $end_date ); ?><br>
			<?php endif; ?>

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
