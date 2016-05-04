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

<?php
/*
 * Add the submission entrant name to the post object, so we can sort.
 * Since the meta is in a different post we can't use orderby on 'meta_value'. This is a workaround.
 */
$submissions = array();
$order_by = get_post_meta( get_the_ID(), 'entries-order-by', true );

while ( $submissions_query->have_posts() ) :
	$submissions_query->the_post();
	$contest_id = get_post_meta( get_the_ID(), 'contest_entry_id', true );
	$entrant_name = get_post_meta( $contest_id, 'entrant_name', true );
	$post->entrant_name = $entrant_name;
	$submissions[] = $post;
endwhile;

/*
 * Actually sort the submissions, if specified.
 */
if ( 'entrant_name' === $order_by ) {
	function sort_submissions( $a, $b ) {
		return strcasecmp( $a->entrant_name, $b->entrant_name );
	}
	usort( $submissions, 'sort_submissions' );
} ?>

<section class="contest__submissions">
	<h4 class="contest__submissions--title">All Entries</h4>

	<ul class="contest__submissions--list">
		<?php
		global $post;
		foreach ( $submissions as $post ) : ?>
			<?php setup_postdata( $post ); ?>
			<?php get_template_part( 'partials/submission', 'tile' ); ?>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</ul>

	<?php if ( $submissions_query->max_num_pages > 1 && ! is_preview() ) : ?>
		<button type="button" class="contest__submissions--load-more">
			<i class="gmr-icon icon-spin icon-loading"></i> Load More
		</button>
	<?php endif; ?>
</section>

<?php wp_reset_postdata(); ?>