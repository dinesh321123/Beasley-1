<?php
/**
 * This runs a check to determine if the post has a thumbnail, and that it's not a gallery or video post format.
 */
if ( has_post_thumbnail() && ! bbgi_post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ): ?>
	<div class="article__thumbnail">
		<img src="<?php gm_post_thumbnail_url( 'gm-article-thumbnail-wbt' ) ?>" alt="">
		<?php bbgi_the_image_attribution(); ?>
	</div>

	<?php if ( ! empty( get_post( get_post_thumbnail_id() )->post_excerpt ) ) : ?>
		<div class="article__thumbnail__caption">
			<?php echo get_post( get_post_thumbnail_id() )->post_excerpt; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
