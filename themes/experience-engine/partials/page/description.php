<div class="description">
	<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
		<?php if ( ! ee_is_whiz() ) { get_template_part( 'partials/featured-media' ); } ?>
	<?php endif; ?>
	<?php the_content(); ?>
</div>
