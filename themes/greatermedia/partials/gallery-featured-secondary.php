<a href="<?php the_permalink(); ?>">
	<div class="gallery__feature">
		<div class="gallery__featured--thumbnail">
			<?php if ( 'gmr_album' == get_post_type() ) { ?>
				<div class="gallery__grid--album"></div>
			<?php } ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class='thumbnail' style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary' ); ?>)'></div>
			<?php else: ?>
				<div class='thumbnail thumbnail-placeholder' style=''></div>
			<?php endif; ?>
		</div>
		<div class="gallery__featured--caption">
			<div class="inner-wrap">
				<h3 class="gallery__featured--title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h3>
			</div>
		</div>
	</div>

</a>