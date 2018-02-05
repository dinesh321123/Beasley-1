<?php

$post = get_queried_object();

$image = false;
$category = current( wp_get_post_terms( $post->ID, 'listing-category' ) );
if ( $category ) :
	$image_id = get_term_meta( $category->term_id, 'featured-image', true );
	if ( ! empty( $image_id ) ) :
		$image = wp_get_attachment_image_url( $image_id, 'full' );
	endif;
endif;

get_header();

the_post();

?><div class="container">
	<div>
		<?php the_post_thumbnail(); ?>
	</div>
	<div>
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $category->name ); ?>">
		<?php endif; ?>

		<h3><?php the_title(); ?></h3>

		<?php the_content(); ?>
	</div>

	<?php get_template_part( 'partials/directory-listing/related-items' ); ?>
</div><?php

get_footer();