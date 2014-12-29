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

<section class="contest-submissions">
	<h4 class="contest-submissions--title">All Entries</h4>

	<ul class="contest-submissions--list">
		<?php while ( $submissions_query->have_posts() ) : ?>
			<?php $submissions_query->the_post(); ?>
			<?php get_template_part( 'partials/submission', 'tile' ); ?>
		<?php endwhile; ?>
	</ul>

	<button type="button" class="contest-submissions--load-more"><i class="fa fa-refresh fa-spin"></i> Load More</button>
</section>

<?php wp_reset_postdata(); ?>