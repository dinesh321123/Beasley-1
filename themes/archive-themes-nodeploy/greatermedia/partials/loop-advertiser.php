<?php

$advertiser_link = get_post_meta( get_the_ID(), 'advertiser_link', true );

?><article class="sponsor cf">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="sponsor__logo">
			<a href="<?php echo filter_var( $advertiser_link, FILTER_VALIDATE_URL ) ? esc_url( $advertiser_link ) : '#'; ?>">
				<img src="<?php echo esc_url( bbgi_get_image_url(get_post_thumbnail_id(), 400, 270 ) ); ?>">
			</a>
		</div>
	<?php else : ?>
		<div class="sponsor__name">
			<a href="<?php echo esc_url( $advertiser_link ); ?>">
				<?php the_title(); ?>
			</a>
		</div>
	<?php endif; ?>
</article>