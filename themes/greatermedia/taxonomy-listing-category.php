<?php
/**
 * Archive template file for directory listing
 */

$category = get_queried_object();
$image_id = get_term_meta( $category->term_id, 'featured-image', true );

get_header();

?><div class="container">
	<img src="<?php echo ! empty( $image_id ) ? esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ) : '#'; ?>" alt="<?php echo esc_attr( $category->name ); ?>">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'partials/directory-listing/taxonomy-item' ); ?>
	<?php endwhile; ?>
</div><?php

get_footer();
