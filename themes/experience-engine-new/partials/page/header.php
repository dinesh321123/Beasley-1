<header class="post-info">
	<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
		<?php if ( ! ee_is_whiz() ) { get_template_part( 'partials/featured-media' ); } ?>
	<?php endif; ?>

	<h1><?php the_title(); ?></h1>
	<div class="post-meta">
		<?php get_template_part( 'partials/content/meta', 'page' ); ?>
	</div>
</header>
