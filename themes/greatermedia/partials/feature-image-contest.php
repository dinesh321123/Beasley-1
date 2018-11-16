<?php if ( has_post_thumbnail() ) : ?>
	<div class="contest__thumbnail">
		<img src="<?php gm_post_thumbnail_url( 'gmr-contest-thumbnail' ) ?>" alt="">
		<?php bbgi_the_image_attribution(); ?>
	</div>
<?php endif; ?>
