<?php
/**
 * This runs a check to determine if the post has a thumbnail, and that it's not a gallery or video post format.
 */
if ( has_post_thumbnail() && ! bbgi_post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' ) && ! in_category( 'fanvote-of-the-day' )  ): ?>
	<div class="article__thumbnail">
		<img src="<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ) ?>" alt="">
		<?php bbgi_the_image_attribution(); ?>
	</div>
<?php endif; ?>
