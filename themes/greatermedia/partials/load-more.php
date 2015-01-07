<?php
// Don't bother showing this if there are no more pages to page through. 
if ( $GLOBALS['paged'] >= $GLOBALS['wp_query']->max_num_pages ) {
	return; 
}
?>

<button type="button" 
		class="posts-pagination--load-more is-loaded"
		data-url="<?php echo str_replace( PHP_INT_MAX, '{{page}}', get_pagenum_link( PHP_INT_MAX ) ); // do not escape this url because it contains a placeholder ?>"
		data-page="<?php echo ! empty( $GLOBALS['paged'] ) ? absint( $GLOBALS['paged'] ) : 1 ?>"
		data-not-found="All content shown"
		>
	<i class="fa fa-spin fa-refresh"></i> Load More
</button>