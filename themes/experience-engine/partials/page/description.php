<div class="description">
	<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
		<?php get_template_part( 'partials/featured-media' ); ?>
	<?php endif; ?>
	<?php the_content(); ?>
</div>
