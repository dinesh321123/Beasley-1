<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery__grid--column' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<a href="<?php the_permalink(); ?>">
		<div class="gallery__grid--thumbnail">
			<?php if ( 'gmr_album' == get_post_type() ) : ?>
				<div class="gallery__grid--album"></div>
			<?php endif; ?>

			<div class="thumbnail" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 404, 267 ); ?>)"></div>
		</div>
	</a>

	<div class="gallery__grid--meta">
		<?php if ( is_post_type_archive( 'gmr_gallery' ) ) : ?>
			<?php echo get_the_category_list( ', ' ); ?>
		<?php endif; ?>
		<h3 class="gallery__grid--title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
	</div>
</article>
