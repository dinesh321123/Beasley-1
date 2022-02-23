<?php
/**
 * Partial to display categories associated with an post
 *
 * @package Greater Media
 * @since   0.1.0
 */


$post_taxonomies = get_post_taxonomies();

$the_id = get_the_ID();
$terms = get_the_term_list( $the_id, 'category', '<li class="article__list--item">', ',</li><li class="article__list--item">', '</li>' );
if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

?><div class="article__categories">
	<div class="article__list--title"><?php _e( 'Category', 'greatermedia' ); ?></div>
	<ul class="article__list--categories">
		<?php echo $terms; ?>
	</ul>
</div>
