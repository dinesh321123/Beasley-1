<div class="directory-categories">
	<h2 class="directory-categories__title">Categories</h2>
	<div class="directory-categories__wrapper">
		<?php foreach ( get_terms( 'taxonomy=listing-category' ) as $category ) : ?>
			<div class="directory-categories__item">
				<div class="directory-categories__item-thumbnail">
					<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
						<?php $image_id = get_term_meta( $category->term_id, 'featured-image', true ); ?>
						<img src="<?php echo ! empty( $image_id ) ? esc_url( wp_get_attachment_image_url( $image_id, 'gm-related-post' ) ) : '#'; ?>" alt="<?php echo esc_attr( $category->name ); ?>">
					</a>
				</div>

				<div class="directory-categories__item-content">
					<p class="directory-categories__item-excerpt"><?php echo esc_html( $category->description ); ?></p>
				</div>

				<div class="inquire">
					<a class="inquire__link" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
