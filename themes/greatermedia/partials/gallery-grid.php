<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery__grid--column' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<a href="<?php the_permalink(); ?>">
		<div class="gallery__grid--thumbnail">
			<?php if ( 'gmr_album' == get_post_type() ) { ?>
				<div class="gallery__grid--album"></div>
			<?php } ?>
			<?php the_post_thumbnail( 'gmr-gallery-grid-thumb' ); ?>
		</div>

		<div class="gallery__grid--meta">
			<h3 class="gallery__grid--title">
				<?php the_title(); ?>
			</h3>
		</div>
	</a>

</article>
