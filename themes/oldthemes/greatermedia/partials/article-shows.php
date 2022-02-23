<?php
/**
 * Partial to display shows associated with an post
 *
 * @package Greater Media
 * @since   0.1.0
 */

$the_id = get_the_ID();
$post_taxonomies = get_post_taxonomies();

?>
<div class="article__shows">
	<div class="article__list--title"><?php _e( 'Shows', 'greatermedia' ); ?></div>
	<ul class="article__list--shows">
		<?php foreach ( get_the_terms( $the_id, ShowsCPT::SHOW_TAXONOMY ) as $show ) : ?>
			<?php if ( ( $show = \TDS\get_related_post( $show ) ) ) : ?>
				<li class="article__list--show">
					<?php if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( $show ) ); ?>" rel="tag">
					<?php endif; ?>

					<?php if ( ( $image_id = intval( get_post_meta( $show->ID, 'logo_image', true ) ) ) ) : ?>
						<div class="article__show--logo">
							<?php echo wp_get_attachment_image( $image_id ); ?>
						</div>
					<?php endif; ?>
						<div class="article__show--name">
							<?php echo esc_html( get_the_title( $show ) ); ?>
						</div>
					<?php if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) : ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
