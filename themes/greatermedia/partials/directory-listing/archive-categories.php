<?php

?><div>
	<h3>Categories</h3>

	<ul>
		<?php foreach ( get_terms( 'taxonomy=listing-category' ) as $category ) : ?>
			<li>
				<?php $image_id = get_term_meta( $category->term_id, 'featured-image', true ); ?>
				<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ); ?>" alt="<?php echo esc_attr( $category->name ); ?>">

				<h4>
					<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
						<?php echo esc_html( $category->name ); ?>
					</a>
				</h4>
			</li>
		<?php endforeach; ?>
	</ul>
</div>