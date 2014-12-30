<?php
/**
 * Partial for Post Footers
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>

<footer class="entry__footer">

	<div class="entry__categories">
		<div class="entry__list--title"><?php _e( 'Category', 'greatermedia' ); ?></div>
		<ul class="entry__list--categories">
			<?php echo get_the_term_list( $post->ID, 'category', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
		</ul>
	</div>

	<div class="entry__tags">
		<div class="entry__list--title"><?php _e( 'Tags', 'greatermedia' ); ?></div>
		<ul class="entry__list--tags">
			<?php echo get_the_term_list( $post->ID, 'post_tag', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
		</ul>
	</div>

	<?php
	/**
	 * @todo replace content in `.entry__shows` with dynamic content
	 */
	?>
	<div class="entry__shows">
		<div class="entry__list--title"><?php _e( 'Shows', 'greatermedia' ); ?></div>
		<ul class="entry__list--shows">
			<?php echo get_the_term_list( $post->ID, '_shows', '<li class="entry__list--show">', ',</li><li class="entry__list--show">', '</li>' ); ?>
		</ul>
	</div>

</footer>