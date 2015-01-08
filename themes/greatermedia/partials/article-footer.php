<?php

$the_id = get_the_ID();
$post_taxonomies = get_post_taxonomies();

?>

<footer class="article__footer">

	<?php if ( in_array( 'category', $post_taxonomies ) && has_category() ) : ?>
		<div class="article__categories">
			<div class="article__list--title"><?php _e( 'Category', 'greatermedia' ); ?></div>
			<ul class="article__list--categories">
				<?php echo get_the_term_list( $the_id, 'category', '<li class="article__list--item">', ',</li><li class="article__list--item">', '</li>' ); ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( in_array( 'post_tag', $post_taxonomies ) && has_tag() ) : ?>
		<div class="article__tags">
			<div class="article__list--title"><?php _e( 'Tags', 'greatermedia' ); ?></div>
			<ul class="article__list--tags">
				<?php echo get_the_term_list( $the_id, 'post_tag', '<li class="article__list--item">', ',</li><li class="article__list--item">', '</li>' ); ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( class_exists( 'ShowsCPT', false ) && in_array( ShowsCPT::SHOW_TAXONOMY, $post_taxonomies ) && has_term( '', ShowsCPT::SHOW_TAXONOMY ) ) : ?>
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
								<span class="article__show--logo">
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
</footer>
