<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery__featured--item' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<div class="gallery__featured--thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'gmr-gallery-grid-secondary' ); ?>
		</a>
		<?php if ( 'gmr_album' == get_post_type() ) { ?>
			<div class="gallery__grid--album"></div>
		<?php } ?>
	</div>

	<div class="gallery__featured--caption">

		<h3 class="gallery__featured--title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

	</div>

</article>