<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?>
<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
	<header class="post-info">
		<?php get_template_part( 'partials/featured-media', 'autoheight' ); ?>
	</header>
<?php endif; ?>

<div class="show-header">
	<?php get_template_part( 'partials/show/information' ); ?>
	<?php get_template_part( 'partials/show/navigation' ); ?>
</div>
