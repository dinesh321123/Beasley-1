<?php
/**
 * Partial for Post Footers
 *
 * @package Greater Media
 * @since   0.1.0
 */

$the_id = get_the_ID();
$post_taxonomies = get_post_taxonomies();

?>

<footer class="entry__footer">

	<?php if ( in_array( 'category', $post_taxonomies ) && has_category() ) : ?>
	<div class="entry__categories">
		<div class="entry__list--title"><?php _e( 'Category', 'greatermedia' ); ?></div>
		<ul class="entry__list--categories">
			<?php echo get_the_term_list( $the_id, 'category', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
		</ul>
	</div>
	<?php endif; ?>

	<?php if ( in_array( 'post_tag', $post_taxonomies ) && has_tag() ) : ?>
	<div class="entry__tags">
		<div class="entry__list--title"><?php _e( 'Tags', 'greatermedia' ); ?></div>
		<ul class="entry__list--tags">
			<?php echo get_the_term_list( $the_id, 'post_tag', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
		</ul>
	</div>
	<?php endif; ?>

	<?php if ( class_exists( 'ShowsCPT', false ) && in_array( ShowsCPT::SHOW_TAXONOMY, $post_taxonomies ) && has_term( '', ShowsCPT::SHOW_TAXONOMY ) ) : ?>
		<div class="entry__shows">
			<div class="entry__list--title"><?php _e( 'Shows', 'greatermedia' ); ?></div>
			<ul class="entry__list--shows">
				<?php foreach ( get_the_terms( $the_id, ShowsCPT::SHOW_TAXONOMY ) as $show ) : ?>
					<?php if ( ( $show = \TDS\get_related_post( $show ) ) ) : ?>
						<li class="entry__list--show">
							<?php if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) : ?>
								<a href="<?php echo esc_url( get_permalink( $show ) ); ?>" rel="tag">
							<?php endif; ?>
									
							<?php if ( ( $image_id = intval( get_post_meta( $show->ID, 'logo_image', true ) ) ) ) : ?>
								<span class="entry__show--logo">
									<?php echo wp_get_attachment_image( $image_id ); ?>
								</span>
							<?php endif; ?>

							<?php echo esc_html( get_the_title( $show ) ); ?>

							<?php if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) : ?>
								</a>
							<?php endif; ?>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) : // If comments are open or we have at least one comment, load up the comment template. ?>
		<?php comments_template(); ?>
	<?php endif; ?>
	
</footer>