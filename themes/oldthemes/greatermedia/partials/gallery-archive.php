<h2 class="content__heading" itemprop="headline">Latest Galleries</h2>
<?php
$featured = greatermedia_get_featured_gallery();
if ( $featured ) :
	$GLOBALS['post'] = $featured;
	setup_postdata( $featured );

	?><div class="gallery__featured">
		<div class="gallery__featured--primary gallery__grid-album">
			<?php get_template_part( 'partials/gallery-featured-primary' ); ?>
		</div>
	</div>

	<?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php if ( ! is_post_type_archive( 'gmr_gallery' ) ) : ?>
	<div class="gallery__grid gallery__grid-album">
		<?php get_template_part( 'partials/loop-album' ); ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>

<div class="gallery__grid gallery__grid-album">
	<?php get_template_part( 'partials/loop-gallery' ); ?>
</div>
