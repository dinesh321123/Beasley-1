<div data-post-id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>

	<?php $mask_id = 'mask-' . uniqid(); ?>
</div>
