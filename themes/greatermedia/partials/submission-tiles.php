<?php
/**
 * Partial for contest submissions.
 *
 * @package Greater Media
 * @since 0.1.0
 */

// do nothing if there are no submisions
$submissions_query = apply_filters( 'gmr_contest_submissions_query', null );
if ( ! $submissions_query || ! $submissions_query->have_posts() ) {
	return;
}

// enqueue gallery script
wp_enqueue_script( 'gmr-gallery' );

// render submissions layout
?>

<section class="contest__submissions">
	<h4 class="contest__submissions--title">All Entries</h4>

	<ul class="contest__submissions--list">
		<?php while ( $submissions_query->have_posts() ) : ?>
			<?php $submissions_query->the_post(); ?>
			<?php get_template_part( 'partials/submission', 'tile' ); ?>
		<?php endwhile; ?>
	</ul>

	<?php if ( $submissions_query->max_num_pages > 1 ) : ?>
		<button type="button" class="contest__submissions--load-more">
			<i class="gmr-icon icon-spin icon-loading"></i> Load More
		</button>
	<?php endif; ?>
</section>

<?php wp_reset_postdata(); ?>