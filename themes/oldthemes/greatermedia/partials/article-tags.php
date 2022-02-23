<?php
/**
 * Partial to display tags associated with an post
 *
 * @package Greater Media
 * @since   0.1.0
 */

$the_id = get_the_ID();
$post_taxonomies = get_post_taxonomies();
$terms = get_the_term_list( $the_id, 'post_tag', '<li class="article__list--item">', ',</li><li class="article__list--item">', '</li>' );

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

?>
<div class="article__tags">
	<div class="article__list--title"><?php _e( 'Tags', 'greatermedia' ); ?></div>
	<ul class="article__list--tags">
		<?php echo $terms; ?>
	</ul>
</div>