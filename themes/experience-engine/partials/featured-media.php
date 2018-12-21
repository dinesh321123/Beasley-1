<?php if ( has_post_thumbnail() ) : ?>
	<div class="post-thumbnail">
		<?php ee_the_lazy_thumbnail(); ?>
		<?php bbgi_the_image_attribution(); ?>
	</div>
<?php endif; ?>