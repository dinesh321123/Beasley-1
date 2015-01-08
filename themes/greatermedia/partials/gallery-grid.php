<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery__grid--column' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<div class="gallery__grid--thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php if ( 'gmr_album' == get_post_type() ) { ?>
				<div class="gallery__grid--album"></div>
			<?php } ?>
			<?php the_post_thumbnail( 'gmr-gallery-grid-thumb' ); ?>
		</a>
	</div>

	<div class="gallery__grid--meta">
		<h3 class="gallery__grid--title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
	</div>

</article>