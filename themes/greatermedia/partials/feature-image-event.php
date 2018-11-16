<?php if ( has_post_thumbnail() ) : ?>
	<div class="event__thumbnail">
		<img class="single__featured-img" src="<?php gm_post_thumbnail_url( 'gmr-event-thumbnail' ) ?>" alt="">
		<?php bbgi_the_image_attribution(); ?>
	</div>
<?php endif; ?>
