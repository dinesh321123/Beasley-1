<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery__grid--featured' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<a href="<?php the_permalink(); ?>">
		<div class="gallery__grid--thumbnail">
			<?php if ( 'gmr_album' == get_post_type() ) { ?>
				<div class="gallery__grid--album"></div>
			<?php } ?>
			<div class="thumbnail" style="background-image: url(<?php gm_post_thumbnail_url( 'gmr-gallery-grid-thumb', null, true ); ?>)"></div>
		</div>
	</a>

		<div class="gallery__grid--meta">
			<?php echo get_the_category_list( ', ' ); ?>
			<h3 class="gallery__grid--title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
		</div>

</article>